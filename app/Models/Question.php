<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'dimension',
        'question_text',
        'question_type',
        'weight',
        'difficulty',
        'is_reverse_scored',
        'order',
        'status',
    ];

    protected $casts = [
        'is_reverse_scored' => 'boolean',
        'weight' => 'integer',
        'order' => 'integer',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
