<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'question_id',
        'selected_option_id',
        'answer_value',
        'is_correct',
        'response_time_ms',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'response_time_ms' => 'integer',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AssessmentSession::class, 'session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }
}
