<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Phase 6: AI Integration & Conversations.
     */
    public function up(): void
    {
        // 1. AI Conversations
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assessment_score_id')->nullable()->constrained('assessment_scores')->nullOnDelete();
            $table->string('title', 150)->default('Konsultasi Wawasan Psikometri');
            $table->timestamps();

            $table->index(['user_id', 'assessment_score_id']);
        });

        // 2. AI Messages
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->cascadeOnDelete();
            $table->string('role', 20); // system, user, assistant
            $table->text('content');
            $table->string('provider', 50)->default('deepseek');
            $table->string('model', 50)->nullable();
            $table->integer('tokens_used')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
    }
};
