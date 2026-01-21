<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledMessage extends Model
{
    protected $fillable = [
        'user_id',
        'instance_id',
        'recipient',
        'content',
        'message_type',
        'media',
        'scheduled_at',
        'sent_at',
        'status',
        'error_message',
        'retry_count',
    ];

    protected function casts(): array
    {
        return [
            'media' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the scheduled message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instance for the scheduled message.
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Check if the message is due to be sent.
     */
    public function isDue(): bool
    {
        return $this->status === 'pending' && $this->scheduled_at->isPast();
    }

    /**
     * Mark the message as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark the message as failed.
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * Cancel the scheduled message.
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Scope for pending messages.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for due messages.
     */
    public function scopeDue($query)
    {
        return $query->pending()->where('scheduled_at', '<=', now());
    }
}
