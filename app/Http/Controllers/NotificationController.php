<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->notifications()->latest();

        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status if provided
        if ($request->has('read')) {
            $query->where('is_read', $request->boolean('read'));
        }

        $notifications = $query->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $notifications->items(),
                    'pagination' => [
                        'current_page' => $notifications->currentPage(),
                        'last_page' => $notifications->lastPage(),
                        'per_page' => $notifications->perPage(),
                        'total' => $notifications->total(),
                    ],
                ],
            ]);
        }

        return view('dashboard.notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count (for badge).
     */
    public function unread(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotificationsCount();

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this notification.',
                ],
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this notification.',
                ],
            ], 403);
        }

        $notification->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully.',
            ]);
        }

        return redirect()->route('dashboard.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }
}
