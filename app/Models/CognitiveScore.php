<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CognitiveScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_score_id',
        'logical_score',
        'numerical_score',
        'verbal_score',
        'overall_cognitive_score',
        'percentile',
    ];

    protected $casts = [
        'logical_score' => 'float',
        'numerical_score' => 'float',
        'verbal_score' => 'float',
        'overall_cognitive_score' => 'float',
        'percentile' => 'float',
    ];

    public function assessmentScore(): BelongsTo
    {
        return $this->belongsTo(AssessmentScore::class);
    }
}
