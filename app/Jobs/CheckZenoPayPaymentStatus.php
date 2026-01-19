<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\Payment\ZenoPayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckZenoPayPaymentStatus implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Payment $payment
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ZenoPayService $zenoPayService): void
    {
        // Check if payment has order_id
        if (! $this->payment->order_id) {
            Log::warning('CheckZenoPayPaymentStatus: Payment missing order_id', [
                'payment_id' => $this->payment->id,
            ]);

            return;
        }

        // Check payment status via ZenoPay API
        $statusResult = $zenoPayService->checkPaymentStatus($this->payment->order_id);

        if (! $statusResult['success']) {
            Log::error('CheckZenoPayPaymentStatus: Failed to check status', [
                'payment_id' => $this->payment->id,
                'order_id' => $this->payment->order_id,
                'error' => $statusResult['error'] ?? 'Unknown error',
            ]);

            return;
        }

        $paymentStatus = $statusResult['payment_status'] ?? 'pending';

        // If payment is completed, activate subscription
        if ($paymentStatus === 'COMPLETED' && $this->payment->status !== 'completed') {
            $this->payment->update([
                'status' => 'completed',
                'reference' => $statusResult['reference'] ?? $this->payment->reference,
                'tx_ref' => $statusResult['transid'] ?? $this->payment->tx_ref,
            ]);

            // Activate subscription
            if ($this->payment->subscription) {
                $this->payment->subscription->update([
                    'status' => 'active',
                ]);

                Log::info('CheckZenoPayPaymentStatus: Payment completed and subscription activated', [
                    'payment_id' => $this->payment->id,
                    'subscription_id' => $this->payment->subscription_id,
                ]);
            }
        } elseif ($paymentStatus === 'FAILED' || $paymentStatus === 'CANCELLED') {
            $this->payment->update([
                'status' => $paymentStatus === 'FAILED' ? 'failed' : 'cancelled',
            ]);

            Log::info('CheckZenoPayPaymentStatus: Payment marked as failed/cancelled', [
                'payment_id' => $this->payment->id,
                'status' => $paymentStatus,
            ]);
        }
    }
}