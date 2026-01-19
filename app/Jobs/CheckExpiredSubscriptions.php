<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\WebhookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(WebhookService $webhookService): void
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);

            Log::info('Subscription expired', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);

            // Send webhook notification
            $webhookService->dispatch(
                'billing.expiring',
                $subscription->user,
                [
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->plan_name,
                    'expired_at' => $subscription->expires_at->toIso8601String(),
                ]
            );
        }

        Log::info('Expired subscriptions check completed', [
            'expired_count' => $expiredSubscriptions->count(),
        ]);
    }
}
