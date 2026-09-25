<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'title',
        'description',
        'version',
        'status',
        'duration_minutes',
        'instructions',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssessmentCategory::class, 'category_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AssessmentSession::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function isCognitive(): bool
    {
        return $this->category && $this->category->code === 'cognitive';
    }

    public function isPersonality(): bool
    {
        return $this->category && $this->category->code === 'personality';
    }

    /**
     * Retrieve the model for a bound value (supports ID, legacy fallback ID 1 & 2, and assessment code).
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        if ($field) {
            return $query->where($field, $value);
        }

        if (is_numeric($value)) {
            $found = (clone $query)->where('id', $value)->first();
            if ($found) {
                return $query->where('id', $value);
            }

            // Legacy ID 1: Cognitive
            if ((int)$value === 1) {
                return $query->where('code', 'cognitive-battery-v1')
                    ->orWhereHas('category', fn($q) => $q->where('code', 'cognitive'));
            }

            // Legacy ID 2: Personality (Big Five)
            if ((int)$value === 2) {
                return $query->where('code', 'big-five-v1')
                    ->orWhereHas('category', fn($q) => $q->where('code', 'personality'));
            }
        }

        return $query->where('code', $value);
    }
}
