<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Phase 5: Machine Learning Integration.
     */
    public function up(): void
    {
        // 1. ML Models Registry
        Schema::create('ml_models', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('version', 50);
            $table->string('algorithm', 50);
            $table->timestamp('training_date')->nullable();
            $table->string('dataset_version', 50)->default('normative-synthetic-v1.0');
            $table->json('metrics')->nullable();
            $table->string('artifact_path', 255)->nullable();
            $table->string('status', 30)->default('active'); // active, archived, draft
            $table->timestamps();

            $table->index(['name', 'version']);
            $table->index('status');
        });

        // 2. Anonymized ML Feature Vectors (Zero PII)
        Schema::create('ml_features', function (Blueprint $table) {
            $table->id();
            $table->string('anonymous_user_id', 64)->index();
            $table->foreignId('assessment_score_id')->constrained('assessment_scores')->cascadeOnDelete();
            $table->json('feature_vector');
            $table->timestamps();

            $table->index('assessment_score_id');
        });

        // 3. ML Inference Predictions & SHAP Explainability
        Schema::create('ml_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_score_id')->constrained('assessment_scores')->cascadeOnDelete();
            $table->foreignId('model_id')->nullable()->constrained('ml_models')->nullOnDelete();
            $table->integer('cluster_id')->nullable();
            $table->string('cluster_label', 150)->nullable();
            $table->string('predicted_category', 150)->nullable();
            $table->json('shap_values')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->timestamps();

            $table->index('assessment_score_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_predictions');
        Schema::dropIfExists('ml_features');
        Schema::dropIfExists('ml_models');
    }
};
