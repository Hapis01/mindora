<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AuditLog;
use App\Models\MlModel;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityAndManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleAndPermissionSeeder::class, AssessmentSeeder::class]);
    }

    public function test_guests_and_standard_users_cannot_access_admin_portal(): void
    {
        // 1. Guest redirected
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.users'))->assertRedirect(route('login'));
        $this->get(route('admin.questions'))->assertRedirect(route('login'));
        $this->get(route('admin.models'))->assertRedirect(route('login'));
        $this->get(route('admin.audit-logs'))->assertRedirect(route('login'));

        // 2. Standard User blocked (403)
        $standardUser = User::where('email', 'user@Mindora.test')->first();
        $this->actingAs($standardUser)->get(route('admin.dashboard'))->assertStatus(403);
        $this->actingAs($standardUser)->get(route('admin.users'))->assertStatus(403);
        $this->actingAs($standardUser)->get(route('admin.questions'))->assertStatus(403);
        $this->actingAs($standardUser)->get(route('admin.models'))->assertStatus(403);
        $this->actingAs($standardUser)->get(route('admin.audit-logs'))->assertStatus(403);
    }

    public function test_admin_and_super_admin_can_access_all_admin_sections(): void
    {
        $admin = User::where('email', 'admin@Mindora.test')->first();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.users'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.questions'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.models'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.audit-logs'))->assertStatus(200);
    }

    public function test_admin_user_table_displays_masked_pii_by_default(): void
    {
        $admin = User::where('email', 'admin@Mindora.test')->first();

        $response = $this->actingAs($admin)->get(route('admin.users'));

        $response->assertStatus(200);
        $response->assertSee('---');
        $response->assertSee('Buka Data');
        $response->assertSee('u****@Mindora.test');
    }

    public function test_admin_unlock_fails_with_invalid_password_and_logs_failed_attempt(): void
    {
        $admin = User::where('email', 'admin@Mindora.test')->first();
        $targetUser = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($admin)->postJson(route('admin.users.unlock', $targetUser), [
            'password' => 'WrongAdminPassword123!',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_failed_unlock_sensitive_data',
            'actor_id' => $admin->id,
            'target_id' => $targetUser->id,
        ]);
    }

    public function test_admin_unlock_succeeds_with_correct_password_and_logs_success(): void
    {
        $admin = User::where('email', 'admin@Mindora.test')->first();
        $targetUser = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($admin)->postJson(route('admin.users.unlock', $targetUser), [
            'password' => 'AdminPassword123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'user_id',
            'expires_at',
            'decrypted' => [
                'name',
                'dob',
                'phone',
                'address',
            ],
        ]);

        $this->assertEquals('success', $response->json('status'));

        // Verify audit log record
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_unlock_sensitive_data',
            'actor_id' => $admin->id,
            'target_id' => $targetUser->id,
        ]);
    }

    public function test_admin_can_update_question_configurations(): void
    {
        $admin = User::where('email', 'admin@Mindora.test')->first();
        $question = Question::first();

        $response = $this->actingAs($admin)->put(route('admin.questions.update', $question), [
            'weight' => 2,
            'is_reverse_scored' => 1,
            'status' => 'draft',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(2, $question->fresh()->weight);
        $this->assertTrue((bool)$question->fresh()->is_reverse_scored);
        $this->assertEquals('draft', $question->fresh()->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_update_question',
            'actor_id' => $admin->id,
            'target_id' => $question->id,
        ]);
    }

    public function test_admin_can_filter_audit_logs(): void
    {
        $admin = User::where('email', 'admin@Mindora.test')->first();

        // Create sample audit log
        AuditLog::create([
            'actor_id' => $admin->id,
            'actor_type' => get_class($admin),
            'action' => 'custom_test_action',
            'ip_address' => '192.168.1.100',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.audit-logs', ['action' => 'custom_test_action']));

        $response->assertStatus(200);
        $response->assertSee('custom_test_action');
    }

    public function test_admin_can_view_model_management_and_ml_diagnostics(): void
    {
        $admin = User::where('email', 'admin@Mindora.test')->first();

        // Create sample ML Model
        MlModel::create([
            'name' => 'test-ensemble-v1',
            'version' => 'v1.0.0',
            'algorithm' => 'Random Forest + K-Means',
            'status' => 'active',
            'metrics' => ['accuracy' => 0.92, 'silhouette_score' => 0.17],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.models'));

        $response->assertStatus(200);
        $response->assertSee('test-ensemble-v1');
        $response->assertSee('Random Forest');
        $response->assertSee('Manajemen Model');
    }

    public function test_admin_can_toggle_model_status(): void
    {
        $admin = User::where('email', 'admin@Mindora.test')->first();

        $model = MlModel::create([
            'name' => 'toggle-test-model',
            'version' => 'v1.0.0',
            'algorithm' => 'K-Means',
            'status' => 'active',
            'metrics' => ['clusters_count' => 4],
        ]);

        $response = $this->actingAs($admin)->put(route('admin.models.toggle', $model));

        $response->assertSessionHasNoErrors();
        $this->assertEquals('inactive', $model->fresh()->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_toggle_ml_model_status',
            'actor_id' => $admin->id,
            'target_id' => $model->id,
        ]);
    }
}
