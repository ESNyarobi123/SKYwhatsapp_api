<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Services\Payment\MpesaService;
use App\Services\Payment\PayPalService;
use App\Services\Payment\Trc20Service;
use App\Services\Payment\ZenoPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        private MpesaService $mpesaService,
        private ZenoPayService $zenoPayService,
        private PayPalService $payPalService,
        private Trc20Service $trc20Service
    ) {}

    /**
     * Initiate payment for subscription.
     */
    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'provider' => ['required', 'in:mpesa,tigopesa,airtelmoney,stripe'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'phone_number' => ['required_if:provider,mpesa,tigopesa,airtelmoney', 'string', 'max:20'],
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $subscription) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SUBSCRIPTION_NOT_FOUND',
                    'message' => 'Subscription not found or you do not own it.',
                ],
            ], 404);
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'subscription_id' => $subscription->id,
            'amount' => $request->amount,
            'currency' => 'TZS',
            'provider' => $request->provider,
            'status' => 'pending',
        ]);

        // Initiate payment based on provider
        if ($request->provider === 'mpesa') {
            $result = $this->mpesaService->initiatePayment(
                $request->user(),
                $subscription,
                $request->amount,
                $request->phone_number
            );

            $payment->update([
                'reference' => $result['reference'],
                'metadata' => $result,
            ]);
        }

        // Other providers (TigoPesa, AirtelMoney, Stripe) would follow similar pattern

        return response()->json([
            'success' => true,
            'data' => [
                'payment' => [
                    'id' => $payment->id,
                    'reference' => $payment->reference,
                    'status' => $payment->status,
                    'amount' => $payment->amount,
                    'provider' => $payment->provider,
                ],
            ],
            'message' => 'Payment initiated successfully.',
        ], 201);
    }

    /**
     * Get payment details.
     */
    public function show(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this payment.',
                ],
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'payment' => $payment->load('subscription'),
            ],
        ]);
    }

    /**
     * Handle payment webhook/callback from providers.
     */
    public function webhook(Request $request, string $provider): JsonResponse
    {
        // Route to appropriate handler based on provider
        if ($provider === 'zenopay') {
            return $this->webhookZenoPay($request);
        }

        // For now, return acknowledgment
        return response()->json([
            'success' => true,
            'message' => 'Webhook received',
        ]);
    }

    /**
     * Handle ZenoPay webhook notification.
     */
    public function webhookZenoPay(Request $request): JsonResponse
    {
        $webhookData = $request->all();

        $result = $this->zenoPayService->handleWebhook($webhookData);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Webhook processed successfully',
            ]);
        }

        Log::error('ZenoPay Webhook Error', [
            'webhook_data' => $webhookData,
            'error' => $result['error'] ?? 'Unknown error',
        ]);

        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Failed to process webhook',
        ], 400);
    }

    /**
     * Show payment method selection page.
     */
    public function select(Request $request, Subscription $subscription)
    {
        if ($subscription->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        if ($subscription->status === 'active') {
            return redirect()->route('dashboard')->with('info', 'This subscription is already active.');
        }

        $enabledMethods = PaymentMethod::getEnabledMethods();

        if ($enabledMethods->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'No payment methods are currently enabled.');
        }

        return view('dashboard.payment.select', compact('subscription', 'enabledMethods'));
    }

    /**
     * Show ZenoPay card payment form.
     */
    public function showZenoPayCardForm(Request $request, Subscription $subscription)
    {
        if ($subscription->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        if ($subscription->status === 'active') {
            return redirect()->route('dashboard')->with('info', 'This subscription is already active.');
        }

        // Check if API key is configured - check both getValue and direct query
        $zenopayApiKey = \App\Models\Setting::getValue('zenopay_api_key');
        $setting = \App\Models\Setting::where('key', 'zenopay_api_key')->first();

        $isApiKeyConfigured = false;
        if ($setting && $setting->is_active && ! empty(trim($setting->value ?? ''))) {
            $isApiKeyConfigured = true;
        } elseif ($zenopayApiKey && ! empty(trim($zenopayApiKey))) {
            $isApiKeyConfigured = true;
        }

        return view('dashboard.payment.zenopay-card', compact('subscription', 'isApiKeyConfigured'));
    }

    /**
     * Handle legacy mobile payment route - determines if ID is payment or subscription.
     */
    public function handleMobilePaymentRoute(Request $request, int $id): RedirectResponse
    {
        // First check if it's a Payment
        $payment = Payment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->where('payment_method', 'zenopay_mobile')
            ->first();

        if ($payment) {
            return redirect()->route('dashboard.payment.zenopay.mobile.show', ['payment' => $payment->id]);
        }

        // If not a payment, check if it's a Subscription
        $subscription = Subscription::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($subscription) {
            return redirect()->route('dashboard.payment.zenopay.mobile.form', ['subscription' => $subscription->id]);
        }

        return redirect()->route('dashboard')->with('error', 'Payment or subscription not found.');
    }

    /**
     * Show ZenoPay mobile money payment form.
     */
    public function showZenoPayMobileForm(Request $request, Subscription $subscription)
    {
        if ($subscription->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        if ($subscription->status === 'active') {
            return redirect()->route('dashboard')->with('info', 'This subscription is already active.');
        }

        return view('dashboard.payment.zenopay-mobile-form', compact('subscription'));
    }

    /**
     * Show PayPal payment form.
     */
    public function showPayPalForm(Request $request, Subscription $subscription)
    {
        if ($subscription->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        if ($subscription->status === 'active') {
            return redirect()->route('dashboard')->with('info', 'This subscription is already active.');
        }

        $paypalEmail = \App\Models\Setting::getValue('paypal_email');

        return view('dashboard.payment.paypal-form', compact('subscription', 'paypalEmail'));
    }

    /**
     * Show TRC20 payment form.
     */
    public function showTrc20Form(Request $request, Subscription $subscription)
    {
        if ($subscription->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        if ($subscription->status === 'active') {
            return redirect()->route('dashboard')->with('info', 'This subscription is already active.');
        }

        return view('dashboard.payment.trc20-form', compact('subscription'));
    }

    /**
     * Initiate ZenoPay card payment.
     */
    public function initiateZenoPayCard(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $subscription) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Subscription not found.',
                ], 404);
            }

            return redirect()->route('dashboard')->with('error', 'Subscription not found.');
        }

        // Get currency and amount for ZenoPay Card (uses USD)
        $package = $subscription->package;
        $currency = $package ? $package->getCurrencyForPaymentMethod('zenopay_card') : 'USD';
        $amount = $package ? $package->getPriceForCurrency($currency) : $subscription->amount;

        $result = $this->zenoPayService->initiateCardPayment(
            (float) $amount,
            $currency,
            $request->user(),
            $subscription
        );

        if (! $result['success']) {
            $errorMessage = $result['error'] ?? 'Failed to initiate payment.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage,
                ], 400);
            }

            return redirect()->route('dashboard.payment.zenopay.card.form', ['subscription' => $subscription->id])
                ->with('error', $errorMessage);
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'subscription_id' => $subscription->id,
            'amount' => $amount,
            'currency' => $currency,
            'provider' => 'stripe', // Keep for compatibility
            'payment_method' => 'zenopay_card',
            'reference' => $result['tx_ref'] ?? $result['order_id'] ?? 'ZENOPAY_CARD_'.Str::random(16),
            'order_id' => $result['order_id'] ?? null,
            'tx_ref' => $result['tx_ref'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'payment_link' => $result['payment_link'],
                    'payment' => $payment,
                ],
            ]);
        }

        // Redirect to ZenoPay checkout
        return redirect($result['payment_link']);
    }

    /**
     * Initiate ZenoPay mobile money payment.
     */
    public function initiateZenoPayMobile(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'phone_number' => ['required', 'string', 'max:20'],
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $subscription) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Subscription not found.',
                ], 404);
            }

            return redirect()->route('dashboard')->with('error', 'Subscription not found.');
        }

        // Get currency and amount for ZenoPay Mobile (uses TZS)
        $package = $subscription->package;
        $currency = $package ? $package->getCurrencyForPaymentMethod('zenopay_mobile') : 'TZS';
        $amount = $package ? $package->getPriceForCurrency($currency) : $subscription->amount;

        $result = $this->zenoPayService->initiateMobileMoneyPayment(
            (float) $amount,
            $request->user(),
            $subscription,
            $request->phone_number
        );

        if (! $result['success']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Failed to initiate payment.',
                ], 400);
            }

            return redirect()->route('dashboard.payment.select', ['subscription' => $subscription->id])
                ->with('error', $result['error'] ?? 'Failed to initiate payment.');
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'subscription_id' => $subscription->id,
            'amount' => $amount,
            'currency' => $currency,
            'provider' => 'mpesa', // Keep for compatibility
            'payment_method' => 'zenopay_mobile',
            'reference' => $result['order_id'] ?? 'ZENOPAY_MOBILE_'.Str::random(16),
            'order_id' => $result['order_id'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'payment' => $payment,
                    'message' => $result['message'] ?? 'Payment initiated',
                ],
            ]);
        }

        return redirect()->route('dashboard.payment.zenopay.mobile.show', ['payment' => $payment->id])
            ->with('success', $result['message'] ?? 'Payment initiated. Please check your phone for payment prompt.');
    }

    /**
     * Initiate PayPal payment.
     */
    public function initiatePayPal(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $subscription) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Subscription not found.',
                ], 404);
            }

            return redirect()->route('dashboard')->with('error', 'Subscription not found.');
        }

        try {
            // Get currency and amount for PayPal (uses USD)
            $package = $subscription->package;
            $currency = $package ? $package->getCurrencyForPaymentMethod('paypal') : 'USD';
            $amount = $package ? $package->getPriceForCurrency($currency) : $subscription->amount;

            $paymentLink = $this->payPalService->generatePaymentLink(
                (float) $amount,
                $currency,
                $request->user(),
                $subscription
            );

            $paymentReference = $this->payPalService->generatePaymentReference($request->user(), $subscription);

            // Create payment record
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'subscription_id' => $subscription->id,
                'amount' => $amount,
                'currency' => $currency,
                'provider' => 'stripe', // Keep for compatibility
                'payment_method' => 'paypal',
                'reference' => $paymentReference,
                'status' => 'pending',
                'verification_status' => 'pending',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'payment_link' => $paymentLink,
                        'payment' => $payment,
                    ],
                ]);
            }

            return redirect()->route('dashboard.payment.paypal.show', ['payment' => $payment->id])
                ->with('payment_link', $paymentLink);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 400);
            }

            return redirect()->route('dashboard.payment.select', ['subscription' => $subscription->id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Initiate TRC20 payment.
     */
    public function initiateTrc20(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $subscription) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Subscription not found.',
                ], 404);
            }

            return redirect()->route('dashboard')->with('error', 'Subscription not found.');
        }

        try {
            // Get currency and amount for TRC20 (uses USDT)
            $package = $subscription->package;
            $currency = $package ? $package->getCurrencyForPaymentMethod('trc20') : 'USD';
            $amount = $package ? $package->getPriceForCurrency('USDT') : $subscription->amount;

            $instructions = $this->trc20Service->generatePaymentInstructions(
                (float) $amount,
                $subscription
            );

            // Create payment record
            // Note: Store cryptocurrency type (USDT) in metadata, use USD for currency column (USDT not valid for 3-char limit)
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'subscription_id' => $subscription->id,
                'amount' => $amount,
                'currency' => 'USD', // Use USD for currency column (USDT stored in metadata)
                'provider' => 'stripe', // Keep for compatibility
                'payment_method' => 'trc20',
                'reference' => $instructions['payment_reference'],
                'status' => 'pending',
                'verification_status' => 'pending',
                'metadata' => [
                    'cryptocurrency' => $instructions['currency'], // USDT stored here
                    'network' => $instructions['network'],
                    'wallet_address' => $instructions['wallet_address'],
                ],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'instructions' => $instructions,
                        'payment' => $payment,
                    ],
                ]);
            }

            return redirect()->route('dashboard.payment.trc20.show', ['payment' => $payment->id])
                ->with('instructions', $instructions);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 400);
            }

            return redirect()->route('dashboard.payment.select', ['subscription' => $subscription->id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Handle ZenoPay callback after payment.
     */
    public function zenopayCallback(Request $request, Subscription $subscription): RedirectResponse
    {
        $txRef = $request->get('tx_ref');
        $status = $request->get('status');

        // Find payment by tx_ref
        $payment = Payment::where('tx_ref', $txRef)
            ->where('subscription_id', $subscription->id)
            ->first();

        if (! $payment) {
            return redirect()->route('dashboard')->with('error', 'Payment not found.');
        }

        if ($status === 'successful') {
            // Check if this is a test mode payment
            $testMode = config('app.env') === 'local' && \App\Models\Setting::getValue('zenopay_test_mode', false);
            $isTestPayment = $testMode && $payment->order_id && str_starts_with($payment->order_id, 'ZENOPAY_TEST_');

            if ($isTestPayment) {
                // Auto-complete test mode payments
                $payment->update([
                    'status' => 'completed',
                    'verification_status' => 'verified',
                ]);
                $subscription->update(['status' => 'active']);

                return redirect()->route('dashboard')->with('success', 'Payment completed successfully! Your subscription is now active.');
            }

            // Check payment status via API for real payments
            $statusResult = $this->zenoPayService->checkPaymentStatus($payment->order_id ?? '');

            if ($statusResult['success'] && ($statusResult['payment_status'] ?? '') === 'COMPLETED') {
                $payment->update(['status' => 'completed']);
                $subscription->update(['status' => 'active']);

                return redirect()->route('dashboard')->with('success', 'Payment completed successfully! Your subscription is now active.');
            }
        }

        return redirect()->route('dashboard')->with('info', 'Payment is being processed. Your subscription will be activated once payment is confirmed.');
    }

    /**
     * Show ZenoPay mobile money payment status page.
     */
    public function showZenoPayMobile(Request $request, Payment $payment)
    {
        if ($payment->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        $payment->load(['subscription', 'user']);

        return view('dashboard.payment.zenopay-mobile', compact('payment'));
    }

    /**
     * Show PayPal payment instructions page.
     */
    public function showPayPal(Request $request, Payment $payment)
    {
        if ($payment->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        $payment->load(['subscription', 'user']);
        $paymentLink = session('payment_link');
        $paypalEmail = \App\Models\Setting::getValue('paypal_email');

        return view('dashboard.payment.paypal', compact('payment', 'paymentLink', 'paypalEmail'));
    }

    /**
     * Show TRC20 payment instructions page.
     */
    public function showTrc20(Request $request, Payment $payment)
    {
        if ($payment->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        $payment->load(['subscription', 'user']);
        $instructions = session('instructions');

        if (! $instructions) {
            try {
                $instructions = $this->trc20Service->generatePaymentInstructions(
                    (float) $payment->amount,
                    $payment->subscription
                );
            } catch (\Exception $e) {
                return redirect()->route('dashboard')->with('error', $e->getMessage());
            }
        }

        return view('dashboard.payment.trc20', compact('payment', 'instructions'));
    }

    /**
     * Check payment status (for polling).
     */
    public function checkStatus(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this payment.',
                ],
            ], 403);
        }

        // If payment is already completed, return current status
        if ($payment->status === 'completed') {
            return response()->json([
                'success' => true,
                'data' => [
                    'payment' => [
                        'id' => $payment->id,
                        'status' => $payment->status,
                        'verification_status' => $payment->verification_status,
                    ],
                ],
            ]);
        }

        // Check status via ZenoPay API if it's a ZenoPay payment
        // Skip API check for test mode payments
        $testMode = config('app.env') === 'local' && \App\Models\Setting::getValue('zenopay_test_mode', false);
        $isTestPayment = $testMode && $payment->order_id && str_starts_with($payment->order_id, 'ZENOPAY_TEST_');

        if ($isTestPayment) {
            // Auto-complete test mode payments
            if ($payment->status !== 'completed') {
                $payment->update([
                    'status' => 'completed',
                    'verification_status' => 'verified',
                ]);

                // Activate subscription
                if ($payment->subscription && $payment->subscription->status === 'pending') {
                    // Expire any other active subscriptions for this user
                    Subscription::where('user_id', $payment->user_id)
                        ->where('id', '!=', $payment->subscription->id)
                        ->where('status', 'active')
                        ->update(['status' => 'expired']);

                    $package = $payment->subscription->package;
                    $durationDays = $package ? $package->duration_days : 30;

                    $payment->subscription->update([
                        'status' => 'active',
                        'expires_at' => now()->addDays($durationDays),
                        'payment_provider' => $payment->provider,
                        'payment_reference' => $payment->reference,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'payment' => [
                        'id' => $payment->id,
                        'status' => 'completed',
                        'verification_status' => 'verified',
                    ],
                ],
            ]);
        }

        if (in_array($payment->payment_method, ['zenopay_card', 'zenopay_mobile']) && $payment->order_id) {
            $statusResult = $this->zenoPayService->checkPaymentStatus($payment->order_id);

            if ($statusResult['success']) {
                $paymentStatus = $statusResult['payment_status'] ?? 'pending';

                // Update payment if status changed
                if ($paymentStatus === 'COMPLETED' && $payment->status !== 'completed') {
                    $payment->update([
                        'status' => 'completed',
                        'reference' => $statusResult['reference'] ?? $payment->reference,
                        'tx_ref' => $statusResult['transid'] ?? $payment->tx_ref,
                    ]);

                    // Activate subscription
                    if ($payment->subscription) {
                        // Expire any other active subscriptions for this user
                        Subscription::where('user_id', $payment->user_id)
                            ->where('id', '!=', $payment->subscription->id)
                            ->where('status', 'active')
                            ->update(['status' => 'expired']);

                        $package = $payment->subscription->package;
                        $durationDays = $package ? $package->duration_days : 30;

                        $payment->subscription->update([
                            'status' => 'active',
                            'expires_at' => now()->addDays($durationDays),
                            'payment_provider' => $payment->provider,
                            'payment_reference' => $payment->reference,
                        ]);
                    }
                } elseif (in_array($paymentStatus, ['FAILED', 'CANCELLED'])) {
                    $payment->update([
                        'status' => $paymentStatus === 'FAILED' ? 'failed' : 'cancelled',
                    ]);

                    // If payment fails, keep the pending subscription but don't activate it
                    // The user's existing active subscription (trial) will remain active
                    // Optionally, we could cancel the pending subscription here, but keeping it
                    // allows user to retry payment without creating a new subscription
                }
            }
        }

        $payment->refresh();

        return response()->json([
            'success' => true,
            'data' => [
                'payment' => [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'verification_status' => $payment->verification_status,
                ],
            ],
        ]);
    }

    /**
     * Show user's payment/order history.
     */
    public function orders(Request $request)
    {
        $payments = $request->user()->payments()
            ->with('subscription.package')
            ->latest()
            ->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'payments' => $payments->items(),
                    'pagination' => [
                        'current_page' => $payments->currentPage(),
                        'last_page' => $payments->lastPage(),
                        'per_page' => $payments->perPage(),
                        'total' => $payments->total(),
                    ],
                ],
            ]);
        }

        return view('dashboard.orders', compact('payments'));
    }
}
