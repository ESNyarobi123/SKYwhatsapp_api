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
    /**
     * Check if user is authorized to perform action on message/instance.
     */
    private function authorizeMessageAction($user, $permission, $instance = null)
    {
        // 1. Check direct ownership (if instance provided)
        if ($instance && $instance->user_id === $user->id) {
            return true;
        }

        // 2. Check team membership
        if ($user->current_team_id) {
            $team = $user->currentTeam;
            
            // If instance provided, it must belong to team owner
            if ($instance && $team && $instance->user_id !== $team->owner_id) {
                return false;
            }

            // Check if user has permission in this team
            if ($team) {
                $member = $team->members()->where('user_id', $user->id)->first();
                if ($member && $member->hasPermission($permission)) {
                    return true;
                }
            }
        }

        // If no team and no direct ownership (or instance not provided but checking general permission)
        // For general permission without instance, we assume self-action unless in team
        if (!$instance && !$user->current_team_id) {
            return true;
        }

        return false;
    }

    /**
     * Send a message (queued for Node.js processing).
     */
    public function send(SendMessageRequest $request): JsonResponse
    {
        $user = $request->user();
        
        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        $instance = Instance::where('id', $request->instance_id)
            ->where('user_id', $owner->id)
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

        // Check permission
        if (!$this->authorizeMessageAction($user, 'messages.send', $instance)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not have permission to send messages.',
                ],
            ], 403);
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

        // Check if OWNER can send messages (messages per day limit)
        if (! $owner->canSendMessage()) {
            $featureLimitService = app(\App\Services\FeatureLimitService::class);
            $usage = $featureLimitService->getFeatureUsageStats($owner);
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
            $owner, // Create message for OWNER
            $instance,
            $request->to,
            $request->body ?? ($request->hasFile('image') ? '[Image]' : ''),
            $metadata
        );

        // Increment message usage for OWNER
        $featureLimitService = app(\App\Services\FeatureLimitService::class);
        $featureLimitService->incrementUsage($owner, 'messages');

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
        
        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        // Check permission
        if (!$this->authorizeMessageAction($user, 'messages.view')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized');
        }
        
        $query = $owner->messages()->with('instance')->latest();

        if ($request->has('instance_id')) {
            $query->where('instance_id', $request->instance_id);
        }

        if ($request->has('direction')) {
            $query->where('direction', $request->direction);
        }

        // Filter out group messages - only show private messages
        $query->where(function ($q) {
            $q->where(function ($subQ) {
                $subQ->where('from', 'not like', '%@g.us');
            })->where(function ($subQ) {
                $subQ->where('to', 'not like', '%@g.us');
            });
        });

        $messages = $query->paginate($request->get('per_page', 50));
        
        // Get instances for the OWNER
        $instances = $owner->instances()->get();

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
        $user = $request->user();
        
        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        if ($message->user_id !== $owner->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this message.',
                ],
            ], 403);
        }

        if (!$this->authorizeMessageAction($user, 'messages.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
