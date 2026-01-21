<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    protected $fillable = [
        'webhook_id',
        'user_id',
        'event_type',
        'payload',
        'response',
        'status_code',
        'status',
        'error_message',
        'response_time_ms',
        'retry_count',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'response' => 'array',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Get the webhook that owns the log.
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    /**
     * Get the user that owns the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the log as success.
     */
    public function markAsSuccess(int $statusCode, array $response, int $responseTime): void
    {
        $this->update([
            'status' => 'success',
            'status_code' => $statusCode,
            'response' => $response,
            'response_time_ms' => $responseTime,
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark the log as failed.
     */
    public function markAsFailed(string $error, ?int $statusCode = null): void
    {
        $this->update([
            'status' => 'failed',
            'status_code' => $statusCode,
            'error_message' => $error,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * Check if delivery was successful.
     */
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Scope for successful deliveries.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed deliveries.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for event type filter.
     */
    public function scopeEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }
}
