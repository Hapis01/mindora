<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiMessage;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentSession;
use App\Models\AuditLog;
use App\Models\CognitiveScore;
use App\Models\MlFeature;
use App\Models\MlModel;
use App\Models\MlPrediction;
use App\Models\PersonalityScore;
use App\Models\Question;
use App\Models\User;
use App\Services\EncryptionService;
use App\Services\MachineLearningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Overview Analytics Dashboard with Metrics and Distribution Charts.
     */
    public function index(): View
    {
        $totalUsers = User::count();
        $totalSessions = AssessmentSession::where('status', 'completed')->count();
        $totalAiMessages = AiMessage::where('role', 'assistant')->count();
        $totalAuditLogs = AuditLog::count();

        // Cognitive Averages
        $cogAvgLogical = round(CognitiveScore::average('logical_score') ?? 70.0, 1);
        $cogAvgNumerical = round(CognitiveScore::average('numerical_score') ?? 68.0, 1);
        $cogAvgVerbal = round(CognitiveScore::average('verbal_score') ?? 74.0, 1);

        // Personality Big Five Averages
        $persAvgOpenness = round(PersonalityScore::average('openness') ?? 72.0, 1);
        $persAvgConscientiousness = round(PersonalityScore::average('conscientiousness') ?? 75.0, 1);
        $persAvgExtraversion = round(PersonalityScore::average('extraversion') ?? 64.0, 1);
        $persAvgAgreeableness = round(PersonalityScore::average('agreeableness') ?? 78.0, 1);
        $persAvgNeuroticism = round(PersonalityScore::average('neuroticism') ?? 38.0, 1);

        $recentAuditLogs = AuditLog::with('actor')->orderByDesc('created_at')->limit(8)->get();
        $recentScores = AssessmentScore::with(['user', 'session.assessment'])->orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalSessions' => $totalSessions,
            'totalAiMessages' => $totalAiMessages,
            'totalAuditLogs' => $totalAuditLogs,
            'cogAvgLogical' => $cogAvgLogical,
            'cogAvgNumerical' => $cogAvgNumerical,
            'cogAvgVerbal' => $cogAvgVerbal,
            'persAvgOpenness' => $persAvgOpenness,
            'persAvgConscientiousness' => $persAvgConscientiousness,
            'persAvgExtraversion' => $persAvgExtraversion,
            'persAvgAgreeableness' => $persAvgAgreeableness,
            'persAvgNeuroticism' => $persAvgNeuroticism,
            'recentAuditLogs' => $recentAuditLogs,
            'recentScores' => $recentScores,
        ]);
    }

    /**
     * User Management View with Default Sensitive Data Masking.
     */
    public function users(Request $request): View
    {
        $users = User::with(['roles', 'profile'])
            ->withCount('auditLogs')
            ->orderByDesc('created_at')
            ->paginate(15);

        $unlockedIds = session('admin_unlocked_user_ids', []);
        // Purge expired unlocked IDs
        $validUnlockedIds = [];
        $now = now()->timestamp;
        foreach ($unlockedIds as $uid => $expiresAt) {
            if ($expiresAt > $now) {
                $validUnlockedIds[$uid] = $expiresAt;
            }
        }
        session(['admin_unlocked_user_ids' => $validUnlockedIds]);

        return view('admin.users', [
            'users' => $users,
            'unlockedUserIds' => array_keys($validUnlockedIds),
        ]);
    }

    /**
     * Re-Authentication Endpoint: Verify Admin Password to Temporarily Decrypt User PII.
     */
    public function unlockUserSensitiveData(Request $request, User $user, EncryptionService $encryptionService): JsonResponse
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Verify Admin Password
        if (!Hash::check($validated['password'], $admin->password)) {
            AuditLog::record(
                action: 'admin_failed_unlock_sensitive_data',
                target: $user,
                metadata: ['target_user_id' => $user->id, 'reason' => 'invalid_admin_password'],
                actorId: $admin->id,
                actorType: get_class($admin)
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Konfirmasi password admin salah. Akses dekripsi ditolak.',
            ], 403);
        }

        // Record Successful Decryption Event in Audit Log
        AuditLog::record(
            action: 'admin_unlock_sensitive_data',
            target: $user,
            metadata: [
                'target_user_id' => $user->id,
                'unlocked_by_admin' => $admin->email,
                'valid_duration_minutes' => 5,
            ],
            actorId: $admin->id,
            actorType: get_class($admin)
        );

        // Store 5-minute session grant
        $expiresAt = now()->addMinutes(5)->timestamp;
        $unlocked = session('admin_unlocked_user_ids', []);
        $unlocked[$user->id] = $expiresAt;
        session(['admin_unlocked_user_ids' => $unlocked]);

        // Decrypt profile data
        $decryptedData = null;
        if ($user->profile) {
            $decryptedData = $user->profile->getDecryptedData($encryptionService);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Autentikasi berhasil. Data sensitif didekripsi sementara selama 5 menit.',
            'user_id' => $user->id,
            'expires_at' => $expiresAt,
            'decrypted' => [
                'name' => $decryptedData['name'] ?? ($user->name ?: 'Tidak diisi'),
                'dob' => $decryptedData['dob'] ?? 'Tidak diisi',
                'phone' => $decryptedData['phone'] ?? 'Tidak diisi',
                'address' => $decryptedData['address'] ?? 'Tidak diisi',
            ],
        ]);
    }

    /**
     * Manually Relock User Data Immediately.
     */
    public function lockUserSensitiveData(Request $request): JsonResponse
    {
        $admin = Auth::user();
        session()->forget('admin_unlocked_user_ids');

        AuditLog::record(
            action: 'admin_relock_sensitive_data',
            target: $admin,
            metadata: ['cleared_all_temporary_grants' => true],
            actorId: $admin->id,
            actorType: get_class($admin)
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Seluruh data sensitif pengguna berhasil dikunci kembali ke status masked.',
        ]);
    }

    /**
     * Question Bank Manager View with Assessment and Dimension Filters.
     */
    public function questions(Request $request): View
    {
        $assessments = Assessment::withCount('questions')->get();
        $selectedAssessmentId = $request->input('assessment_id', $assessments->first()?->id);

        $query = Question::with(['assessment', 'options']);

        if ($selectedAssessmentId) {
            $query->where('assessment_id', $selectedAssessmentId);
        }

        if ($request->filled('dimension')) {
            $query->where('dimension', $request->input('dimension'));
        }

        $questions = $query->orderBy('order')->paginate(15);
        $dimensions = Question::select('dimension')->distinct()->pluck('dimension');

        return view('admin.questions', [
            'assessments' => $assessments,
            'selectedAssessmentId' => $selectedAssessmentId,
            'questions' => $questions,
            'dimensions' => $dimensions,
        ]);
    }

    /**
     * Update Question attributes (weight, reverse scoring, status).
     */
    public function updateQuestion(Request $request, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'weight' => ['required', 'integer', 'min:1', 'max:10'],
            'is_reverse_scored' => ['required', 'boolean'],
            'status' => ['required', 'in:published,draft,inactive'],
        ]);

        $admin = Auth::user();

        $question->update($validated);

        AuditLog::record(
            action: 'admin_update_question',
            target: $question,
            metadata: [
                'question_id' => $question->id,
                'assessment_id' => $question->assessment_id,
                'changes' => $validated,
            ],
            actorId: $admin->id,
            actorType: get_class($admin)
        );

        return redirect()->back()->with('success', "Soal #{$question->id} berhasil diperbarui.");
    }

    /**
     * Comprehensive Security Audit Trail Explorer.
     */
    public function auditLogs(Request $request): View
    {
        $query = AuditLog::with('actor')->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('actor', fn($aq) => $aq->where('email', 'like', "%{$search}%"));
            });
        }

        $auditLogs = $query->paginate(20)->withQueryString();
        $distinctActions = AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.audit-logs', [
            'auditLogs' => $auditLogs,
            'distinctActions' => $distinctActions,
        ]);
    }

    /**
     * Model Management & ML Microservice Diagnostics.
     */
    public function models(Request $request, MachineLearningService $mlService): View
    {
        $models = MlModel::orderByDesc('created_at')->get();
        $totalPredictions = MlPrediction::count();
        $totalFeatures = MlFeature::count();

        // Diagnostics from FastAPI microservice
        $health = $mlService->checkHealth();

        // Cluster breakdown
        $clusterDistribution = MlPrediction::selectRaw('cluster_label, count(*) as count')
            ->groupBy('cluster_label')
            ->pluck('count', 'cluster_label')
            ->toArray();

        // Recent anonymous inference runs
        $recentPredictions = MlPrediction::with(['score.user', 'model'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $avgConfidence = round(MlPrediction::avg('confidence') ?? 0, 1);

        return view('admin.models', [
            'models' => $models,
            'totalPredictions' => $totalPredictions,
            'totalFeatures' => $totalFeatures,
            'avgConfidence' => $avgConfidence,
            'health' => $health,
            'clusterDistribution' => $clusterDistribution,
            'recentPredictions' => $recentPredictions,
        ]);
    }

    /**
     * Toggle ML Model active status.
     */
    public function toggleModelStatus(Request $request, MlModel $model): RedirectResponse
    {
        $admin = Auth::user();
        $newStatus = $model->status === 'active' ? 'inactive' : 'active';
        $model->update(['status' => $newStatus]);

        AuditLog::record(
            action: 'admin_toggle_ml_model_status',
            target: $model,
            metadata: [
                'model_id' => $model->id,
                'model_name' => $model->name,
                'new_status' => $newStatus,
            ],
            actorId: $admin->id,
            actorType: get_class($admin)
        );

        return redirect()->back()->with('success', "Status model {$model->name} berhasil diperbarui menjadi '{$newStatus}'.");
    }
}

