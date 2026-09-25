<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_score_id',
        'title',
        'category',
        'description',
        'suggested_areas',
    ];

    protected $casts = [
        'suggested_areas' => 'array',
    ];

    public function assessmentScore(): BelongsTo
    {
        return $this->belongsTo(AssessmentScore::class);
    }
}
