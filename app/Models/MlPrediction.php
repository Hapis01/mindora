<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MlPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_score_id',
        'model_id',
        'cluster_id',
        'cluster_label',
        'predicted_category',
        'shap_values',
        'confidence',
    ];

    protected $casts = [
        'shap_values' => 'array',
        'confidence' => 'float',
        'cluster_id' => 'integer',
    ];

    public function assessmentScore(): BelongsTo
    {
        return $this->belongsTo(AssessmentScore::class, 'assessment_score_id');
    }

    public function score(): BelongsTo
    {
        return $this->belongsTo(AssessmentScore::class, 'assessment_score_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(MlModel::class, 'model_id');
    }
}
