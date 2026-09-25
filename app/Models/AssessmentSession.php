<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssessmentSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assessment_id',
        'version',
        'status',
        'started_at',
        'completed_at',
        'total_time_seconds',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_time_seconds' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'session_id');
    }

    public function score(): HasOne
    {
        return $this->hasOne(AssessmentScore::class, 'session_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }
}
