<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AssessmentScore;
use App\Services\AI\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AIController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get or initialize a consultation conversation for an assessment score.
     */
    public function getConversation(Request $request, ?AssessmentScore $score = null): JsonResponse|View
    {
        $user = Auth::user();

        if ($score && !$user->isAdmin() && $score->user_id !== $user->id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Akses ditolak ke hasil asesmen ini.'], 403);
            }
            abort(403, 'Akses ditolak ke hasil asesmen ini.');
        }

        // If score not explicitly specified in URL, look for user's latest score for context
        if (!$score) {
            $score = AssessmentScore::where('user_id', $user->id)->latest()->first();
        }

        $conversation = $this->aiService->getOrCreateConversation($user, $score);

        $messages = $conversation->userVisibleMessages()->get()->map(fn($m) => [
            'id' => $m->id,
            'role' => $m->role,
            'content' => $m->content,
            'created_at' => $m->created_at->format('H:i'),
        ]);

        $providerName = $this->aiService->getProvider()->getProviderName();

        // If requested via AJAX/fetch or JSON expectation (e.g. from result.blade.php)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'conversation_id' => $conversation->id,
                'title' => $conversation->title,
                'provider' => $providerName,
                'messages' => $messages,
            ]);
        }

        // Otherwise render the full responsive AI Consultation Blade view
        $userScores = AssessmentScore::where('user_id', $user->id)
            ->with('session.assessment')
            ->latest()
            ->get();

        return view('ai.index', [
            'conversation' => $conversation,
            'messages' => $messages,
            'score' => $score,
            'userScores' => $userScores,
            'provider' => $providerName,
        ]);
    }

    /**
     * Send a user query to the AI Assistant and receive constructive response.
     */
    public function sendMessage(Request $request, AiConversation $conversation): JsonResponse
    {
        $user = Auth::user();

        if ($conversation->user_id !== $user->id) {
            return response()->json(['error' => 'Akses ditolak ke percakapan ini.'], 403);
        }

        $validated = $request->validate([
            'prompt' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        $reply = $this->aiService->askAssistant($conversation, $validated['prompt']);

        return response()->json([
            'status' => 'success',
            'message' => [
                'id' => $reply->id,
                'role' => $reply->role,
                'content' => $reply->content,
                'model' => $reply->model,
                'tokens_used' => $reply->tokens_used,
                'created_at' => $reply->created_at->format('H:i'),
            ],
        ]);
    }

    /**
     * Clear chat message history while retaining the system prompt context.
     */
    public function clearHistory(AiConversation $conversation): JsonResponse
    {
        $user = Auth::user();

        if ($conversation->user_id !== $user->id) {
            return response()->json(['error' => 'Akses ditolak ke percakapan ini.'], 403);
        }

        $conversation->messages()->whereIn('role', ['user', 'assistant'])->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Riwayat konsultasi berhasil dibersihkan.',
        ]);
    }
}
