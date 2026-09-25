<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentScore;
use App\Models\AssessmentSession;
use App\Models\AuditLog;
use App\Models\QuestionOption;
use App\Services\PsychometricService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    /**
     * List all available psychometric assessments.
     */
    public function index(): View
    {
        $categories = AssessmentCategory::with(['assessments' => function ($q) {
            $q->published()->withCount('questions');
        }])->get();

        $user = Auth::user();
        $recentSessions = AssessmentSession::where('user_id', $user->id)
            ->with(['assessment', 'score'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('assessments.index', [
            'categories' => $categories,
            'recentSessions' => $recentSessions,
        ]);
    }

    /**
     * Display assessment instructions and details before starting.
     */
    public function instructions(Assessment $assessment): View
    {
        $assessment->loadCount('questions');
        $user = Auth::user();

        // Check if there is an existing in_progress session
        $existingSession = AssessmentSession::where('user_id', $user->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'in_progress')
            ->first();

        return view('assessments.instructions', [
            'assessment' => $assessment,
            'existingSession' => $existingSession,
        ]);
    }

    /**
     * Start a new assessment session or resume an in-progress session.
     */
    public function start(Assessment $assessment): RedirectResponse
    {
        $user = Auth::user();

        // Resume existing active session if available
        $session = AssessmentSession::where('user_id', $user->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$session) {
            $session = AssessmentSession::create([
                'user_id' => $user->id,
                'assessment_id' => $assessment->id,
                'version' => $assessment->version,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            AuditLog::record(
                action: 'start_assessment',
                target: $session,
                metadata: ['assessment_code' => $assessment->code, 'version' => $assessment->version],
                actorId: $user->id,
                actorType: get_class($user)
            );
        }

        return redirect()->route('assessments.take', $session);
    }

    /**
     * Interactive test taking room with state persistence across refresh.
     */
    public function take(AssessmentSession $session): View|RedirectResponse
    {
        $user = Auth::user();

        if ($session->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke sesi asesmen ini.');
        }

        if ($session->isCompleted()) {
            return redirect()->route('assessments.result', $session->score);
        }

        $session->loadMissing(['assessment.questions.options', 'answers']);

        $savedAnswers = $session->answers->keyBy('question_id');

        $elapsedSeconds = now()->diffInSeconds($session->started_at);
        $totalAllowedSeconds = $session->assessment->duration_minutes * 60;
        $remainingSeconds = max(0, (int) round($totalAllowedSeconds - $elapsedSeconds));

        return view('assessments.take', [
            'session' => $session,
            'assessment' => $session->assessment,
            'questions' => $session->assessment->questions,
            'savedAnswers' => $savedAnswers,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    /**
     * Record/Save an answer with response time tracking.
     */
    public function saveAnswer(AssessmentSession $session, Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($session->user_id !== $user->id || !$session->isInProgress()) {
            return response()->json(['error' => 'Sesi asesmen tidak valid atau telah selesai.'], 403);
        }

        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'selected_option_id' => ['required', 'exists:question_options,id'],
            'response_time_ms' => ['nullable', 'integer', 'min:0'],
        ]);

        $option = QuestionOption::findOrFail($validated['selected_option_id']);

        Answer::updateOrCreate(
            [
                'session_id' => $session->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_option_id' => $option->id,
                'answer_value' => (string) $option->score_value,
                'is_correct' => $option->is_correct,
                'response_time_ms' => $validated['response_time_ms'] ?? null,
            ]
        );

        return response()->json([
            'status' => 'success',
            'saved_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Finalize the assessment session, compute psychometric scores, and generate insights.
     */
    public function finish(AssessmentSession $session, PsychometricService $psychometricService): RedirectResponse
    {
        $user = Auth::user();

        if ($session->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke sesi ini.');
        }

        if ($session->isCompleted()) {
            return redirect()->route('assessments.result', $session->score);
        }

        $session->update([
            'status' => 'completed',
            'completed_at' => now(),
            'total_time_seconds' => now()->diffInSeconds($session->started_at),
        ]);

        // Compute scoring via Psychometric Engine
        $score = $psychometricService->scoreSession($session);

        AuditLog::record(
            action: 'finish_assessment',
            target: $score,
            metadata: [
                'assessment_type' => $score->assessment_type,
                'overall_score' => $score->overall_score,
                'total_time_seconds' => $session->total_time_seconds,
            ],
            actorId: $user->id,
            actorType: get_class($user)
        );

        return redirect()->route('assessments.result', $score)->with('success', 'Asesmen berhasil diselesaikan! Berikut adalah analisis hasil psikometri Anda.');
    }

    /**
     * Display rich psychometric result dashboard with radar & bar charts.
     */
    public function result(AssessmentScore $score): View
    {
        $user = Auth::user();

        // Enforce authorization: user can only view their own result (admins can view any)
        if (!$user->isAdmin() && $user->id !== $score->user_id) {
            abort(403, 'Akses ditolak: Anda hanya dapat melihat hasil asesmen milik Anda sendiri.');
        }

        $score->loadMissing([
            'session.assessment.category',
            'cognitiveScore',
            'personalityScore',
            'recommendations',
            'user',
            'mlPrediction.model',
            'mlFeature',
        ]);

        if (!$score->mlPrediction) {
            $mlPrediction = app(\App\Services\MachineLearningService::class)->analyzeAndStore($score);
            if ($mlPrediction) {
                $score->setRelation('mlPrediction', $mlPrediction);
            }
        }

        return view('assessments.result', [
            'score' => $score,
            'session' => $score->session,
            'assessment' => $score->session->assessment,
            'cognitiveScore' => $score->cognitiveScore,
            'personalityScore' => $score->personalityScore,
            'recommendations' => $score->recommendations,
            'mlPrediction' => $score->mlPrediction,
        ]);
    }
}
