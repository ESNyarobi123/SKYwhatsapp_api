<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Subscription;
use App\Services\Payment\MpesaService;
use App\Services\WebhookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPaymentWebhooks implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Payment $payment,
        public array $callbackData
    ) {}

    /**
     * Execute the job.
     */
    public function handle(MpesaService $mpesaService, WebhookService $webhookService): void
    {
        // Process callback based on payment provider
        if ($this->payment->provider === 'mpesa') {
            $result = $mpesaService->processCallback($this->callbackData);
        }

        // Update payment status
        if (isset($result)) {
            $this->payment->update([
                'status' => 'completed',
                'metadata' => array_merge($this->payment->metadata ?? [], $this->callbackData),
            ]);

            // Update subscription if payment successful
            if ($this->payment->subscription_id) {
                $subscription = $this->payment->subscription;

                // Expire any other active subscriptions for this user (including trial)
                Subscription::where('user_id', $this->payment->user_id)
                    ->where('id', '!=', $subscription->id)
                    ->where('status', 'active')
                    ->update(['status' => 'expired']);

                $package = $subscription->package;
                $durationDays = $package ? $package->duration_days : 30;

                $subscription->update([
                    'status' => 'active',
                    'expires_at' => now()->addDays($durationDays),
                    'payment_provider' => $this->payment->provider,
                    'payment_reference' => $this->payment->reference,
                ]);
            }

            // Send webhook notification to user
            $webhookService->dispatch(
                'billing.payment_completed',
                $this->payment->user,
                [
                    'payment_id' => $this->payment->id,
                    'amount' => $this->payment->amount,
                    'provider' => $this->payment->provider,
                ]
            );
        }
    }
}
