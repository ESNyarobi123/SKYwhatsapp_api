<?php

namespace App\Services\Payment;

use App\Models\Setting;
use App\Models\Subscription;
use Illuminate\Support\Str;

class Trc20Service
{
    /**
     * Get TRC20 wallet address from settings.
     */
    public function getWalletAddress(): ?string
    {
        return Setting::getValue('trc20_wallet_address');
    }

    /**
     * Generate payment instructions for TRC20 payment.
     */
    public function generatePaymentInstructions(float $amount, Subscription $subscription): array
    {
        $walletAddress = $this->getWalletAddress();

        if (! $walletAddress) {
            throw new \Exception('TRC20 wallet address not configured. Please contact administrator.');
        }

        // Generate unique payment reference
        $paymentReference = 'TRC20_'.Str::random(16);

        return [
            'wallet_address' => $walletAddress,
            'amount' => $amount,
            'currency' => 'USDT', // TRC20 typically uses USDT
            'payment_reference' => $paymentReference,
            'network' => 'TRC20',
            'instructions' => [
                'Send exactly '.number_format($amount, 2).' USDT to the wallet address above',
                'Use TRC20 network only',
                'Include payment reference in memo/note if possible',
                'Screenshot the transaction and upload it for verification',
            ],
        ];
    }

    /**
     * Get TRC20 screenshot URL from settings.
     */
    public function getScreenshotUrl(): ?string
    {
        $screenshot = Setting::getValue('trc20_screenshot');

        if (! $screenshot) {
            return null;
        }

        return asset('storage/'.$screenshot);
    }
}