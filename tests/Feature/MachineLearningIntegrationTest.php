<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentSession;
use App\Models\MlFeature;
use App\Models\MlModel;
use App\Models\MlPrediction;
use App\Models\User;
use App\Services\MachineLearningService;
use App\Services\PsychometricService;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineLearningIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleAndPermissionSeeder::class, AssessmentSeeder::class]);
    }

    public function test_ml_service_health_check(): void
    {
        $mlService = app(MachineLearningService::class);
        $health = $mlService->checkHealth();

        $this->assertIsArray($health);
        $this->assertEquals('healthy', $health['status'] ?? null);
        $this->assertTrue($health['models_loaded'] ?? false);
        $this->assertTrue($health['shap_available'] ?? false);
    }

    public function test_feature_vector_extraction_and_zero_pii_anonymization(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $mlService = app(MachineLearningService::class);

        $anonId = $mlService->generateAnonymousUserId($user->id);
        $this->assertStringStartsWith('ANON-', $anonId);
        $this->assertEquals(17, strlen($anonId)); // 'ANON-' + 12 hex chars
        $this->assertStringNotContainsString($user->email, $anonId);
        $this->assertNotEquals("ANON-{$user->id}", $anonId);
    }

    public function test_end_to_end_assessment_ml_analysis_and_persistence(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $personality = Assessment::where('code', 'big-five-v1')->first();

        $session = AssessmentSession::create([
            'user_id' => $user->id,
            'assessment_id' => $personality->id,
            'version' => 'v1.0',
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(10),
        ]);

        foreach ($personality->questions as $question) {
            $val = $question->is_reverse_scored ? 1 : 5;
            $option = $question->options->where('score_value', $val)->first();

            Answer::create([
                'session_id' => $session->id,
                'question_id' => $question->id,
                'selected_option_id' => $option->id,
                'answer_value' => (string) $val,
                'is_correct' => false,
                'response_time_ms' => 1900,
            ]);
        }

        $psychometricService = app(PsychometricService::class);
        $score = $psychometricService->scoreSession($session);

        $this->assertInstanceOf(AssessmentScore::class, $score);

        // Verify ML tables populated
        $this->assertDatabaseHas('ml_features', [
            'assessment_score_id' => $score->id,
        ]);

        $this->assertDatabaseHas('ml_predictions', [
            'assessment_score_id' => $score->id,
        ]);

        $prediction = MlPrediction::where('assessment_score_id', $score->id)->first();
        $this->assertNotNull($prediction);
        $this->assertNotNull($prediction->cluster_id);
        $this->assertNotNull($prediction->cluster_label);
        $this->assertNotNull($prediction->predicted_category);
        $this->assertGreaterThan(0, $prediction->confidence);
        $this->assertNotEmpty($prediction->shap_values);
        $this->assertIsArray($prediction->shap_values);

        // Verify model registry record
        $model = MlModel::where('name', 'psychometric-ensemble-v1')->first();
        $this->assertNotNull($model);
        $this->assertEquals('active', $model->status);
    }

    public function test_result_page_displays_ml_archetype_and_shap_explainability(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $cognitive = Assessment::where('code', 'cognitive-battery-v1')->first();

        $session = AssessmentSession::create([
            'user_id' => $user->id,
            'assessment_id' => $cognitive->id,
            'version' => 'v1.0',
            'status' => 'completed',
            'started_at' => now()->subMinutes(12),
            'completed_at' => now(),
            'total_time_seconds' => 720,
        ]);

        foreach ($cognitive->questions as $question) {
            $option = $question->options->where('is_correct', true)->first();
            Answer::create([
                'session_id' => $session->id,
                'question_id' => $question->id,
                'selected_option_id' => $option->id,
                'answer_value' => (string) $option->score_value,
                'is_correct' => true,
                'response_time_ms' => 2800,
            ]);
        }

        $psychometricService = app(PsychometricService::class);
        $score = $psychometricService->scoreSession($session);

        $response = $this->actingAs($user)->get(route('assessments.result', $score));

        $response->assertStatus(200);
        $response->assertSee('Machine Learning & Explainable AI (SHAP)');
        $response->assertSee('K-Means Behavioral Cluster');
        $response->assertSee('Random Forest Profile Category');
        $response->assertSee('SHAP Feature Importance');
        $response->assertSee('TreeSHAP');
        $response->assertSee('Pemberitahuan Model Demonstrasi & Non-Klinis');
    }
}
