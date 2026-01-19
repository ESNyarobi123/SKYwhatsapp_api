<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Models\Message;
use App\Services\InstanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InternalController extends Controller
{
    public function __construct(
        private InstanceService $instanceService
    ) {}

    /**
     * Store QR code for an instance.
     */
    public function storeQrCode(Request $request, Instance $instance): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ],
            ], 422);
        }

        $ttlMinutes = 5;
        if ($request->has('expires_at')) {
            $ttlMinutes = now()->diffInMinutes($request->expires_at);
        }

        $this->instanceService->storeQrCode(
            $instance,
            $request->qr_code,
            max(1, $ttlMinutes)
        );

        return response()->json([
            'success' => true,
            'message' => 'QR code stored successfully.',
        ]);
    }

    /**
     * Update instance status.
     */
    public function updateStatus(Request $request, Instance $instance): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:connecting,connected,disconnected',
            'phone_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ],
            ], 422);
        }

        $updateData = ['status' => $request->status];

        if ($request->has('phone_number')) {
            $updateData['phone_number'] = $request->phone_number;
        }

        if ($request->status === 'connected') {
            $updateData['last_connected_at'] = now();
        }

        $instance->update($updateData);

        return response()->json([
            'success' => true,
            'data' => [
                'instance' => $instance->fresh(),
            ],
            'message' => 'Instance status updated successfully.',
        ]);
    }

    /**
     * Store session data for an instance.
     */
    public function storeSession(Request $request, Instance $instance): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ],
            ], 422);
        }

        $instance->update([
            'session_data' => $request->session_data,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session data stored successfully.',
        ]);
    }

    /**
     * Store a received message.
     */
    public function storeMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'instance_id' => 'required|exists:instances,id',
            'from' => 'required|string',
            'to' => 'required|string',
            'message' => 'required|string',
            'message_id' => 'nullable|string',
            'timestamp' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ],
            ], 422);
        }

        $instance = Instance::findOrFail($request->instance_id);

        $message = Message::create([
            'user_id' => $instance->user_id,
            'instance_id' => $request->instance_id,
            'from' => $request->from,
            'to' => $request->to,
            'body' => $request->message,
            'message_id' => $request->message_id,
            'created_at' => $request->timestamp ? now()->parse($request->timestamp) : now(),
            'metadata' => $request->metadata ?? [],
            'direction' => 'inbound',
            'status' => 'delivered',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $message,
            ],
            'message' => 'Message stored successfully.',
        ], 201);
    }

    /**
     * Get all instances (for polling status changes).
     */
    public function getAllInstances(Request $request): JsonResponse
    {
        $instances = Instance::whereIn('status', ['connecting', 'connected', 'disconnected'])
            ->get()
            ->map(function ($instance) {
                return [
                    'id' => $instance->id,
                    'user_id' => $instance->user_id,
                    'name' => $instance->name,
                    'phone_number' => $instance->phone_number,
                    'status' => $instance->status,
                    'session_data' => $instance->session_data,
                    'created_at' => $instance->created_at,
                    'updated_at' => $instance->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'instances' => $instances,
            ],
        ]);
    }

    /**
     * Get pending connections (instances with status "connecting").
     */
    public function getPendingConnections(Request $request): JsonResponse
    {
        $instances = Instance::where('status', 'connecting')
            ->where(function ($query) {
                $query->whereNull('qr_code')
                    ->orWhere('qr_expires_at', '<', now());
            })
            ->get()
            ->map(function ($instance) {
                // Format instance data for Node.js service
                return [
                    'id' => $instance->id,
                    'user_id' => $instance->user_id,
                    'name' => $instance->name,
                    'phone_number' => $instance->phone_number,
                    'status' => $instance->status,
                    'session_data' => $instance->session_data, // Will be null for new connections
                    'created_at' => $instance->created_at,
                    'updated_at' => $instance->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'instances' => $instances,
            ],
        ]);
    }

    /**
     * Get instance details for connection.
     */
    public function getInstanceForConnection(Instance $instance): JsonResponse
    {
        if ($instance->status !== 'connecting') {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INSTANCE_NOT_CONNECTING',
                    'message' => 'Instance is not in connecting state.',
                ],
            ], 400);
        }

        // Prepare instance data for Node.js service
        // Only include session_data if it exists (for reconnection scenarios)
        $instanceData = [
            'id' => $instance->id,
            'user_id' => $instance->user_id,
            'name' => $instance->name,
            'phone_number' => $instance->phone_number,
            'status' => $instance->status,
            'session_data' => $instance->session_data, // Will be null for new connections, which is fine
            'created_at' => $instance->created_at,
            'updated_at' => $instance->updated_at,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'instance' => $instanceData,
            ],
        ]);
    }

    /**
     * Get pending messages (outbound messages with status 'sent' that need to be processed).
     */
    public function getPendingMessages(Request $request): JsonResponse
    {
        $messages = Message::where('direction', 'outbound')
            ->where('status', 'sent')
            ->whereHas('instance', function ($query) {
                $query->where('status', 'connected');
            })
            ->with(['instance', 'user'])
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages,
            ],
        ]);
    }
}
