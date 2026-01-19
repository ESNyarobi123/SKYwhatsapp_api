<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Get user's subscriptions.
     */
    public function index(Request $request): JsonResponse
    {
        $subscriptions = $request->user()->subscriptions()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'subscriptions' => $subscriptions,
            ],
        ]);
    }

    /**
     * Get active subscription.
     */
    public function active(Request $request): JsonResponse
    {
        $subscription = $request->user()->activeSubscription;

        if (! $subscription) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NO_ACTIVE_SUBSCRIPTION',
                    'message' => 'No active subscription found.',
                ],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'subscription' => $subscription,
            ],
        ]);
    }

    /**
     * Get subscription details.
     */
    public function show(Request $request, Subscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this subscription.',
                ],
            ], 403);
        }

        $subscription->load('payments');

        return response()->json([
            'success' => true,
            'data' => [
                'subscription' => $subscription,
            ],
        ]);
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(Request $request, Subscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this subscription.',
                ],
            ], 403);
        }

        $subscription->update(['status' => 'cancelled']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully.',
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Subscribe to a package.
     */
    public function subscribeToPackage(Request $request, Package $package): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        // Check if package is active
        if (! $package->isActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'PACKAGE_NOT_ACTIVE',
                        'message' => 'This package is not available.',
                    ],
                ], 400);
            }

            return redirect()->route('dashboard')->with('error', 'This package is not available.');
        }

        // Don't cancel trial subscription yet - keep it active until payment completes
        // The trial will be expired when payment completes (handled in PaymentController)
        // This ensures user has an active subscription while payment is pending

        // Calculate expiration date
        $expiresAt = now()->addDays($package->duration_days);

        // Use USD price as default (base currency)
        $amount = $package->getPriceForCurrency('USD');

        // Create subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'plan_name' => $package->name,
            'status' => $package->isFree() ? 'active' : 'pending',
            'amount' => $amount, // Default to USD price
            'expires_at' => $expiresAt,
            'renewal_date' => $expiresAt->copy()->subDays(7),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['subscription' => $subscription],
                'message' => $package->isFree() ? 'Free trial started successfully!' : 'Subscription created. Please complete payment to activate.',
            ], 201);
        }

        if ($package->isFree()) {
            return redirect()->route('dashboard')->with('success', 'Free trial started successfully! You can now create instances and API keys.');
        }

        // For paid packages, check if any payment methods are enabled
        $enabledMethods = PaymentMethod::getEnabledMethods();

        if ($enabledMethods->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'No payment methods are currently enabled. Please contact administrator.');
        }

        // Redirect to payment selection page
        return redirect()->route('dashboard.payment.select', ['subscription' => $subscription->id])
            ->with('info', 'Subscription created. Please complete payment to activate.');
    }
}
