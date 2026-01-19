<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class ApiKey extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'key',
        'key_preview',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the API key.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all usage logs for this API key.
     */
    public function usageLogs(): HasMany
    {
        return $this->hasMany(UsageLog::class);
    }

    /**
     * Hash the API key before saving.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($apiKey) {
            if (isset($apiKey->key) && ! str_starts_with($apiKey->key, '$2y$')) {
                $apiKey->key = Hash::make($apiKey->key);
            }
        });
    }

    /**
     * Check if API key is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if API key is valid.
     */
    public function isValid(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }
}
