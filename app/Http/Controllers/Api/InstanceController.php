<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateInstanceRequest;
use App\Models\Instance;
use App\Services\InstanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstanceController extends Controller
{
    public function __construct(
        private InstanceService $instanceService
    ) {}

    /**
     * List all instances for the authenticated user.
     */
    public function index(Request $request)
    {
        $instances = $request->user()->instances()->latest()->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'instances' => $instances,
                ],
            ]);
        }

        return view('dashboard.instances.index', compact('instances'));
    }

    /**
     * Create a new instance.
     */
    public function store(CreateInstanceRequest $request)
    {
        $user = $request->user();

        // Check if user has active subscription
        if (! $user->hasActiveSubscription()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'SUBSCRIPTION_REQUIRED',
                        'message' => 'Active subscription is required to create instances. Please select a package first.',
                    ],
                ], 403);
            }

            return redirect()->route('dashboard.instances')
                ->with('error', 'Active subscription is required to create instances. Please select a package first.');
        }

        // Check if user can create more instances
        if (! $user->canCreateInstance()) {
            $featureLimitService = app(\App\Services\FeatureLimitService::class);
            $usage = $featureLimitService->getFeatureUsageStats($user);
            $instanceUsage = $usage['instances'] ?? null;
            
            $message = 'Instance limit reached for your plan.';
            if ($instanceUsage && $instanceUsage['limit'] !== null) {
                $message = "Instance limit reached ({$instanceUsage['usage']}/{$instanceUsage['limit']}). Please upgrade your plan to create more instances.";
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'INSTANCE_LIMIT_REACHED',
                        'message' => $message,
                    ],
                ], 403);
            }

            return redirect()->route('dashboard.instances')->with('error', $message);
        }

        $instance = $this->instanceService->create(
            $user,
            $request->name,
            $request->phone_number
        );

        // Increment instance usage after successful creation
        $featureLimitService = app(\App\Services\FeatureLimitService::class);
        $featureLimitService->incrementUsage($user, 'instances');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'instance' => $instance,
                ],
                'message' => 'Instance created successfully.',
            ], 201);
        }

        return redirect()->route('dashboard.instances')
            ->with('success', 'Instance created successfully.');
    }

    /**
     * Get instance details.
     */
    public function show(Request $request, Instance $instance): JsonResponse
    {
        if ($instance->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this instance.',
                ],
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'instance' => $instance,
            ],
        ]);
    }

    /**
     * Get QR code for instance (if connecting).
     */
    public function qr(Request $request, Instance $instance): JsonResponse
    {
        if ($instance->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this instance.',
                ],
            ], 403);
        }

        if ($instance->status === 'connected') {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ALREADY_CONNECTED',
                    'message' => 'Instance is already connected.',
                ],
            ], 400);
        }

        if (! $instance->qr_code || $instance->isQrExpired()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'QR_NOT_AVAILABLE',
                    'message' => 'QR code is not available. Please trigger connect first.',
                ],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'qr_code' => $instance->qr_code,
                'expires_at' => $instance->qr_expires_at,
            ],
        ]);
    }

    /**
     * Trigger QR generation (for Node.js service to handle).
     */
    public function connect(Request $request, Instance $instance): JsonResponse
    {
        if ($instance->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this instance.',
                ],
            ], 403);
        }

        if ($instance->status === 'connected') {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ALREADY_CONNECTED',
                    'message' => 'Instance is already connected.',
                ],
            ], 400);
        }

        $this->instanceService->updateStatus($instance, 'connecting');

        // Notify Node.js service about connection request (optional optimization)
        // Node.js service will also poll for pending connections
        $this->instanceService->notifyConnectionRequest($instance->fresh());

        return response()->json([
            'success' => true,
            'data' => [
                'instance' => $instance->fresh(),
            ],
            'message' => 'Instance connection initiated. QR code will be available shortly.',
        ]);
    }

    /**
     * Delete an instance.
     */
    public function destroy(Request $request, Instance $instance): JsonResponse
    {
        if ($instance->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this instance.',
                ],
            ], 403);
        }

        $instance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Instance deleted successfully.',
        ]);
    }
}
