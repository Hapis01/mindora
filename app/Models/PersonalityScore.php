<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalityScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_score_id',
        'openness',
        'conscientiousness',
        'extraversion',
        'agreeableness',
        'neuroticism',
        'consistency_score',
    ];

    protected $casts = [
        'openness' => 'float',
        'conscientiousness' => 'float',
        'extraversion' => 'float',
        'agreeableness' => 'float',
        'neuroticism' => 'float',
        'consistency_score' => 'float',
    ];

    public function assessmentScore(): BelongsTo
    {
        return $this->belongsTo(AssessmentScore::class);
    }
}
