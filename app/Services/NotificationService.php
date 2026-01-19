<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user.
     */
    public function create(
        User $user,
        string $type,
        string $title,
        string $message,
        ?string $icon = null,
        string $priority = 'normal',
        ?string $actionUrl = null,
        ?string $actionText = null,
        ?array $metadata = null
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon ?? $this->getDefaultIcon($type),
            'priority' => $priority,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Create notification for all users (admin broadcast).
     */
    public function createForAllUsers(
        string $type,
        string $title,
        string $message,
        ?string $icon = null,
        string $priority = 'normal',
        ?string $actionUrl = null,
        ?string $actionText = null,
        ?array $metadata = null
    ): int {
        $users = User::where('role', '!=', 'admin')->get();
        $count = 0;

        foreach ($users as $user) {
            $this->create(
                $user,
                $type,
                $title,
                $message,
                $icon,
                $priority,
                $actionUrl,
                $actionText,
                $metadata
            );
            $count++;
        }

        return $count;
    }

    /**
     * Get default icon for notification type.
     */
    protected function getDefaultIcon(string $type): string
    {
        return match ($type) {
            'admin_message' => '📢',
            'info' => 'ℹ️',
            'warning' => '⚠️',
            'success' => '✅',
            default => '🔔',
        };
    }
}
