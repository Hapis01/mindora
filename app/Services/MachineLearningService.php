<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\AssessmentScore;
use App\Models\CognitiveScore;
use App\Models\MlFeature;
use App\Models\MlModel;
use App\Models\MlPrediction;
use App\Models\PersonalityScore;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MachineLearningService
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ml.url', 'http://127.0.0.1:8001'), '/');
        $this->token = config('services.ml.token');
    }

    /**
     * Check if the ML microservice is online and healthy.
     */
    public function checkHealth(): array
    {
        try {
            $response = Http::timeout(3)->get("{$this->baseUrl}/health");
            if ($response->successful()) {
                return $response->json();
            }
            return ['status' => 'unreachable', 'code' => $response->status()];
        } catch (Exception $e) {
            Log::warning("ML Service health check failed: {$e->getMessage()}");
            return ['status' => 'offline', 'error' => $e->getMessage()];
        }
    }

    /**
     * Analyze an assessment score through the ML microservice and persist results.
     */
    public function analyzeAndStore(AssessmentScore $score): ?MlPrediction
    {
        try {
            // 1. Build pseudo-anonymized identifier (Zero PII sent to ML)
            $anonUserId = $this->generateAnonymousUserId($score->user_id);

            // 2. Extract normalized 10-dimensional feature vector
            $features = $this->extractFeatureVector($score);

            // 3. Ensure active model is registered in ml_models table
            $mlModel = $this->resolveActiveModel();

            // 4. Call FastAPI microservice /analyze endpoint
            $response = Http::timeout(5)
                ->withToken($this->token)
                ->post("{$this->baseUrl}/analyze", [
                    'anonymous_user_id' => $anonUserId,
                    'features' => $features,
                ]);

            if (!$response->successful()) {
                Log::error("ML Service analysis request failed with status: {$response->status()}", [
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            // 5. Persist Anonymized Features
            MlFeature::updateOrCreate(
                ['assessment_score_id' => $score->id],
                [
                    'anonymous_user_id' => $anonUserId,
                    'feature_vector' => $features,
                ]
            );

            // 6. Persist ML Predictions & SHAP Explainability
            $prediction = MlPrediction::updateOrCreate(
                ['assessment_score_id' => $score->id],
                [
                    'model_id' => $mlModel?->id,
                    'cluster_id' => $data['cluster']['cluster_id'] ?? null,
                    'cluster_label' => $data['cluster']['cluster_label'] ?? null,
                    'predicted_category' => $data['prediction']['predicted_category'] ?? null,
                    'shap_values' => $data['explainability']['top_features'] ?? null,
                    'confidence' => $data['prediction']['confidence'] ?? null,
                ]
            );

            Log::info("ML Analysis successfully saved for score #{$score->id} (Cluster: {$prediction->cluster_label}, Category: {$prediction->predicted_category})");

            return $prediction;

        } catch (Exception $e) {
            Log::error("Exception in MachineLearningService::analyzeAndStore: {$e->getMessage()}", [
                'score_id' => $score->id,
            ]);
            return null;
        }
    }

    /**
     * Generate pseudo-anonymous identifier (strictly zero PII).
     */
    public function generateAnonymousUserId(int $userId): string
    {
        $salt = config('app.key') ?: 'Mindora-salt-2026';
        $hash = strtoupper(substr(hash_hmac('sha256', "user-{$userId}", $salt), 0, 12));
        return "ANON-{$hash}";
    }

    /**
     * Extract 10-dimensional feature vector combining cognitive, personality, and session telemetry.
     */
    public function extractFeatureVector(AssessmentScore $score): array
    {
        $score->loadMissing(['cognitiveScore', 'personalityScore', 'session.answers']);

        // Default neutral baseline values (50.0)
        $logical = 50.0;
        $numerical = 50.0;
        $verbal = 50.0;
        $openness = 50.0;
        $conscientiousness = 50.0;
        $extraversion = 50.0;
        $agreeableness = 50.0;
        $neuroticism = 50.0;
        $consistency = 95.0;

        if ($score->cognitiveScore) {
            $logical = (float) $score->cognitiveScore->logical_score;
            $numerical = (float) $score->cognitiveScore->numerical_score;
            $verbal = (float) $score->cognitiveScore->verbal_score;
        } else {
            // Check for previous cognitive score for this user
            $prevCog = CognitiveScore::whereHas('assessmentScore', fn($q) => $q->where('user_id', $score->user_id))
                ->latest()
                ->first();
            if ($prevCog) {
                $logical = (float) $prevCog->logical_score;
                $numerical = (float) $prevCog->numerical_score;
                $verbal = (float) $prevCog->verbal_score;
            }
        }

        if ($score->personalityScore) {
            $openness = (float) $score->personalityScore->openness;
            $conscientiousness = (float) $score->personalityScore->conscientiousness;
            $extraversion = (float) $score->personalityScore->extraversion;
            $agreeableness = (float) $score->personalityScore->agreeableness;
            $neuroticism = (float) $score->personalityScore->neuroticism;
            $consistency = (float) $score->personalityScore->consistency_score;
        } else {
            // Check for previous personality score for this user
            $prevPers = PersonalityScore::whereHas('assessmentScore', fn($q) => $q->where('user_id', $score->user_id))
                ->latest()
                ->first();
            if ($prevPers) {
                $openness = (float) $prevPers->openness;
                $conscientiousness = (float) $prevPers->conscientiousness;
                $extraversion = (float) $prevPers->extraversion;
                $agreeableness = (float) $prevPers->agreeableness;
                $neuroticism = (float) $prevPers->neuroticism;
                $consistency = (float) $prevPers->consistency_score;
            }
        }

        // Telemetry: average response time in ms
        $avgResponseTime = 2500.0;
        if ($score->session && $score->session->answers) {
            $times = $score->session->answers->pluck('response_time_ms')->filter(fn($t) => $t && $t > 0);
            if ($times->isNotEmpty()) {
                $avgResponseTime = round($times->average(), 1);
            }
        }

        return [
            'logical' => $logical,
            'numerical' => $numerical,
            'verbal' => $verbal,
            'openness' => $openness,
            'conscientiousness' => $conscientiousness,
            'extraversion' => $extraversion,
            'agreeableness' => $agreeableness,
            'neuroticism' => $neuroticism,
            'consistency' => $consistency,
            'avg_response_time_ms' => $avgResponseTime,
        ];
    }

    /**
     * Resolve or register the active ML model metadata in ml_models table.
     */
    protected function resolveActiveModel(): ?MlModel
    {
        return MlModel::firstOrCreate(
            [
                'name' => 'psychometric-ensemble-v1',
                'version' => 'v1.0.0',
            ],
            [
                'algorithm' => 'K-Means (k=4) + Random Forest (n=120) + TreeSHAP',
                'training_date' => now(),
                'dataset_version' => 'normative-synthetic-v1.0',
                'metrics' => [
                    'accuracy' => 0.9175,
                    'silhouette_score' => 0.1696,
                    'clusters_count' => 4,
                ],
                'artifact_path' => 'python-ml/artifacts',
                'status' => 'active',
            ]
        );
    }
}
