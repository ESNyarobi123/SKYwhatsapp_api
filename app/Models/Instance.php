<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Instance extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'status',
        'qr_code',
        'qr_expires_at',
        'session_data',
        'last_connected_at',
    ];

    protected function casts(): array
    {
        return [
            'qr_expires_at' => 'datetime',
            'last_connected_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the instance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all messages for this instance.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get all webhooks for this instance.
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    /**
     * Get all bot replies for this instance.
     */
    public function botReplies(): HasMany
    {
        return $this->hasMany(BotReply::class);
    }

    /**
     * Encrypt session data before saving.
     */
    public function setSessionDataAttribute($value): void
    {
        $this->attributes['session_data'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt session data when retrieving.
     */
    public function getSessionDataAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If decryption fails (e.g., corrupted data), return null
            return null;
        }
    }

    /**
     * Encrypt QR code before saving.
     */
    public function setQrCodeAttribute($value): void
    {
        $this->attributes['qr_code'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt QR code when retrieving.
     */
    public function getQrCodeAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If decryption fails (e.g., corrupted data), return null
            return null;
        }
    }

    /**
     * Check if instance is connected.
     */
    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    /**
     * Check if QR code is expired.
     */
    public function isQrExpired(): bool
    {
        return $this->qr_expires_at && $this->qr_expires_at->isPast();
    }
}
