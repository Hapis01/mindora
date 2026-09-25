<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MlFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'anonymous_user_id',
        'assessment_score_id',
        'feature_vector',
    ];

    protected $casts = [
        'feature_vector' => 'array',
    ];

    public function assessmentScore(): BelongsTo
    {
        return $this->belongsTo(AssessmentScore::class);
    }
}
