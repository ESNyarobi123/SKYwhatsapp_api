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
            \Log::error('Send Message Error: Instance not found', [
                'instance_id' => $request->instance_id,
                'user_id' => $user->id
            ]);
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INSTANCE_NOT_FOUND',
                    'message' => 'Instance not found or you do not own it.',
                ],
            ], 404);
        }

        if (! $instance->isConnected()) {
            \Log::error('Send Message Error: Instance not connected', [
                'instance_id' => $instance->id,
                'status' => $instance->status
            ]);
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
            'message' => 'Message queued for sending (pending).',
        ], 201);
    }

    /**
     * Get message history.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Debug: Check total messages for user
        $totalMessages = $user->messages()->count();
        \Log::info('Total messages for user '.$user->id.': '.$totalMessages);
        
        $query = $user->messages()->with('instance')->latest();

        if ($request->has('instance_id')) {
            $query->where('instance_id', $request->instance_id);
        }

        if ($request->has('direction')) {
            $query->where('direction', $request->direction);
        }

        // Count before filtering groups
        $beforeFilterCount = $query->count();
        \Log::info('Messages before group filter: '.$beforeFilterCount);

        // Filter out group messages - only show private messages
        // Groups have @g.us suffix (always groups)
        // @lid can be private messages (WhatsApp service already filters groups before sending)
        // Only filter @g.us, allow @lid as WhatsApp service handles group filtering
        $query->where(function ($q) {
            $q->where(function ($subQ) {
                // from field should not contain @g.us (always groups)
                $subQ->where('from', 'not like', '%@g.us');
            })->where(function ($subQ) {
                // to field should not contain @g.us (always groups)
                $subQ->where('to', 'not like', '%@g.us');
            });
        });

        // Count after filtering groups
        $afterFilterCount = $query->count();
        \Log::info('Messages after group filter: '.$afterFilterCount);

        $messages = $query->paginate($request->get('per_page', 50));
        $instances = $user->instances()->get();
        
        \Log::info('Final messages count: '.$messages->total());

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
