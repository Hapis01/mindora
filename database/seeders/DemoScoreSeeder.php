<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\AssessmentSession;
use App\Models\User;
use App\Services\PsychometricService;
use Illuminate\Database\Seeder;

class DemoScoreSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        if (!$user) return;

        // 1. Big Five Personality Session
        $personality = Assessment::where('code', 'big-five-v1')->first();
        if ($personality && !$this->hasCompletedSession($user, $personality)) {
            $session = AssessmentSession::create([
                'user_id' => $user->id,
                'assessment_id' => $personality->id,
                'version' => $personality->version,
                'status' => 'completed',
                'started_at' => now()->subMinutes(15),
                'completed_at' => now(),
                'total_time_seconds' => 900,
            ]);

            foreach ($personality->questions as $question) {
                $val = $question->is_reverse_scored ? 2 : 4;
                $opt = $question->options->where('score_value', $val)->first();
                Answer::create([
                    'session_id' => $session->id,
                    'question_id' => $question->id,
                    'selected_option_id' => $opt->id,
                    'answer_value' => (string) $val,
                    'is_correct' => false,
                    'response_time_ms' => 2200,
                ]);
            }

            $score = app(PsychometricService::class)->scoreSession($session);
            $this->command->info("Personality Score #{$score->id} created with ML Cluster: {$score->mlPrediction?->cluster_label}");
        }

        // 2. Cognitive Assessment Session
        $cognitive = Assessment::where('code', 'cognitive-battery-v1')->first();
        if ($cognitive && !$this->hasCompletedSession($user, $cognitive)) {
            $sessionCog = AssessmentSession::create([
                'user_id' => $user->id,
                'assessment_id' => $cognitive->id,
                'version' => $cognitive->version,
                'status' => 'completed',
                'started_at' => now()->subMinutes(20),
                'completed_at' => now()->subMinutes(5),
                'total_time_seconds' => 900,
            ]);

            foreach ($cognitive->questions as $idx => $question) {
                $correct = ($idx % 4 !== 0); // 75% accuracy
                $opt = $question->options->where('is_correct', $correct)->first() ?? $question->options->first();
                Answer::create([
                    'session_id' => $sessionCog->id,
                    'question_id' => $question->id,
                    'selected_option_id' => $opt->id,
                    'answer_value' => (string) $opt->score_value,
                    'is_correct' => $opt->is_correct,
                    'response_time_ms' => 3100,
                ]);
            }

            $scoreCog = app(PsychometricService::class)->scoreSession($sessionCog);
            $this->command->info("Cognitive Score #{$scoreCog->id} created with ML Cluster: {$scoreCog->mlPrediction?->cluster_label}");
        }
    }

    private function hasCompletedSession(User $user, Assessment $assessment): bool
    {
        return AssessmentSession::where('user_id', $user->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'completed')
            ->exists();
    }
}
