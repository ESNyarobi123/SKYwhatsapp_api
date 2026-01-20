<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreatePackageRequest;
use App\Http\Requests\Admin\UpdatePackageRequest;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * List all packages.
     */
    public function index(Request $request)
    {
        $query = Package::latest('sort_order')->withCount('subscriptions');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $packages = $query->paginate($request->get('per_page', 15));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'packages' => $packages->items(),
                    'pagination' => [
                        'current_page' => $packages->currentPage(),
                        'last_page' => $packages->lastPage(),
                        'per_page' => $packages->perPage(),
                        'total' => $packages->total(),
                    ],
                ],
            ]);
        }

        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show create package form.
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Store a new package.
     */
    public function store(CreatePackageRequest $request)
    {
        $validated = $request->validated();

        // Ensure currency is USD and price_usd is set
        $validated['currency'] = 'USD';
        $validated['price_usd'] = $validated['price'] ?? 0;

        // Convert feature inputs to structured JSON
        $validated['features'] = $this->buildFeaturesArray($request);

        $package = Package::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['package' => $package],
                'message' => 'Package created successfully.',
            ], 201);
        }

        return redirect()->route('admin.packages.index')->with('success', 'Package created successfully.');
    }

    /**
     * Show package details.
     */
    public function show(Request $request, Package $package)
    {
        $package->loadCount('subscriptions');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['package' => $package],
            ]);
        }

        return view('admin.packages.show', compact('package'));
    }

    /**
     * Show edit package form.
     */
    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Update a package.
     */
    public function update(UpdatePackageRequest $request, Package $package)
    {
        $validated = $request->validated();

        // Ensure currency is USD and price_usd is updated
        $validated['currency'] = 'USD';
        if (isset($validated['price'])) {
            $validated['price_usd'] = $validated['price'];
        }

        // Convert feature inputs to structured JSON
        $validated['features'] = $this->buildFeaturesArray($request);

        $package->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['package' => $package->fresh()],
                'message' => 'Package updated successfully.',
            ]);
        }

        return redirect()->route('admin.packages.index')->with('success', 'Package updated successfully.');
    }

    /**
     * Delete a package.
     */
    public function destroy(Request $request, Package $package)
    {
        $subscriptionsCount = $package->subscriptions()->count();
        
        if ($subscriptionsCount > 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'HAS_SUBSCRIPTIONS',
                        'message' => "Cannot delete package with {$subscriptionsCount} subscription(s) associated.",
                    ],
                ], 400);
            }

            return back()->withErrors(['package' => "Hauwezi kufuta package hii kwa sababu ina subscriptions {$subscriptionsCount}. Tafadhali futa subscriptions kwanza."]);
        }

        $package->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Package deleted successfully.',
            ]);
        }

        return redirect()->route('admin.packages.index')->with('success', 'Package deleted successfully.');
    }

    /**
     * Build structured features array from form inputs.
     */
    private function buildFeaturesArray(Request $request): array
    {
        $features = [];

        // Instances
        if ($request->has('feature_instances_limit') && $request->feature_instances_limit !== '' && $request->feature_instances_limit !== null) {
            $features['instances'] = [
                'limit' => (int) $request->feature_instances_limit,
                'period' => $request->feature_instances_period ?? 'lifetime',
            ];
        }

        // Messages
        if ($request->has('feature_messages_limit') && $request->feature_messages_limit !== '' && $request->feature_messages_limit !== null) {
            $features['messages'] = [
                'limit' => (int) $request->feature_messages_limit,
                'period' => $request->feature_messages_period ?? 'day',
            ];
        }

        // API Calls
        if ($request->has('feature_api_calls_limit') && $request->feature_api_calls_limit !== '' && $request->feature_api_calls_limit !== null) {
            $features['api_calls'] = [
                'limit' => (int) $request->feature_api_calls_limit,
                'period' => $request->feature_api_calls_period ?? 'month',
            ];
        }

        // API Keys
        if ($request->has('feature_api_keys_limit') && $request->feature_api_keys_limit !== '' && $request->feature_api_keys_limit !== null) {
            $features['api_keys'] = [
                'limit' => (int) $request->feature_api_keys_limit,
                'period' => $request->feature_api_keys_period ?? 'lifetime',
            ];
        }

        // Bot Builder Features
        $features['bot_type'] = $request->input('bot_type', 'simple');

        // Bot Rules
        if ($request->has('bot_rules_limit') && $request->bot_rules_limit !== '' && $request->bot_rules_limit !== null) {
            $features['bot_rules'] = [
                'limit' => (int) $request->bot_rules_limit,
                'period' => $request->bot_rules_period ?? 'lifetime',
            ];
        }

        // Bot Feature Toggles
        $features['bot_menus'] = $request->has('bot_menus') && $request->bot_menus;
        $features['bot_buttons'] = $request->has('bot_buttons') && $request->bot_buttons;
        $features['bot_analytics'] = $request->has('bot_analytics') && $request->bot_analytics;
        $features['priority_support'] = $request->has('priority_support') && $request->priority_support;

        return $features;
    }
}
