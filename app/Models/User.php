<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'admin']);
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionName)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->roles->contains($role->id)) {
            $this->roles()->attach($role->id);
        }
    }

    /**
     * Return masked email, e.g. h********@gmail.com
     */
    public function getMaskedEmailAttribute(): string
    {
        $parts = explode('@', $this->email);
        if (count($parts) !== 2) {
            return '---';
        }

        $name = $parts[0];
        $domain = $parts[1];

        $firstChar = substr($name, 0, 1);
        $maskedName = $firstChar . str_repeat('*', max(4, strlen($name) - 1));

        return $maskedName . '@' . $domain;
    }
}
