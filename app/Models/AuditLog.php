<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'actor_type',
        'action',
        'target_type',
        'target_id',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Record an audit log entry.
     * Note: Never pass plaintext sensitive data (passwords, encryption keys, raw PII) in metadata.
     */
    public static function record(
        string $action,
        ?Model $target = null,
        array $metadata = [],
        ?int $actorId = null,
        ?string $actorType = null
    ): self {
        $user = auth()->user();

        // Sanitize metadata to never include forbidden keys
        $forbiddenKeys = ['password', 'password_confirmation', 'token', 'key', 'secret', 'phone', 'address', 'dob', 'date_of_birth'];
        $sanitizedMetadata = collect($metadata)->map(function ($value, $key) use ($forbiddenKeys) {
            if (in_array(strtolower($key), $forbiddenKeys)) {
                return '[REDACTED]';
            }
            return $value;
        })->toArray();

        return static::create([
            'actor_id' => $actorId ?? ($user ? $user->id : null),
            'actor_type' => $actorType ?? ($user ? get_class($user) : null),
            'action' => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target ? $target->getKey() : null,
            'metadata' => empty($sanitizedMetadata) ? null : $sanitizedMetadata,
            'ip_address' => Request::ip(),
            'user_agent' => substr(Request::userAgent() ?? '', 0, 255),
            'created_at' => now(),
        ]);
    }
}
