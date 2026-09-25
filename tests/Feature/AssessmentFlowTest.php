<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentSession;
use App\Models\QuestionOption;
use App\Models\User;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleAndPermissionSeeder::class, AssessmentSeeder::class]);
    }

    public function test_guests_cannot_access_assessments(): void
    {
        $response = $this->get('/assessments');
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_assessment_catalog(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($user)->get('/assessments');
        $response->assertStatus(200);
        $response->assertSee('Tes Kemampuan Kognitif');
        $response->assertSee('Tes Kepribadian Big Five');
    }

    public function test_user_can_view_instructions_and_start_assessment(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $assessment = Assessment::where('code', 'cognitive-battery-v1')->first();

        $instructionResponse = $this->actingAs($user)->get(route('assessments.instructions', $assessment));
        $instructionResponse->assertStatus(200);
        $instructionResponse->assertSee('Petunjuk Pengerjaan');

        $startResponse = $this->actingAs($user)->post(route('assessments.start', $assessment));
        $startResponse->assertRedirect();

        $session = AssessmentSession::where('user_id', $user->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'in_progress')
            ->first();

        $this->assertNotNull($session);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'start_assessment',
            'actor_id' => $user->id,
        ]);
    }

    public function test_user_can_save_answer_with_response_time(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $assessment = Assessment::where('code', 'cognitive-battery-v1')->first();
        $question = $assessment->questions->first();
        $option = $question->options->first();

        $session = AssessmentSession::create([
            'user_id' => $user->id,
            'assessment_id' => $assessment->id,
            'version' => 'v1.0',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('assessments.answer', $session), [
            'question_id' => $question->id,
            'selected_option_id' => $option->id,
            'response_time_ms' => 4250,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('answers', [
            'session_id' => $session->id,
            'question_id' => $question->id,
            'selected_option_id' => $option->id,
            'response_time_ms' => 4250,
        ]);
    }

    public function test_user_can_finish_assessment_and_view_result(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $assessment = Assessment::where('code', 'cognitive-battery-v1')->first();

        $session = AssessmentSession::create([
            'user_id' => $user->id,
            'assessment_id' => $assessment->id,
            'version' => 'v1.0',
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(5),
        ]);

        // Submit answers
        foreach ($assessment->questions as $q) {
            $opt = $q->options->first();
            Answer::create([
                'session_id' => $session->id,
                'question_id' => $q->id,
                'selected_option_id' => $opt->id,
                'answer_value' => (string) $opt->score_value,
                'is_correct' => $opt->is_correct,
                'response_time_ms' => 3000,
            ]);
        }

        $finishResponse = $this->actingAs($user)->post(route('assessments.finish', $session));
        $finishResponse->assertRedirect();

        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertNotNull($session->score);

        // View result
        $resultResponse = $this->actingAs($user)->get(route('assessments.result', $session->score));
        $resultResponse->assertStatus(200);
        $resultResponse->assertSee('Skor Asesmen');
        $resultResponse->assertSee('Profil Kecenderungan Utama');
    }

    public function test_user_cannot_view_another_users_assessment_result(): void
    {
        $userA = User::where('email', 'user@Mindora.test')->first();
        $userB = User::factory()->create();
        $assessment = Assessment::where('code', 'cognitive-battery-v1')->first();

        $session = AssessmentSession::create([
            'user_id' => $userB->id,
            'assessment_id' => $assessment->id,
            'version' => 'v1.0',
            'status' => 'completed',
            'started_at' => now()->subMinutes(10),
            'completed_at' => now(),
        ]);

        $score = AssessmentScore::create([
            'session_id' => $session->id,
            'user_id' => $userB->id,
            'assessment_type' => 'cognitive',
            'overall_score' => 85.0,
            'archetype_label' => 'Analytical Thinker',
            'summary_text' => 'Ringkasan tes user B',
        ]);

        // User A attempts to view User B's result -> 403 Forbidden
        $response = $this->actingAs($userA)->get(route('assessments.result', $score));
        $response->assertStatus(403);

        // Admin attempts to view User B's result -> 200 OK
        $admin = User::where('email', 'admin@Mindora.test')->first();
        $adminResponse = $this->actingAs($admin)->get(route('assessments.result', $score));
        $adminResponse->assertStatus(200);
    }
}
