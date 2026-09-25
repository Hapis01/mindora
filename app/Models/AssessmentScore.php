<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssessmentScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'assessment_type',
        'overall_score',
        'archetype_label',
        'summary_text',
        'strengths',
        'development_areas',
    ];

    protected $casts = [
        'overall_score' => 'float',
        'strengths' => 'array',
        'development_areas' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AssessmentSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cognitiveScore(): HasOne
    {
        return $this->hasOne(CognitiveScore::class, 'assessment_score_id');
    }

    public function personalityScore(): HasOne
    {
        return $this->hasOne(PersonalityScore::class, 'assessment_score_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'assessment_score_id');
    }

    public function mlPrediction(): HasOne
    {
        return $this->hasOne(MlPrediction::class, 'assessment_score_id');
    }

    public function mlFeature(): HasOne
    {
        return $this->hasOne(MlFeature::class, 'assessment_score_id');
    }
}
