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
        // 1. Assessment Scores (Master Record)
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->unique()->constrained('assessment_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('assessment_type', 50)->index(); // cognitive, personality
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->string('archetype_label', 150)->nullable();
            $table->text('summary_text')->nullable();
            $table->json('strengths')->nullable();
            $table->json('development_areas')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'assessment_type']);
        });

        // 2. Cognitive Sub-Scores
        Schema::create('cognitive_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_score_id')->unique()->constrained('assessment_scores')->cascadeOnDelete();
            $table->decimal('logical_score', 5, 2)->default(0);
            $table->decimal('numerical_score', 5, 2)->default(0);
            $table->decimal('verbal_score', 5, 2)->default(0);
            $table->decimal('overall_cognitive_score', 5, 2)->default(0);
            $table->decimal('percentile', 5, 2)->nullable();
            $table->timestamps();
        });

        // 3. Personality Sub-Scores (Big Five Model)
        Schema::create('personality_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_score_id')->unique()->constrained('assessment_scores')->cascadeOnDelete();
            $table->decimal('openness', 5, 2)->default(0);
            $table->decimal('conscientiousness', 5, 2)->default(0);
            $table->decimal('extraversion', 5, 2)->default(0);
            $table->decimal('agreeableness', 5, 2)->default(0);
            $table->decimal('neuroticism', 5, 2)->default(0);
            $table->decimal('consistency_score', 5, 2)->default(100);
            $table->timestamps();
        });

        // 4. Career and Self-Development Recommendations
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_score_id')->constrained('assessment_scores')->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('category', 50); // career_exploration, skill_growth, habit
            $table->text('description');
            $table->json('suggested_areas')->nullable(); // list of suggested exploration fields
            $table->timestamps();

            $table->index(['assessment_score_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('personality_scores');
        Schema::dropIfExists('cognitive_scores');
        Schema::dropIfExists('assessment_scores');
    }
};
