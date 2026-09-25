<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\AssessmentScore;
use App\Models\AssessmentSession;
use App\Models\CognitiveScore;
use App\Models\PersonalityScore;
use App\Models\Question;
use App\Models\Recommendation;
use Illuminate\Support\Collection;

class PsychometricService
{
    protected MachineLearningService $mlService;

    public function __construct(MachineLearningService $mlService)
    {
        $this->mlService = $mlService;
    }

    /**
     * Score a completed assessment session and generate psychometric profile.
     */
    public function scoreSession(AssessmentSession $session): AssessmentScore
    {
        $session->loadMissing(['assessment.category', 'answers.question.options', 'answers.selectedOption']);

        $score = $session->assessment->isCognitive()
            ? $this->scoreCognitiveSession($session)
            : $this->scorePersonalitySession($session);

        // Enrich with Machine Learning Archetype & SHAP Explainability
        $this->mlService->analyzeAndStore($score);

        return $score;
    }

    /**
     * Score Cognitive Ability Assessment (Logical, Numerical, Verbal Reasoning).
     */
    protected function scoreCognitiveSession(AssessmentSession $session): AssessmentScore
    {
        $answers = $session->answers;

        $dimensions = ['logical', 'numerical', 'verbal'];
        $dimStats = [];

        foreach ($dimensions as $dim) {
            $dimAnswers = $answers->filter(fn(Answer $a) => strtolower($a->question->dimension) === $dim);
            $total = $dimAnswers->count();
            $correct = $dimAnswers->filter(fn(Answer $a) => $a->selectedOption && $a->selectedOption->is_correct)->count();
            $score = $total > 0 ? round(($correct / $total) * 100, 2) : 0.0;

            $dimStats[$dim] = [
                'total' => $total,
                'correct' => $correct,
                'score' => $score,
            ];
        }

        // Overall Cognitive Ability Score (0-100 scale)
        $scoresOnly = array_column($dimStats, 'score');
        $overallScore = count($scoresOnly) > 0 ? round(array_sum($scoresOnly) / count($scoresOnly), 2) : 0.0;

        // Estimated Percentile Rank based on standard normative distribution approximation
        $percentile = round(min(99.0, max(1.0, $overallScore * 0.95 + 4)), 1);

        // Determine Archetype
        $archetype = $this->determineCognitiveArchetype($dimStats);

        // Formulate Non-Clinical Strengths & Growth Areas
        $strengths = [];
        $developmentAreas = [];

        if ($dimStats['logical']['score'] >= 70) {
            $strengths[] = 'Kemampuan penalaran logis dan deduktif yang kuat dalam memecahkan masalah struktural.';
        } else {
            $developmentAreas[] = 'Latihan mengenali pola hubungan logis dan silogisme kompleks.';
        }

        if ($dimStats['numerical']['score'] >= 70) {
            $strengths[] = 'Ketajaman analisis kuantitatif dan interpretasi data numerik yang akurat.';
        } else {
            $developmentAreas[] = 'Peningkatan pemahaman grafik data, deret angka, dan penalaran aritmatika praktis.';
        }

        if ($dimStats['verbal']['score'] >= 70) {
            $strengths[] = 'Kemampuan pemahaman bahasa, analogi konsep, dan sintesis informasi verbal yang tajam.';
        } else {
            $developmentAreas[] = 'Pengayaan perbendaharaan kosa kata kontekstual dan pemahaman bacaan kritis.';
        }

        if (empty($strengths)) {
            $strengths[] = 'Keseimbangan pemrosesan informasi umum dan potensi adaptasi kognitif yang merata.';
        }

        if (empty($developmentAreas)) {
            $developmentAreas[] = 'Eksplorasi persoalan analitis tingkat lanjut dengan batas waktu yang lebih ketat.';
        }

        $summaryText = "Berdasarkan hasil asesmen kognitif mandiri, Anda menunjukkan kecenderungan skor kemampuan kognitif sebesar {$overallScore}%. Pola performa menunjukkan performa paling menonjol pada domain " .
            $this->getTopDimensionName($dimStats) . ". Hasil ini menggambarkan profil kapasitas pemrosesan informasi pada saat tes dan bukan merupakan pengukuran diagnosis klinis.";

        // Create Master Assessment Score
        $assessmentScore = AssessmentScore::create([
            'session_id' => $session->id,
            'user_id' => $session->user_id,
            'assessment_type' => 'cognitive',
            'overall_score' => $overallScore,
            'archetype_label' => $archetype,
            'summary_text' => $summaryText,
            'strengths' => $strengths,
            'development_areas' => $developmentAreas,
        ]);

        // Create Cognitive Sub-Scores
        CognitiveScore::create([
            'assessment_score_id' => $assessmentScore->id,
            'logical_score' => $dimStats['logical']['score'],
            'numerical_score' => $dimStats['numerical']['score'],
            'verbal_score' => $dimStats['verbal']['score'],
            'overall_cognitive_score' => $overallScore,
            'percentile' => $percentile,
        ]);

        // Generate Career Exploration Recommendations
        $this->generateCognitiveRecommendations($assessmentScore, $dimStats);

        return $assessmentScore;
    }

    /**
     * Score Big Five Personality Assessment (Openness, Conscientiousness, Extraversion, Agreeableness, Neuroticism).
     */
    protected function scorePersonalitySession(AssessmentSession $session): AssessmentScore
    {
        $answers = $session->answers;

        $dimensions = ['openness', 'conscientiousness', 'extraversion', 'agreeableness', 'neuroticism'];
        $traitScores = [];
        $responseTimes = [];

        foreach ($dimensions as $dim) {
            $dimAnswers = $answers->filter(fn(Answer $a) => strtolower($a->question->dimension) === $dim);
            $totalQuestions = $dimAnswers->count();

            if ($totalQuestions === 0) {
                $traitScores[$dim] = 50.0;
                continue;
            }

            $adjustedSum = 0;
            foreach ($dimAnswers as $ans) {
                $rawValue = (int) ($ans->answer_value ?? ($ans->selectedOption ? $ans->selectedOption->score_value : 3));
                if ($ans->question->is_reverse_scored) {
                    $adjusted = 6 - $rawValue; // Reverse Likert (1->5, 2->4, 3->3, 4->2, 5->1)
                } else {
                    $adjusted = $rawValue;
                }
                $adjustedSum += $adjusted;

                if ($ans->response_time_ms) {
                    $responseTimes[] = $ans->response_time_ms;
                }
            }

            // Normalize Likert 1-5 to 0-100% scale
            // min possible = totalQuestions * 1, max possible = totalQuestions * 5
            $minPossible = $totalQuestions * 1;
            $maxPossible = $totalQuestions * 5;
            $percentage = round((($adjustedSum - $minPossible) / ($maxPossible - $minPossible)) * 100, 2);

            $traitScores[$dim] = max(0.0, min(100.0, $percentage));
        }

        // Consistency Score Calculation (0-100%)
        $consistencyScore = $this->calculateConsistencyScore($answers);

        // Overall Profile Score (Mean of positive traits adjustment)
        $overallScore = round(($traitScores['openness'] + $traitScores['conscientiousness'] + $traitScores['extraversion'] + $traitScores['agreeableness'] + (100 - $traitScores['neuroticism'])) / 5, 2);

        // Determine Archetype
        $archetype = $this->determinePersonalityArchetype($traitScores);

        // Strengths & Growth Areas
        $strengths = [];
        $developmentAreas = [];

        if ($traitScores['openness'] >= 65) {
            $strengths[] = 'Keterbukaan tinggi terhadap konsep baru, rasa ingin tahu intelektual, dan daya imajinasi kreatif.';
        } else {
            $developmentAreas[] = 'Membuka ruang eksplorasi ide yang berada di luar rutinitas atau kebiasaan konvensional.';
        }

        if ($traitScores['conscientiousness'] >= 65) {
            $strengths[] = 'Ketekunan, kedisiplinan organisasi tugas yang tinggi, dan keandalan dalam menepati komitmen.';
        } else {
            $developmentAreas[] = 'Mengembangkan kebiasaan terencana dan manajemen tenggat waktu yang lebih terstruktur.';
        }

        if ($traitScores['extraversion'] >= 65) {
            $strengths[] = 'Energi sosial dinamis, kemudahan berkomunikasi, dan antusiasme dalam membangun jejaring tim.';
        } else {
            $developmentAreas[] = 'Meningkatkan partisipasi aktif dalam forum kolaborasi atau diskusi kelompok publik.';
        }

        if ($traitScores['agreeableness'] >= 65) {
            $strengths[] = 'Tingkat empati yang hangat, kemauan bekerja sama, dan orientasi menjaga keharmonisan kelompok.';
        } else {
            $developmentAreas[] = 'Melatih ketegasan (assertiveness) dalam menyampaikan opini kritis yang konstruktif.';
        }

        if ($traitScores['neuroticism'] <= 40) {
            $strengths[] = 'Stabilitas emosional yang mantap, tenang saat menghadapi tekanan beban kerja tinggi.';
        } else {
            $developmentAreas[] = 'Menerapkan teknik pengelolaan regulasi stres saat menghadapi ketidakpastian atau beban tinggi.';
        }

        $summaryText = "Berdasarkan hasil asesmen Big Five Personality mandiri, Anda menunjukkan profil kecenderungan " .
            "{$archetype}. Dimensi kepribadian yang paling menonjol pada diri Anda adalah " .
            $this->getTopPersonalityTraitName($traitScores) . ". Profil ini mencerminkan preferensi perilaku tipikal dan bukan diagnosis klinis gangguan kepribadian.";

        // Create Master Score Record
        $assessmentScore = AssessmentScore::create([
            'session_id' => $session->id,
            'user_id' => $session->user_id,
            'assessment_type' => 'personality',
            'overall_score' => $overallScore,
            'archetype_label' => $archetype,
            'summary_text' => $summaryText,
            'strengths' => array_slice($strengths, 0, 3),
            'development_areas' => array_slice($developmentAreas, 0, 3),
        ]);

        // Create Personality Sub-Scores
        PersonalityScore::create([
            'assessment_score_id' => $assessmentScore->id,
            'openness' => $traitScores['openness'],
            'conscientiousness' => $traitScores['conscientiousness'],
            'extraversion' => $traitScores['extraversion'],
            'agreeableness' => $traitScores['agreeableness'],
            'neuroticism' => $traitScores['neuroticism'],
            'consistency_score' => $consistencyScore,
        ]);

        // Generate Career Exploration Recommendations
        $this->generatePersonalityRecommendations($assessmentScore, $traitScores);

        return $assessmentScore;
    }

    /**
     * Compute internal consistency metric based on response variance and reversal alignment.
     */
    protected function calculateConsistencyScore(Collection $answers): float
    {
        if ($answers->count() < 4) {
            return 100.0;
        }

        // Evaluate variance across normal vs reverse questions
        $straightScores = $answers->filter(fn(Answer $a) => !$a->question->is_reverse_scored)->pluck('answer_value')->map(fn($v) => (int)$v);
        $reverseScores = $answers->filter(fn(Answer $a) => $a->question->is_reverse_scored)->pluck('answer_value')->map(fn($v) => 6 - (int)$v);

        if ($straightScores->isEmpty() || $reverseScores->isEmpty()) {
            return 95.0;
        }

        $straightMean = $straightScores->average();
        $reverseMean = $reverseScores->average();

        $discrepancy = abs($straightMean - $reverseMean); // max discrepancy 4.0
        $consistency = max(50.0, min(100.0, 100.0 - ($discrepancy * 12.5)));

        return round($consistency, 1);
    }

    protected function determineCognitiveArchetype(array $dimStats): string
    {
        $log = $dimStats['logical']['score'];
        $num = $dimStats['numerical']['score'];
        $ver = $dimStats['verbal']['score'];

        if ($log >= 75 && $num >= 75) {
            return 'Analytical & Quantitative Strategist';
        }
        if ($log >= 75 && $ver >= 75) {
            return 'Logical & Conceptual Thinker';
        }
        if ($num >= 75 && $ver >= 75) {
            return 'Versatile Synthesizer';
        }
        if ($log >= 75) {
            return 'Systematic Problem Solver';
        }
        if ($num >= 75) {
            return 'Data & Precision Specialist';
        }
        if ($ver >= 75) {
            return 'Articulate Communicator';
        }

        return 'Balanced Generalist';
    }

    protected function determinePersonalityArchetype(array $traits): string
    {
        $o = $traits['openness'];
        $c = $traits['conscientiousness'];
        $e = $traits['extraversion'];
        $a = $traits['agreeableness'];
        $n = $traits['neuroticism'];

        if ($c >= 70 && $o >= 70) {
            return 'Innovative Architect (Terstruktur & Kreatif)';
        }
        if ($c >= 70 && $e >= 65) {
            return 'Dynamic Driver (Fokus Eksekusi & Pemimpin Tim)';
        }
        if ($a >= 70 && $e >= 65) {
            return 'Empathetic Facilitator (Kolaboratif & Komunikatif)';
        }
        if ($c >= 70 && $n <= 35) {
            return 'Reliable Anchor (Tenang, Konsisten & Bertanggung Jawab)';
        }
        if ($o >= 70 && $e >= 65) {
            return 'Visionary Explorer (Penuh Ide & Antusias)';
        }
        if ($c >= 65) {
            return 'Structured Planner (Teliti & Disiplin)';
        }

        return 'Adaptive Pragmatist (Fleksibel & Seimbang)';
    }

    protected function getTopDimensionName(array $dimStats): string
    {
        uasort($dimStats, fn($a, $b) => $b['score'] <=> $a['score']);
        $topKey = array_key_first($dimStats);

        return match ($topKey) {
            'logical' => 'Penalaran Logis (Logical Reasoning)',
            'numerical' => 'Penalaran Numerik (Numerical Reasoning)',
            'verbal' => 'Penalaran Verbal (Verbal Reasoning)',
            default => 'Kognitif Umum',
        };
    }

    protected function getTopPersonalityTraitName(array $traits): string
    {
        arsort($traits);
        $topKey = array_key_first($traits);

        return match ($topKey) {
            'openness' => 'Openness (Keterbukaan Ide & Kreativitas)',
            'conscientiousness' => 'Conscientiousness (Ketelitian & Kedisiplinan)',
            'extraversion' => 'Extraversion (Interaksi Sosial & Antusiasme)',
            'agreeableness' => 'Agreeableness (Empati & Keramahan)',
            'neuroticism' => 'Sensitivitas Responsif',
            default => 'Kepribadian Seimbang',
        };
    }

    protected function generateCognitiveRecommendations(AssessmentScore $score, array $dimStats): void
    {
        $log = $dimStats['logical']['score'];
        $num = $dimStats['numerical']['score'];
        $ver = $dimStats['verbal']['score'];

        $suggestedCareers = [];
        if ($log >= 70 && $num >= 70) {
            $suggestedCareers = array_merge($suggestedCareers, ['Data Analysis & Science', 'Software Architecture', 'Financial Modeling']);
        } elseif ($log >= 70) {
            $suggestedCareers = array_merge($suggestedCareers, ['System Engineering', 'Business Process Analysis', 'Quality Assurance']);
        }

        if ($num >= 70) {
            $suggestedCareers = array_merge($suggestedCareers, ['Quantitative Research', 'Actuarial Analysis', 'Fintech Analytics']);
        }

        if ($ver >= 70) {
            $suggestedCareers = array_merge($suggestedCareers, ['Strategic Communications', 'Product Marketing & Copywriting', 'Legal & Policy Advisory']);
        }

        if (empty($suggestedCareers)) {
            $suggestedCareers = ['Project Coordination', 'Operations Management', 'Business Administration'];
        }

        Recommendation::create([
            'assessment_score_id' => $score->id,
            'title' => 'Bidang Eksplorasi Berbasis Kekuatan Kognitif',
            'category' => 'career_exploration',
            'description' => 'Area yang berpotensi selaras dengan profil kecenderungan pemrosesan informasi Anda (disajikan sebagai eksplorasi, bukan deterministik).',
            'suggested_areas' => array_unique(array_slice($suggestedCareers, 0, 4)),
        ]);
    }

    protected function generatePersonalityRecommendations(AssessmentScore $score, array $traits): void
    {
        $suggestedEnvironments = [];

        if ($traits['conscientiousness'] >= 65) {
            $suggestedEnvironments = array_merge($suggestedEnvironments, ['Manajemen Proyek Berbasis Metrik', 'Operasional Skala Besar', 'Pengembangan Sistem Kepatuhan']);
        }

        if ($traits['openness'] >= 65) {
            $suggestedEnvironments = array_merge($suggestedEnvironments, ['Riset & Pengembangan (R&D)', 'Desain Produk & UI/UX', 'Inovasi Bisnis Baru']);
        }

        if ($traits['extraversion'] >= 65) {
            $suggestedEnvironments = array_merge($suggestedEnvironments, ['Kemitraan Strategis & Komunitas', 'Kepemimpinan Lintas Tim', 'Customer Success']);
        }

        if (empty($suggestedEnvironments)) {
            $suggestedEnvironments = ['Analisis Kebijakan', 'Dukungan Teknis & Solusi', 'Tata Kelola Informasi'];
        }

        Recommendation::create([
            'assessment_score_id' => $score->id,
            'title' => 'Lingkungan Kerja & Gaya Kolaborasi yang Berpotensi Sesuai',
            'category' => 'career_exploration',
            'description' => 'Gaya kerja dan dinamika organisasi yang cenderung mendukung kenyamanan kinerja berdasarkan profil Big Five Anda.',
            'suggested_areas' => array_unique(array_slice($suggestedEnvironments, 0, 4)),
        ]);
    }
}
