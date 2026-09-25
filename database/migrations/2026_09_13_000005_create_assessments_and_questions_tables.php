<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Assessment Categories (e.g. Cognitive, Personality)
        Schema::create('assessment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // cognitive, personality
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Assessments (e.g. Cognitive Battery v1.0, Big Five Personality v1.0)
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('assessment_categories')->cascadeOnDelete();
            $table->string('code', 50)->unique();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('version', 20)->default('v1.0');
            $table->enum('status', ['draft', 'published', 'inactive'])->default('draft')->index();
            $table->integer('duration_minutes')->default(15);
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        // 3. Questions
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->string('dimension', 50)->index(); // e.g. logical, numerical, verbal, openness, conscientiousness
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'likert'])->default('multiple_choice');
            $table->integer('weight')->default(1);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_reverse_scored')->default(false);
            $table->integer('order')->default(1);
            $table->enum('status', ['draft', 'published', 'inactive'])->default('published')->index();
            $table->timestamps();
        });

        // 4. Question Options
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->string('label', 20)->nullable(); // e.g. A, B, C, D or 1, 2, 3, 4, 5
            $table->text('option_text');
            $table->integer('score_value')->default(0); // 0 or 1 for MCQ, 1-5 for Likert
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        // 5. Assessment Sessions
        Schema::create('assessment_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->string('version', 20)->default('v1.0');
            $table->enum('status', ['in_progress', 'completed', 'expired'])->default('in_progress')->index();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->integer('total_time_seconds')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // 6. Answers
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('assessment_sessions')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->string('answer_value', 100)->nullable(); // selected text or raw value
            $table->boolean('is_correct')->nullable();
            $table->integer('response_time_ms')->nullable(); // tracks millisecond response latency
            $table->timestamps();

            $table->unique(['session_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
        Schema::dropIfExists('assessment_sessions');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('assessment_categories');
    }
};
