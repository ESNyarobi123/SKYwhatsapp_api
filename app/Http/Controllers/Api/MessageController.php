<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
use App\Models\Instance;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private MessageService $messageService
    ) {}

    /**
     * Send a message (queued for Node.js processing).
     */
    public function send(SendMessageRequest $request): JsonResponse
    {
        $user = $request->user();
        $instance = Instance::where('id', $request->instance_id)
            ->where('user_id', $user->id)
            ->first();

        if (! $instance) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INSTANCE_NOT_FOUND',
                    'message' => 'Instance not found or you do not own it.',
                ],
            ], 404);
        }

        if (! $instance->isConnected()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INSTANCE_NOT_CONNECTED',
                    'message' => 'Instance is not connected. Please connect your WhatsApp first.',
                ],
            ], 400);
        }

        // Check if user can send messages (messages per day limit)
        if (! $user->canSendMessage()) {
            $featureLimitService = app(\App\Services\FeatureLimitService::class);
            $usage = $featureLimitService->getFeatureUsageStats($user);
            $messageUsage = $usage['messages'] ?? null;

            $message = 'Message limit reached for your plan.';
            if ($messageUsage && $messageUsage['limit'] !== null) {
                $periodText = $messageUsage['period'] === 'day' ? 'today' : 'this '.$messageUsage['period'];
                $message = "Message limit reached ({$messageUsage['usage']}/{$messageUsage['limit']}) for {$periodText}. Please upgrade your plan or try again later.";
            }

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'MESSAGE_LIMIT_REACHED',
                    'message' => $message,
                ],
            ], 403);
        }

        // Handle image upload if provided
        $metadata = $request->metadata ?? [];
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('messages', 'public');
            $metadata['hasMedia'] = true;
            $metadata['mediaType'] = $image->getMimeType();
            $metadata['mediaUrl'] = asset('storage/'.$imagePath);
            $metadata['mediaPath'] = $imagePath;
        }

        $message = $this->messageService->createOutboundMessage(
            $user,
            $instance,
            $request->to,
            $request->body ?? ($request->hasFile('image') ? '[Image]' : ''),
            $metadata
        );

        // Increment message usage after successful message creation
        $featureLimitService = app(\App\Services\FeatureLimitService::class);
        $featureLimitService->incrementUsage($user, 'messages');

        // Note: In production, this would queue a job for Node.js service to process
        // For now, we just create the message record

        return response()->json([
            'success' => true,
            'data' => [
                'message' => [
                    'id' => $message->id,
                    'message_id' => $message->message_id,
                    'status' => $message->status,
                    'to' => $message->to,
                    'created_at' => $message->created_at,
                ],
            ],
            'message' => 'Message queued for sending.',
        ], 201);
    }

    /**
     * Get message history.
     */
    public function index(Request $request)
    {
        $query = $request->user()->messages()->with('instance')->latest();

        if ($request->has('instance_id')) {
            $query->where('instance_id', $request->instance_id);
        }

        if ($request->has('direction')) {
            $query->where('direction', $request->direction);
        }

        // Filter out group messages - only show private messages
        // Groups have @g.us or @lid in the JID
        // Use OR logic: message should not be from a group AND should not be to a group
        $query->where(function ($q) {
            $q->where(function ($subQ) {
                $subQ->where('from', 'not like', '%@g.us')
                    ->where('from', 'not like', '%@lid');
            })->where(function ($subQ) {
                $subQ->where('to', 'not like', '%@g.us')
                    ->where('to', 'not like', '%@lid');
            });
        });

        $messages = $query->paginate($request->get('per_page', 50));
        $instances = $request->user()->instances()->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => $messages->items(),
                    'pagination' => [
                        'current_page' => $messages->currentPage(),
                        'last_page' => $messages->lastPage(),
                        'per_page' => $messages->perPage(),
                        'total' => $messages->total(),
                    ],
                ],
            ]);
        }

        return view('dashboard.messages.index', compact('messages', 'instances'));
    }

    /**
     * Get message details.
     */
    public function show(Request $request, Message $message): JsonResponse
    {
        if ($message->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this message.',
                ],
            ], 403);
        }

        $message->load('instance');

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $message,
            ],
        ]);
    }
}
