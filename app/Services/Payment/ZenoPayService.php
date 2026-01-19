<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ZenoPayService
{
    private string $baseUrl = 'https://zenoapi.com/api/payments';

    /**
     * Get ZenoPay API key from settings.
     */
    private function getApiKey(): string
    {
        // First try getValue method
        $apiKey = Setting::getValue('zenopay_api_key');

        // If getValue returns empty, check directly (in case is_active is false but we still want to use it)
        if (empty($apiKey)) {
            $setting = Setting::where('key', 'zenopay_api_key')->first();
            if ($setting && ! empty(trim($setting->value ?? ''))) {
                $apiKey = trim($setting->value);
                Log::warning('ZenoPay API Key found but is_active is false', [
                    'is_active' => $setting->is_active,
                ]);
            }
        }

        // Trim whitespace and return empty string if null or empty
        return $apiKey ? trim((string) $apiKey) : '';
    }

    /**
     * Initiate ZenoPay card payment (checkout).
     */
    public function initiateCardPayment(float $amount, string $currency, User $user, Subscription $subscription): array
    {
        // Check if test mode is enabled (for local development)
        $testMode = config('app.env') === 'local' && Setting::getValue('zenopay_test_mode', false);

        if ($testMode) {
            Log::info('ZenoPay Card Payment (TEST MODE - Local Development)', [
                'amount' => $amount,
                'currency' => $currency,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);

            // Return mock successful response for testing
            $orderId = 'ZENOPAY_TEST_'.Str::uuid()->toString();
            $txRef = 'TX_TEST_'.Str::random(10);

            // Create a mock payment link that redirects to callback with success
            $mockPaymentLink = route('dashboard.payment.zenopay.callback', [
                'subscription' => $subscription->id,
            ]).'?tx_ref='.$txRef.'&status=successful';

            return [
                'success' => true,
                'payment_link' => $mockPaymentLink,
                'tx_ref' => $txRef,
                'order_id' => $orderId,
            ];
        }

        $apiKey = $this->getApiKey();

        // Check if API key is configured
        if (empty($apiKey)) {
            // Check if setting exists in database
            $setting = Setting::where('key', 'zenopay_api_key')->first();

            Log::error('ZenoPay API Key Missing', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'setting_exists' => $setting !== null,
                'setting_value' => $setting ? (strlen($setting->value) > 0 ? 'SET ('.strlen($setting->value).' chars)' : 'EMPTY') : 'NOT FOUND',
                'setting_is_active' => $setting ? $setting->is_active : false,
            ]);

            return [
                'success' => false,
                'error' => 'ZenoPay API key is not configured. Please contact the administrator.',
            ];
        }

        $orderId = 'ZENOPAY_'.Str::uuid()->toString();

        Log::info('ZenoPay Card Payment Initiated', [
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => $currency,
            'user_id' => $user->id,
            'api_key_length' => strlen($apiKey),
        ]);

        try {
            // Prepare request payload according to ZenoPay API documentation
            $payload = [
                'order_id' => $orderId, // Required by ZenoPay/3G Direct Pay API
                'amount' => (float) $amount,
                'currency' => strtoupper($currency), // Ensure currency is uppercase (e.g., TZS, USD)
                'redirect_url' => route('dashboard.payment.zenopay.callback', ['subscription' => $subscription->id]),
                'buyer_email' => $user->email,
                'buyer_name' => $user->name,
                'buyer_phone' => $user->phone ?? '0000000000',
            ];

            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post("{$this->baseUrl}/checkout/", $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('ZenoPay Card Payment Success', [
                    'response' => $data,
                    'order_id' => $orderId,
                ]);

                return [
                    'success' => true,
                    'payment_link' => $data['payment_link'] ?? null,
                    'tx_ref' => $data['tx_ref'] ?? null,
                    'order_id' => $orderId,
                ];
            }

            // Log detailed error information
            $errorBody = $response->body();
            $errorJson = $response->json();

            Log::error('ZenoPay Card Payment Error', [
                'status' => $response->status(),
                'response_body' => $errorBody,
                'response_json' => $errorJson,
                'headers_sent' => [
                    'x-api-key' => substr($apiKey, 0, 10).'...',
                    'Content-Type' => 'application/json',
                ],
                'request_url' => "{$this->baseUrl}/checkout/",
                'order_id' => $orderId,
            ]);

            // Provide more helpful error message
            $errorMessage = 'Failed to initiate payment';
            if ($response->status() === 403) {
                $errorMessage = 'Authentication failed. Please verify your ZenoPay API key is correct and your account is approved.';
            } elseif ($response->status() === 401) {
                $errorMessage = 'Invalid API key. Please check your ZenoPay API key in settings.';
            } elseif (isset($errorJson['message'])) {
                $errorMessage = $errorJson['message'];
            } elseif (isset($errorJson['error'])) {
                $errorMessage = is_string($errorJson['error']) ? $errorJson['error'] : 'Payment initiation failed';
            }

            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ZenoPay Connection Error', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => 'Unable to connect to ZenoPay service. Please check your internet connection and try again, or contact support if the problem persists.',
            ];
        } catch (\Exception $e) {
            Log::error('ZenoPay Card Payment Exception', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while processing your payment. Please try again or contact support.',
            ];
        }
    }

    /**
     * Initiate ZenoPay mobile money payment (Tanzania).
     */
    public function initiateMobileMoneyPayment(float $amount, User $user, Subscription $subscription, string $phone): array
    {
        $apiKey = $this->getApiKey();

        // Check if API key is configured
        if (empty($apiKey)) {
            return [
                'success' => false,
                'error' => 'ZenoPay API key is not configured. Please contact the administrator.',
            ];
        }

        $orderId = 'ZENOPAY_'.Str::uuid()->toString();

        // Format phone number (ensure it starts with country code)
        $formattedPhone = $this->formatPhoneNumber($phone);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("{$this->baseUrl}/mobile_money_tanzania", [
                    'order_id' => $orderId,
                    'buyer_email' => $user->email,
                    'buyer_name' => $user->name,
                    'buyer_phone' => $formattedPhone,
                    'amount' => (int) $amount, // ZenoPay expects integer amount in TZS
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'order_id' => $orderId,
                    'status' => $data['status'] ?? 'pending',
                    'message' => $data['message'] ?? 'Payment initiated',
                ];
            }

            Log::error('ZenoPay Mobile Money Error', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Failed to initiate mobile money payment',
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ZenoPay Connection Error', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => 'Unable to connect to ZenoPay service. Please check your internet connection and try again, or contact support if the problem persists.',
            ];
        } catch (\Exception $e) {
            Log::error('ZenoPay Mobile Money Exception', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while processing your payment. Please try again or contact support.',
            ];
        }
    }

    /**
     * Check payment status via ZenoPay API.
     */
    public function checkPaymentStatus(string $orderId): array
    {
        // Check if test mode is enabled (for local development)
        $testMode = config('app.env') === 'local' && Setting::getValue('zenopay_test_mode', false);

        // If test mode and order ID starts with ZENOPAY_TEST, return success
        if ($testMode && str_starts_with($orderId, 'ZENOPAY_TEST_')) {
            Log::info('ZenoPay Status Check (TEST MODE)', [
                'order_id' => $orderId,
            ]);

            return [
                'success' => true,
                'payment_status' => 'COMPLETED',
                'message' => 'Payment completed (test mode)',
            ];
        }

        $apiKey = $this->getApiKey();

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                ])->get("{$this->baseUrl}/order-status", [
                    'order_id' => $orderId,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['data'][0])) {
                    $paymentData = $data['data'][0];

                    return [
                        'success' => true,
                        'order_id' => $paymentData['order_id'] ?? $orderId,
                        'payment_status' => $paymentData['payment_status'] ?? 'pending',
                        'reference' => $paymentData['reference'] ?? null,
                        'transid' => $paymentData['transid'] ?? null,
                        'channel' => $paymentData['channel'] ?? null,
                        'amount' => $paymentData['amount'] ?? null,
                    ];
                }
            }

            Log::error('ZenoPay Status Check Error', [
                'order_id' => $orderId,
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to check payment status',
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ZenoPay Status Check Connection Error', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Unable to connect to ZenoPay service',
            ];
        } catch (\Exception $e) {
            Log::error('ZenoPay Status Check Exception', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while checking payment status',
            ];
        }
    }

    /**
     * Handle ZenoPay webhook notification.
     */
    public function handleWebhook(array $webhookData): array
    {
        // Parse webhook data
        // Verify the webhook (if ZenoPay provides signature verification)
        // Update payment status
        // Activate subscription if payment completed

        $orderId = $webhookData['order_id'] ?? null;
        $status = $webhookData['payment_status'] ?? null;

        if (! $orderId || ! $status) {
            return [
                'success' => false,
                'error' => 'Invalid webhook data',
            ];
        }

        // Find payment by order_id
        $payment = Payment::where('order_id', $orderId)->first();

        if (! $payment) {
            return [
                'success' => false,
                'error' => 'Payment not found',
            ];
        }

        // Update payment status
        if ($status === 'COMPLETED') {
            $payment->update([
                'status' => 'completed',
                'reference' => $webhookData['reference'] ?? $payment->reference,
                'tx_ref' => $webhookData['transid'] ?? $payment->tx_ref,
            ]);

            // Activate subscription
            if ($payment->subscription) {
                $payment->subscription->update(['status' => 'active']);
            }

            return [
                'success' => true,
                'message' => 'Payment completed and subscription activated',
            ];
        }

        return [
            'success' => true,
            'message' => 'Webhook processed',
        ];
    }

    /**
     * Format phone number for ZenoPay (Tanzania format).
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If it starts with 255, keep it
        if (str_starts_with($phone, '255')) {
            return $phone;
        }

        // If it starts with 0, replace with 255
        if (str_starts_with($phone, '0')) {
            return '255'.substr($phone, 1);
        }

        // If it's 10 digits, assume it's missing country code
        if (strlen($phone) === 10) {
            return '255'.$phone;
        }

        // Otherwise return as is
        return $phone;
    }
}
