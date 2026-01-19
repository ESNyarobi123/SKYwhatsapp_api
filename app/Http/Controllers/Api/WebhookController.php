<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateWebhookRequest;
use App\Models\Instance;
use App\Models\Webhook;
use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        private WebhookService $webhookService
    ) {}

    /**
     * List all webhooks for the authenticated user.
     */
    public function index(Request $request)
    {
        $webhooks = $request->user()->webhooks()->with('instance')->latest()->get();
        $instances = $request->user()->instances()->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'webhooks' => $webhooks->map(fn ($webhook) => [
                        'id' => $webhook->id,
                        'url' => $webhook->url,
                        'events' => $webhook->events,
                        'instance_id' => $webhook->instance_id,
                        'is_active' => $webhook->is_active,
                        'secret' => $webhook->secret, // Show secret for signature verification
                        'created_at' => $webhook->created_at,
                    ]),
                ],
            ]);
        }

        return view('dashboard.webhooks.index', compact('webhooks', 'instances'));
    }

    /**
     * Create a new webhook.
     */
    public function store(CreateWebhookRequest $request): JsonResponse
    {
        $instance = $request->instance_id
            ? Instance::where('id', $request->instance_id)
                ->where('user_id', $request->user()->id)
                ->first()
            : null;

        if ($request->instance_id && ! $instance) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INSTANCE_NOT_FOUND',
                    'message' => 'Instance not found or you do not own it.',
                ],
            ], 404);
        }

        $webhook = $this->webhookService->create(
            $request->user(),
            $request->url,
            $request->events,
            $instance
        );

        return response()->json([
            'success' => true,
            'data' => [
                'webhook' => [
                    'id' => $webhook->id,
                    'url' => $webhook->url,
                    'events' => $webhook->events,
                    'instance_id' => $webhook->instance_id,
                    'secret' => $webhook->secret,
                    'is_active' => $webhook->is_active,
                ],
            ],
            'message' => 'Webhook created successfully. Save the secret for signature verification.',
        ], 201);
    }

    /**
     * Update a webhook.
     */
    public function update(Request $request, Webhook $webhook): JsonResponse
    {
        if ($webhook->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this webhook.',
                ],
            ], 403);
        }

        $validated = $request->validate([
            'url' => ['sometimes', 'required', 'url', 'max:512'],
            'events' => ['sometimes', 'required', 'array', 'min:1'],
            'events.*' => ['required', 'string', 'in:message.inbound,message.status,instance.connected,instance.disconnected,billing.expiring'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $webhook->update($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'webhook' => $webhook->fresh(),
            ],
            'message' => 'Webhook updated successfully.',
        ]);
    }

    /**
     * Delete a webhook.
     */
    public function destroy(Request $request, Webhook $webhook): JsonResponse
    {
        if ($webhook->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this webhook.',
                ],
            ], 403);
        }

        $webhook->delete();

        return response()->json([
            'success' => true,
            'message' => 'Webhook deleted successfully.',
        ]);
    }
}
