<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketRequest;
use App\Http\Requests\SendSupportMessageRequest;
use App\Models\SupportTicket;
use App\Services\SupportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function __construct(
        private SupportService $supportService
    ) {}

    /**
     * List user's support tickets.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->supportTickets()->with(['assignedAdmin', 'lastRepliedBy'])->latest();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate(15);

        return view('dashboard.support.index', compact('tickets'));
    }

    /**
     * Show create ticket form.
     */
    public function create()
    {
        return view('dashboard.support.create');
    }

    /**
     * Create a new support ticket.
     */
    public function store(CreateTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $ticket = $this->supportService->createTicket(
            $request->user(),
            $validated['subject'],
            $validated['description'],
            $validated['category'] ?? 'other',
            $validated['priority'] ?? 'normal'
        );

        return redirect()->route('dashboard.support.show', $ticket)
            ->with('success', 'Support ticket created successfully. Ticket number: '.$ticket->ticket_number);
    }

    /**
     * View a support ticket and its messages.
     */
    public function show(Request $request, SupportTicket $ticket)
    {
        // Ensure user owns this ticket
        if ($ticket->user_id !== $request->user()->id) {
            abort(403, 'You do not have access to this ticket.');
        }

        // Mark unread admin messages as read
        $ticket->messages()
            ->where('is_admin', true)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $ticket->load(['messages.user', 'assignedAdmin', 'lastRepliedBy']);
        $messages = $ticket->messages()->with('user')->orderBy('created_at', 'asc')->get();

        return view('dashboard.support.show', compact('ticket', 'messages'));
    }

    /**
     * Send a message in a ticket.
     */
    public function sendMessage(SendSupportMessageRequest $request, SupportTicket $ticket): RedirectResponse
    {
        // Ensure user owns this ticket
        if ($ticket->user_id !== $request->user()->id) {
            abort(403, 'You do not have access to this ticket.');
        }

        // Ensure ticket is not closed
        if ($ticket->isClosed()) {
            return redirect()->back()
                ->with('error', 'Cannot send message to a closed ticket.');
        }

        $this->supportService->sendMessage(
            $ticket,
            $request->user(),
            $request->validated()['message'],
            false
        );

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    /**
     * Close a ticket (user).
     */
    public function close(Request $request, SupportTicket $ticket): RedirectResponse
    {
        // Ensure user owns this ticket
        if ($ticket->user_id !== $request->user()->id) {
            abort(403, 'You do not have access to this ticket.');
        }

        $this->supportService->closeTicket($ticket);

        return redirect()->back()->with('success', 'Ticket closed successfully.');
    }
}
