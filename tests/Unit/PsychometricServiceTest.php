<?php

namespace Tests\Unit;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentScore;
use App\Models\AssessmentSession;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use App\Services\PsychometricService;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PsychometricServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PsychometricService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleAndPermissionSeeder::class, AssessmentSeeder::class]);
        $this->service = app(PsychometricService::class);
    }

    public function test_cognitive_assessment_scoring(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $cognitive = Assessment::where('code', 'cognitive-battery-v1')->first();

        $session = AssessmentSession::create([
            'user_id' => $user->id,
            'assessment_id' => $cognitive->id,
            'version' => 'v1.0',
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(10),
        ]);

        // Answer all questions correctly
        foreach ($cognitive->questions as $question) {
            $correctOption = $question->options->where('is_correct', true)->first();
            Answer::create([
                'session_id' => $session->id,
                'question_id' => $question->id,
                'selected_option_id' => $correctOption->id,
                'answer_value' => (string) $correctOption->score_value,
                'is_correct' => true,
                'response_time_ms' => 3500,
            ]);
        }

        $score = $this->service->scoreSession($session);

        $this->assertInstanceOf(AssessmentScore::class, $score);
        $this->assertEquals('cognitive', $score->assessment_type);
        $this->assertEquals(100.0, $score->overall_score);
        $this->assertNotNull($score->cognitiveScore);
        $this->assertEquals(100.0, $score->cognitiveScore->logical_score);
        $this->assertEquals(100.0, $score->cognitiveScore->numerical_score);
        $this->assertEquals(100.0, $score->cognitiveScore->verbal_score);
        $this->assertNotEmpty($score->recommendations);
    }

    public function test_personality_assessment_scoring_with_reverse_scoring(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $personality = Assessment::where('code', 'big-five-v1')->first();

        $session = AssessmentSession::create([
            'user_id' => $user->id,
            'assessment_id' => $personality->id,
            'version' => 'v1.0',
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(8),
        ]);

        // Answer high on all items
        // For normal questions: give 5 (raw 5)
        // For reverse questions: give 1 (which reverses to 6 - 1 = 5)
        foreach ($personality->questions as $question) {
            $chosenVal = $question->is_reverse_scored ? 1 : 5;
            $option = $question->options->where('score_value', $chosenVal)->first();

            Answer::create([
                'session_id' => $session->id,
                'question_id' => $question->id,
                'selected_option_id' => $option->id,
                'answer_value' => (string) $chosenVal,
                'is_correct' => false,
                'response_time_ms' => 2100,
            ]);
        }

        $score = $this->service->scoreSession($session);

        $this->assertInstanceOf(AssessmentScore::class, $score);
        $this->assertEquals('personality', $score->assessment_type);
        $this->assertNotNull($score->personalityScore);

        // Since normal was 5 and reverse was 1 (reversed to 5), traits should all be 100%
        $this->assertEquals(100.0, $score->personalityScore->openness);
        $this->assertEquals(100.0, $score->personalityScore->conscientiousness);
        $this->assertEquals(100.0, $score->personalityScore->extraversion);
        $this->assertEquals(100.0, $score->personalityScore->agreeableness);
        $this->assertEquals(100.0, $score->personalityScore->neuroticism);
        $this->assertGreaterThanOrEqual(90.0, $score->personalityScore->consistency_score);
        $this->assertNotEmpty($score->recommendations);
    }
}
