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
        $user = $request->user();
        
        // If user is part of a team, show team instances
        if ($user->current_team_id) {
            // Assuming instances are linked to the team owner for now, 
            // or we need to check if instances table has team_id.
            // Based on current schema, instances belong to user_id.
            // So we should show instances of the team owner.
            $team = $user->currentTeam;
            if ($team) {
                $instances = Instance::where('user_id', $team->owner_id)->latest()->get();
            } else {
                $instances = $user->instances()->latest()->get();
            }
        } else {
            $instances = $user->instances()->latest()->get();
        }

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
     * Stop an instance (disconnect but keep session data).
     */
    public function stop(Request $request, Instance $instance): JsonResponse
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

        if ($instance->status === 'disconnected') {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ALREADY_STOPPED',
                    'message' => 'Instance is already stopped.',
                ],
            ], 400);
        }

        // Update status to disconnected but keep session_data
        // This allows reconnection without losing session
        $this->instanceService->updateStatus($instance, 'disconnected');
        
        // Clear QR code but keep session_data
        $instance->update([
            'qr_code' => null,
            'qr_expires_at' => null,
        ]);

        // Notify Node.js service to stop this instance
        $this->instanceService->notifyInstanceStop($instance->fresh());

        return response()->json([
            'success' => true,
            'data' => [
                'instance' => $instance->fresh(),
            ],
            'message' => 'Instance stopped successfully. Messages are preserved.',
        ]);
    }

    /**
     * Start an instance (reconnect using existing session or generate new QR).
     */
    public function start(Request $request, Instance $instance): JsonResponse
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

        // If instance has session_data, try to reconnect
        // Otherwise, generate new QR code
        if ($instance->session_data) {
            // Try to reconnect with existing session
            $this->instanceService->updateStatus($instance, 'connecting');
            $this->instanceService->notifyInstanceStart($instance->fresh());
        } else {
            // No session data, need new QR code
            $this->instanceService->updateStatus($instance, 'connecting');
            $this->instanceService->notifyConnectionRequest($instance->fresh());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'instance' => $instance->fresh(),
            ],
            'message' => $instance->session_data 
                ? 'Instance starting with existing session...' 
                : 'Instance connection initiated. QR code will be available shortly.',
        ]);
    }

    /**
     * Restart an instance (stop then start).
     */
    public function restart(Request $request, Instance $instance): JsonResponse
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

        // Refresh instance to get latest data
        $instance->refresh();
        
        // Store session_data status BEFORE stop (to preserve it)
        $hasSession = !empty($instance->session_data);

        // Stop first (but preserve session_data - it's NOT cleared)
        if ($instance->status !== 'disconnected') {
            $this->instanceService->updateStatus($instance, 'disconnected');
            // Clear QR code but KEEP session_data (don't touch it!)
            $instance->update([
                'qr_code' => null,
                'qr_expires_at' => null,
                // session_data is NOT in the update array - it stays!
            ]);
            $this->instanceService->notifyInstanceStop($instance->fresh());
        }

        // Small delay to ensure stop is processed
        usleep(500000); // 0.5 seconds

        // Refresh instance again to get latest session_data
        $instance->refresh();

        // Then start
        if ($hasSession && $instance->session_data) {
            // Has session - reconnect without QR
            $this->instanceService->updateStatus($instance, 'connecting');
            $this->instanceService->notifyInstanceStart($instance->fresh());
            
            return response()->json([
                'success' => true,
                'data' => [
                    'instance' => $instance->fresh(),
                ],
                'message' => 'Instance restart initiated. Reconnecting with existing session...',
            ]);
        } else {
            // No session - need new QR code
            $this->instanceService->updateStatus($instance, 'connecting');
            $this->instanceService->notifyConnectionRequest($instance->fresh());
            
            return response()->json([
                'success' => true,
                'data' => [
                    'instance' => $instance->fresh(),
                ],
                'message' => 'Instance restart initiated. QR code will be available shortly.',
            ]);
        }
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
