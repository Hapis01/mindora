<?php

namespace Tests\Feature;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentSession;
use App\Models\User;
use App\Services\AI\AIService;
use App\Services\PsychometricService;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AIAssistantIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleAndPermissionSeeder::class, AssessmentSeeder::class]);
    }

    public function test_guest_cannot_access_ai_conversation(): void
    {
        $response = $this->getJson(route('ai.conversation'));
        $response->assertStatus(401);
    }

    public function test_user_can_initialize_ai_conversation_with_sanitized_context(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $cognitive = Assessment::where('code', 'cognitive-battery-v1')->first();

        $session = AssessmentSession::create([
            'user_id' => $user->id,
            'assessment_id' => $cognitive->id,
            'version' => 'v1.0',
            'status' => 'completed',
            'started_at' => now()->subMinutes(10),
            'completed_at' => now(),
            'total_time_seconds' => 600,
        ]);

        foreach ($cognitive->questions as $question) {
            $opt = $question->options->first();
            \App\Models\Answer::create([
                'session_id' => $session->id,
                'question_id' => $question->id,
                'selected_option_id' => $opt->id,
                'answer_value' => (string)$opt->score_value,
                'is_correct' => true,
                'response_time_ms' => 2000,
            ]);
        }

        $score = app(PsychometricService::class)->scoreSession($session);

        $response = $this->actingAs($user)->getJson(route('ai.conversation', $score->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'conversation_id',
            'title',
            'provider',
            'messages',
        ]);

        $conversation = AiConversation::find($response->json('conversation_id'));
        $this->assertNotNull($conversation);
        $this->assertEquals($user->id, $conversation->user_id);
        $this->assertEquals($score->id, $conversation->assessment_score_id);

        // Verify System Prompt was created with strict rules
        $systemMsg = $conversation->messages()->where('role', 'system')->first();
        $this->assertNotNull($systemMsg);
        $this->assertStringContainsString('BUKAN DIAGNOSIS KLINIS', $systemMsg->content);
        $this->assertStringContainsString('BUKAN TES IQ', $systemMsg->content);
        $this->assertStringContainsString('PRIVASI ZERO PII', $systemMsg->content);

        // Verify Zero PII: no email or user ID leaked in prompt
        $this->assertStringNotContainsString($user->email, $systemMsg->content);
        $this->assertStringNotContainsString('password', $systemMsg->content);
    }

    public function test_user_can_access_ai_conversation_web_view(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($user)->get(route('ai.conversation'));

        $response->assertStatus(200);
        $response->assertViewIs('ai.index');
        $response->assertSee('Asisten AI Gemini');
    }

    public function test_user_can_send_message_and_receive_insightful_ai_response(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $aiService = app(AIService::class);
        $conversation = $aiService->getOrCreateConversation($user);

        $response = $this->actingAs($user)->postJson(route('ai.message', $conversation), [
            'prompt' => 'Apa arti dan makna skor profil saya secara menyeluruh?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message' => [
                'id',
                'role',
                'content',
                'model',
                'created_at',
            ],
        ]);

        $this->assertEquals('assistant', $response->json('message.role'));
        $this->assertNotEmpty($response->json('message.content'));

        // Verify user and assistant messages exist in database
        $this->assertDatabaseHas('ai_messages', [
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Apa arti dan makna skor profil saya secara menyeluruh?',
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
        ]);
    }

    public function test_user_cannot_access_or_send_messages_to_another_users_conversation(): void
    {
        $userA = User::where('email', 'user@Mindora.test')->first();
        $userB = User::factory()->create([
            'email' => 'other_user@Mindora.test',
        ]);

        $aiService = app(AIService::class);
        $conversationA = $aiService->getOrCreateConversation($userA);

        // User B attempts to send message to User A's conversation
        $response = $this->actingAs($userB)->postJson(route('ai.message', $conversationA), [
            'prompt' => 'Halo, ini pesan dari user lain',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_clear_conversation_history(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();
        $aiService = app(AIService::class);
        $conversation = $aiService->getOrCreateConversation($user);

        // Add user and assistant messages
        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Test message',
            'provider' => 'deepseek',
        ]);

        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Test reply',
            'provider' => 'deepseek',
        ]);

        $this->assertCount(3, $conversation->messages); // 1 system + 2 messages

        $response = $this->actingAs($user)->deleteJson(route('ai.clear', $conversation));
        $response->assertStatus(200);

        // System message remains, chat messages cleared
        $this->assertEquals(1, $conversation->fresh()->messages()->count());
        $this->assertEquals('system', $conversation->fresh()->messages()->first()->role);
    }
}
