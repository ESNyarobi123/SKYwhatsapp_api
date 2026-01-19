<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Webhook extends Model
{
    protected $fillable = [
        'user_id',
        'instance_id',
        'url',
        'events',
        'secret',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the webhook.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instance associated with the webhook.
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Check if webhook listens to a specific event.
     */
    public function listensTo(string $event): bool
    {
        return in_array($event, $this->events ?? [], true);
    }
}
