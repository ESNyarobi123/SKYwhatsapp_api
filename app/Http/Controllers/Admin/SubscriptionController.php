<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * List all subscriptions with filters.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'payments']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $subscriptions = $query->latest()->paginate($request->get('per_page', 15));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'subscriptions' => $subscriptions->items(),
                    'pagination' => [
                        'current_page' => $subscriptions->currentPage(),
                        'last_page' => $subscriptions->lastPage(),
                        'per_page' => $subscriptions->perPage(),
                        'total' => $subscriptions->total(),
                    ],
                ],
            ]);
        }

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show create subscription form.
     */
    public function create()
    {
        $users = User::where('role', '!=', 'admin')->get();
        $packages = Package::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.subscriptions.create', compact('users', 'packages'));
    }

    /**
     * Store a new subscription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'plan_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expires_at' => ['required', 'date', 'after:now'],
            'payment_provider' => ['nullable', 'string', 'in:mpesa,tigopesa,airtelmoney,stripe'],
        ]);

        $subscription = Subscription::create([
            'user_id' => $validated['user_id'],
            'package_id' => $validated['package_id'] ?? null,
            'plan_name' => $validated['plan_name'],
            'status' => 'active',
            'amount' => $validated['amount'],
            'expires_at' => $validated['expires_at'],
            'renewal_date' => now()->parse($validated['expires_at'])->subDays(7)->toDateString(),
            'payment_provider' => $validated['payment_provider'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['subscription' => $subscription],
                'message' => 'Subscription created successfully.',
            ], 201);
        }

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription created successfully.');
    }

    /**
     * Get subscription details.
     */
    public function show(Request $request, Subscription $subscription)
    {
        $subscription->load(['user', 'package', 'payments']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'subscription' => $subscription,
                ],
            ]);
        }

        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show edit subscription form.
     */
    public function edit(Subscription $subscription)
    {
        $packages = Package::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.subscriptions.edit', compact('subscription', 'packages'));
    }

    /**
     * Update a subscription.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'package_id' => ['nullable', 'exists:packages,id'],
            'plan_name' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'string', 'in:active,expired,cancelled,pending'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'expires_at' => ['sometimes', 'required', 'date'],
        ]);

        $subscription->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['subscription' => $subscription->fresh()],
                'message' => 'Subscription updated successfully.',
            ]);
        }

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription updated successfully.');
    }

    /**
     * Renew a subscription.
     */
    public function renew(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'duration_days' => ['nullable', 'integer', 'min:1'],
        ]);

        $durationDays = $validated['duration_days'] ?? ($subscription->package->duration_days ?? 30);
        $newExpiresAt = now()->addDays($durationDays);

        $subscription->update([
            'status' => 'active',
            'expires_at' => $newExpiresAt,
            'renewal_date' => $newExpiresAt->copy()->subDays(7)->toDateString(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['subscription' => $subscription->fresh()],
                'message' => 'Subscription renewed successfully.',
            ]);
        }

        return back()->with('success', 'Subscription renewed successfully.');
    }

    /**
     * Delete a subscription.
     */
    public function destroy(Request $request, Subscription $subscription)
    {
        $subscription->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Subscription deleted successfully.',
            ]);
        }

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully.');
    }
}
