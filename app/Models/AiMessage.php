<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'provider',
        'model',
        'tokens_used',
    ];

    protected $casts = [
        'tokens_used' => 'integer',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    public function isSystem(): bool
    {
        return $this->role === 'system';
    }
}
