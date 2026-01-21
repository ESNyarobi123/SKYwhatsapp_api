<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookLogController extends Controller
{
    /**
     * Display a listing of webhook logs.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->get('status');
        $webhookId = $request->get('webhook_id');
        $eventType = $request->get('event_type');

        $logs = WebhookLog::where('user_id', $user->id)
            ->with('webhook:id,url')
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($webhookId, fn($q) => $q->where('webhook_id', $webhookId))
            ->when($eventType, fn($q) => $q->where('event_type', $eventType))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $webhooks = Webhook::where('user_id', $user->id)->get();

        $stats = [
            'total' => WebhookLog::where('user_id', $user->id)->count(),
            'success' => WebhookLog::where('user_id', $user->id)->successful()->count(),
            'failed' => WebhookLog::where('user_id', $user->id)->failed()->count(),
            'pending' => WebhookLog::where('user_id', $user->id)->where('status', 'pending')->count(),
        ];

        $eventTypes = WebhookLog::where('user_id', $user->id)
            ->distinct()
            ->pluck('event_type')
            ->toArray();

        return view('dashboard.webhook-logs.index', compact(
            'logs',
            'webhooks',
            'stats',
            'eventTypes',
            'status',
            'webhookId',
            'eventType'
        ));
    }

    /**
     * Show details of a specific log.
     */
    public function show(WebhookLog $webhookLog)
    {
        // Ensure user owns the log
        if ($webhookLog->user_id !== auth()->id()) {
            abort(403);
        }

        return view('dashboard.webhook-logs.show', compact('webhookLog'));
    }

    /**
     * Retry a failed webhook delivery.
     */
    public function retry(WebhookLog $webhookLog)
    {
        // Ensure user owns the log
        if ($webhookLog->user_id !== auth()->id()) {
            abort(403);
        }

        if ($webhookLog->status !== 'failed') {
            return back()->withErrors(['error' => 'Can only retry failed deliveries.']);
        }

        // Create a new log for retry
        $newLog = WebhookLog::create([
            'webhook_id' => $webhookLog->webhook_id,
            'user_id' => $webhookLog->user_id,
            'event_type' => $webhookLog->event_type,
            'payload' => $webhookLog->payload,
            'status' => 'pending',
            'retry_count' => $webhookLog->retry_count + 1,
        ]);

        // TODO: Dispatch job to actually send the webhook
        // SendWebhookJob::dispatch($newLog);

        return redirect()->route('dashboard.webhook-logs.index')
            ->with('success', 'Webhook retry scheduled!');
    }

    /**
     * Get log details as JSON (for modal).
     */
    public function details(WebhookLog $webhookLog)
    {
        // Ensure user owns the log
        if ($webhookLog->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json([
            'success' => true,
            'log' => [
                'id' => $webhookLog->id,
                'event_type' => $webhookLog->event_type,
                'status' => $webhookLog->status,
                'status_code' => $webhookLog->status_code,
                'payload' => $webhookLog->payload,
                'response' => $webhookLog->response,
                'error_message' => $webhookLog->error_message,
                'response_time_ms' => $webhookLog->response_time_ms,
                'retry_count' => $webhookLog->retry_count,
                'created_at' => $webhookLog->created_at->format('Y-m-d H:i:s'),
                'delivered_at' => $webhookLog->delivered_at?->format('Y-m-d H:i:s'),
                'webhook_url' => $webhookLog->webhook?->url,
            ],
        ]);
    }

    /**
     * Clear old logs.
     */
    public function clear(Request $request)
    {
        $user = auth()->user();
        $days = $request->get('days', 30);

        $deleted = WebhookLog::where('user_id', $user->id)
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        return redirect()->route('dashboard.webhook-logs.index')
            ->with('success', "Cleared {$deleted} logs older than {$days} days.");
    }
}
