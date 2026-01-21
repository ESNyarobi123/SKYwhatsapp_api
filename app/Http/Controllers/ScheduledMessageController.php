<?php

namespace App\Http\Controllers;

use App\Models\ScheduledMessage;
use App\Models\Instance;
use Illuminate\Http\Request;

class ScheduledMessageController extends Controller
{
    /**
     * Display a listing of scheduled messages.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->get('status');

        $messages = ScheduledMessage::where('user_id', $user->id)
            ->with('instance:id,name,phone_number')
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);

        $instances = Instance::where('user_id', $user->id)
            ->where('status', 'connected')
            ->get();

        $stats = [
            'pending' => ScheduledMessage::where('user_id', $user->id)->where('status', 'pending')->count(),
            'sent' => ScheduledMessage::where('user_id', $user->id)->where('status', 'sent')->count(),
            'failed' => ScheduledMessage::where('user_id', $user->id)->where('status', 'failed')->count(),
        ];

        return view('dashboard.scheduler.index', compact('messages', 'instances', 'stats', 'status'));
    }

    /**
     * Store a newly created scheduled message.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'instance_id' => 'required|exists:instances,id',
            'recipient' => 'required|string|max:50',
            'content' => 'required|string|max:4096',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'message_type' => 'in:text,image,document',
        ]);

        // Verify instance belongs to user
        $instance = Instance::where('id', $validated['instance_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Combine date and time
        $scheduledAt = \Carbon\Carbon::parse(
            $validated['scheduled_date'] . ' ' . $validated['scheduled_time']
        );

        // Must be in the future
        if ($scheduledAt->isPast()) {
            return back()->withErrors(['scheduled_date' => 'Scheduled time must be in the future.']);
        }

        // Clean phone number
        $recipient = preg_replace('/[^0-9+]/', '', $validated['recipient']);

        ScheduledMessage::create([
            'user_id' => $user->id,
            'instance_id' => $instance->id,
            'recipient' => $recipient,
            'content' => $validated['content'],
            'message_type' => $validated['message_type'] ?? 'text',
            'scheduled_at' => $scheduledAt,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard.scheduler.index')
            ->with('success', 'Message scheduled successfully!');
    }

    /**
     * Update the specified scheduled message.
     */
    public function update(Request $request, ScheduledMessage $scheduledMessage)
    {
        // Ensure user owns the message
        if ($scheduledMessage->user_id !== auth()->id()) {
            abort(403);
        }

        // Can only update pending messages
        if ($scheduledMessage->status !== 'pending') {
            return back()->withErrors(['error' => 'Cannot update message that has already been processed.']);
        }

        $validated = $request->validate([
            'recipient' => 'required|string|max:50',
            'content' => 'required|string|max:4096',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
        ]);

        $scheduledAt = \Carbon\Carbon::parse(
            $validated['scheduled_date'] . ' ' . $validated['scheduled_time']
        );

        if ($scheduledAt->isPast()) {
            return back()->withErrors(['scheduled_date' => 'Scheduled time must be in the future.']);
        }

        $scheduledMessage->update([
            'recipient' => preg_replace('/[^0-9+]/', '', $validated['recipient']),
            'content' => $validated['content'],
            'scheduled_at' => $scheduledAt,
        ]);

        return redirect()->route('dashboard.scheduler.index')
            ->with('success', 'Scheduled message updated!');
    }

    /**
     * Cancel a scheduled message.
     */
    public function cancel(ScheduledMessage $scheduledMessage)
    {
        // Ensure user owns the message
        if ($scheduledMessage->user_id !== auth()->id()) {
            abort(403);
        }

        if ($scheduledMessage->status !== 'pending') {
            return back()->withErrors(['error' => 'Cannot cancel message that has already been processed.']);
        }

        $scheduledMessage->cancel();

        return redirect()->route('dashboard.scheduler.index')
            ->with('success', 'Scheduled message cancelled!');
    }

    /**
     * Remove the specified scheduled message.
     */
    public function destroy(ScheduledMessage $scheduledMessage)
    {
        // Ensure user owns the message
        if ($scheduledMessage->user_id !== auth()->id()) {
            abort(403);
        }

        $scheduledMessage->delete();

        return redirect()->route('dashboard.scheduler.index')
            ->with('success', 'Scheduled message deleted!');
    }

    /**
     * Retry a failed message.
     */
    public function retry(ScheduledMessage $scheduledMessage)
    {
        // Ensure user owns the message
        if ($scheduledMessage->user_id !== auth()->id()) {
            abort(403);
        }

        if ($scheduledMessage->status !== 'failed') {
            return back()->withErrors(['error' => 'Can only retry failed messages.']);
        }

        $scheduledMessage->update([
            'status' => 'pending',
            'scheduled_at' => now()->addMinutes(1),
            'error_message' => null,
        ]);

        return redirect()->route('dashboard.scheduler.index')
            ->with('success', 'Message rescheduled for retry!');
    }
}
