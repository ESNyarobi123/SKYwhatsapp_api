<?php

namespace App\Services;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;

class SupportService
{
    /**
     * Create a new support ticket.
     */
    public function createTicket(
        User $user,
        string $subject,
        string $description,
        string $category = 'other',
        string $priority = 'normal'
    ): SupportTicket {
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'subject' => $subject,
            'description' => $description,
            'category' => $category,
            'priority' => $priority,
            'status' => 'open',
        ]);

        // Create initial message from description
        $this->sendMessage($ticket, $user, $description, false);

        return $ticket;
    }

    /**
     * Send a message in a ticket.
     */
    public function sendMessage(
        SupportTicket $ticket,
        User $user,
        string $message,
        bool $isAdmin = false
    ): SupportMessage {
        $supportMessage = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $message,
            'is_admin' => $isAdmin,
            'is_read' => false,
        ]);

        // Update ticket's last replied info
        $ticket->update([
            'last_replied_at' => now(),
            'last_replied_by' => $user->id,
        ]);

        // If admin replied and ticket is open, set to in_progress
        if ($isAdmin && $ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return $supportMessage;
    }

    /**
     * Assign ticket to an admin.
     */
    public function assignTicket(SupportTicket $ticket, User $admin): bool
    {
        if (! $admin->isAdmin()) {
            return false;
        }

        $ticket->update([
            'assigned_to' => $admin->id,
            'status' => 'in_progress',
        ]);

        return true;
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(SupportTicket $ticket, string $status): bool
    {
        $validStatuses = ['open', 'in_progress', 'resolved', 'closed'];

        if (! in_array($status, $validStatuses)) {
            return false;
        }

        return $ticket->update(['status' => $status]);
    }

    /**
     * Close a ticket.
     */
    public function closeTicket(SupportTicket $ticket): bool
    {
        return $ticket->update(['status' => 'closed']);
    }
}
