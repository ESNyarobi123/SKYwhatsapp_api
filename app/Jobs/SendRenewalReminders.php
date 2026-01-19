<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\WebhookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendRenewalReminders implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(WebhookService $webhookService): void
    {
        // Send reminders 3 days before expiry
        $reminderDate = now()->addDays(3);

        $subscriptionsNeedingReminder = Subscription::where('status', 'active')
            ->whereDate('expires_at', $reminderDate->toDateString())
            ->get();

        foreach ($subscriptionsNeedingReminder as $subscription) {
            Log::info('Sending renewal reminder', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'expires_at' => $subscription->expires_at,
            ]);

            // Send webhook notification
            $webhookService->dispatch(
                'billing.expiring',
                $subscription->user,
                [
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->plan_name,
                    'expires_at' => $subscription->expires_at->toIso8601String(),
                    'days_remaining' => $subscription->expires_at->diffInDays(now()),
                ]
            );
        }

        Log::info('Renewal reminders sent', [
            'reminder_count' => $subscriptionsNeedingReminder->count(),
        ]);
    }
}
