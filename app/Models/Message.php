<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'instance_id',
        'message_id',
        'direction',
        'status',
        'to',
        'from',
        'body',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instance that sent/received the message.
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }
}
