<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendSupportMessageRequest;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\SupportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function __construct(
        private SupportService $supportService
    ) {}

    /**
     * List all support tickets.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'assignedAdmin', 'lastRepliedBy'])->latest();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assigned admin if provided
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by priority if provided
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter unassigned tickets
        if ($request->has('unassigned') && $request->boolean('unassigned')) {
            $query->whereNull('assigned_to');
        }

        $tickets = $query->paginate(20);
        $admins = User::where('role', 'admin')->get();

        return view('admin.support.index', compact('tickets', 'admins'));
    }

    /**
     * View a support ticket and its messages.
     */
    public function show(Request $request, SupportTicket $ticket)
    {
        // Mark unread user messages as read
        $ticket->messages()
            ->where('is_admin', false)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $ticket->load(['messages.user', 'user', 'assignedAdmin', 'lastRepliedBy']);
        $messages = $ticket->messages()->with('user')->orderBy('created_at', 'asc')->get();
        $admins = User::where('role', 'admin')->get();

        return view('admin.support.show', compact('ticket', 'messages', 'admins'));
    }

    /**
     * Assign ticket to an admin.
     */
    public function assign(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'admin_id' => ['required', 'exists:users,id'],
        ]);

        $admin = User::findOrFail($request->admin_id);

        if (! $admin->isAdmin()) {
            return redirect()->back()->with('error', 'Selected user is not an admin.');
        }

        $this->supportService->assignTicket($ticket, $admin);

        return redirect()->back()->with('success', 'Ticket assigned successfully.');
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:open,in_progress,resolved,closed'],
        ]);

        $this->supportService->updateStatus($ticket, $request->status);

        return redirect()->back()->with('success', 'Ticket status updated successfully.');
    }

    /**
     * Send a message in a ticket (admin).
     */
    public function sendMessage(SendSupportMessageRequest $request, SupportTicket $ticket): RedirectResponse
    {
        // Ensure ticket is not closed
        if ($ticket->isClosed()) {
            return redirect()->back()
                ->with('error', 'Cannot send message to a closed ticket.');
        }

        $this->supportService->sendMessage(
            $ticket,
            $request->user(),
            $request->validated()['message'],
            true
        );

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    /**
     * Close a ticket (admin).
     */
    public function close(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->supportService->closeTicket($ticket);

        return redirect()->back()->with('success', 'Ticket closed successfully.');
    }
}
