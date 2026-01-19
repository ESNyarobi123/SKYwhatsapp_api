<?php

namespace App\Services\Payment;

use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;

class PayPalService
{
    /**
     * Generate PayPal payment link (PayPal.me).
     */
    public function generatePaymentLink(float $amount, string $currency, User $user, Subscription $subscription): string
    {
        $paypalEmail = $this->getPayPalEmail();

        if (! $paypalEmail) {
            throw new \Exception('PayPal email not configured. Please contact administrator.');
        }

        // Get PayPal.me username from settings
        $paypalMeUsername = Setting::getValue('paypal_me_username');
        
        if (empty($paypalMeUsername) || ! trim($paypalMeUsername)) {
            // If username not set, throw error to prompt admin to configure it
            throw new \Exception('PayPal.me username is not configured. Please contact administrator to set it in Payment Settings.');
        }

        $username = trim($paypalMeUsername);
        
        // If username contains @ (is an email), extract just the prefix part
        if (str_contains($username, '@')) {
            $username = explode('@', $username)[0];
        }
        
        // Clean username: remove any invalid characters
        $username = preg_replace('/[^a-zA-Z0-9_-]/', '', $username);

        // Format amount: PayPal.me expects integer or decimal number
        // Remove decimals for whole numbers, keep 2 decimals for decimal amounts
        if ($amount == floor($amount)) {
            $amountFormatted = (string) (int) $amount; // Whole number
        } else {
            $amountFormatted = number_format($amount, 2, '.', ''); // Decimal
        }

        // PayPal.me format: https://paypal.me/{username}/{amount}
        // Currency is handled by PayPal based on account settings
        return "https://paypal.me/{$username}/{$amountFormatted}";
    }

    /**
     * Get PayPal email from settings.
     */
    public function getPayPalEmail(): ?string
    {
        return Setting::getValue('paypal_email');
    }

    /**
     * Generate payment reference for tracking.
     */
    public function generatePaymentReference(User $user, Subscription $subscription): string
    {
        return 'PAYPAL_'.$subscription->id.'_'.time();
    }
}