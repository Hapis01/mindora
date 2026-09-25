<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assessment_score_id',
        'title',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assessmentScore(): BelongsTo
    {
        return $this->belongsTo(AssessmentScore::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function userVisibleMessages(): HasMany
    {
        return $this->messages()->whereIn('role', ['user', 'assistant']);
    }
}
