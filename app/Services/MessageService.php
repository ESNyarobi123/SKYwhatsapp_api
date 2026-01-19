<?php

namespace App\Services;

use App\Models\Instance;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Str;

class MessageService
{
    /**
     * Create a message to be sent (queued for Node.js processing).
     */
    public function createOutboundMessage(
        User $user,
        Instance $instance,
        string $to,
        string $body,
        ?array $metadata = null
    ): Message {
        return Message::create([
            'user_id' => $user->id,
            'instance_id' => $instance->id,
            'message_id' => 'msg_'.Str::random(32),
            'direction' => 'outbound',
            'status' => 'sent',
            'to' => $to,
            'from' => $instance->phone_number ?? 'unknown',
            'body' => $body,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Log an incoming message (from Node.js).
     */
    public function createInboundMessage(
        User $user,
        Instance $instance,
        string $messageId,
        string $from,
        string $body,
        ?array $metadata = null
    ): Message {
        return Message::create([
            'user_id' => $user->id,
            'instance_id' => $instance->id,
            'message_id' => $messageId,
            'direction' => 'inbound',
            'status' => 'delivered',
            'to' => $instance->phone_number ?? 'unknown',
            'from' => $from,
            'body' => $body,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Update message status.
     */
    public function updateMessageStatus(Message $message, string $status): bool
    {
        return $message->update(['status' => $status]);
    }
}
