<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserProfile;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_profile_displays_masked_data_by_default(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);

        // Verify default masked strings
        $response->assertSee('Ringkasan Informasi Pribadi (Masked)');
        $response->assertSee('---');
        $response->assertSee($user->masked_email); // Masked email
        $response->assertDontSee('Jl. Sudirman Kav. 21'); // Sensitive address must NOT be visible
        $response->assertDontSee('081234567890'); // Sensitive phone must NOT be visible
    }

    public function test_profile_cannot_be_unlocked_with_wrong_password(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($user)->post('/profile/unlock', [
            'password' => 'WrongPassword123!',
            'date_of_birth' => '1998-05-20',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertFalse(session()->has('sensitive_data_unlocked_at'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'failed_unlock_profile',
            'actor_id' => $user->id,
        ]);
    }

    public function test_profile_cannot_be_unlocked_with_wrong_date_of_birth(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($user)->post('/profile/unlock', [
            'password' => 'UserPassword123!',
            'date_of_birth' => '1990-01-01', // Incorrect DOB factor
        ]);

        $response->assertSessionHasErrors(['date_of_birth']);
        $this->assertFalse(session()->has('sensitive_data_unlocked_at'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'failed_unlock_profile',
            'actor_id' => $user->id,
        ]);
    }

    public function test_profile_can_be_unlocked_with_correct_two_factor_credentials(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($user)->post('/profile/unlock', [
            'password' => 'UserPassword123!',
            'date_of_birth' => '1998-05-20',
        ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');
        $this->assertTrue(session()->has('sensitive_data_unlocked_at'));

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'unlock_profile',
            'actor_id' => $user->id,
        ]);

        // Follow redirect and verify decrypted data is revealed
        $profileResponse = $this->actingAs($user)->get('/profile');
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Data Profil Sensitif Terbuka');
        $profileResponse->assertSee('Muhammad Hafiz');
        $profileResponse->assertSee('081234567890');
        $profileResponse->assertSee('Jl. Sudirman Kav. 21, Jakarta Selatan');
    }

    public function test_user_can_manually_lock_profile_again(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        // Simulate unlocked session
        session([
            'sensitive_data_unlocked_at' => now()->timestamp,
            'sensitive_data_expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        $response = $this->actingAs($user)->post('/profile/lock');
        $response->assertRedirect(route('profile.show'));
        $this->assertFalse(session()->has('sensitive_data_unlocked_at'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'lock_profile',
            'actor_id' => $user->id,
        ]);
    }

    public function test_user_can_update_profile_when_unlocked(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        // Simulate unlocked session
        session([
            'sensitive_data_unlocked_at' => now()->timestamp,
            'sensitive_data_expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Muhammad Hafiz Updated',
            'date_of_birth' => '1997-12-15',
            'phone' => '089988776655',
            'address' => 'Gedung Cyber 2, Kuningan Barat',
            'gender' => 'male',
        ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');

        // Check user name updated
        $this->assertEquals('Muhammad Hafiz Updated', $user->fresh()->name);

        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'profile_update',
            'actor_id' => $user->id,
        ]);
    }
}
