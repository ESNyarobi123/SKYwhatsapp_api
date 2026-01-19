<?php

namespace App\Services;

use App\Jobs\SendWebhook;
use App\Models\Instance;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Support\Str;

class WebhookService
{
    /**
     * Create a new webhook.
     */
    public function create(User $user, string $url, array $events, ?Instance $instance = null): Webhook
    {
        return Webhook::create([
            'user_id' => $user->id,
            'instance_id' => $instance?->id,
            'url' => $url,
            'events' => $events,
            'secret' => Str::random(32),
            'is_active' => true,
        ]);
    }

    /**
     * Generate HMAC signature for webhook payload.
     */
    public function generateSignature(string $payload, string $secret): string
    {
        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Dispatch webhook to user's webhooks.
     */
    public function dispatch(string $event, User $user, array $payload, ?Instance $instance = null): void
    {
        $webhooks = Webhook::where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function ($query) use ($event, $instance) {
                $query->whereJsonContains('events', $event);
                
                if ($instance) {
                    $query->where(function ($q) use ($instance) {
                        $q->where('instance_id', $instance->id)
                            ->orWhereNull('instance_id');
                    });
                } else {
                    $query->whereNull('instance_id');
                }
            })
            ->get();

        foreach ($webhooks as $webhook) {
            SendWebhook::dispatch($webhook, $event, $payload);
        }
    }
}
