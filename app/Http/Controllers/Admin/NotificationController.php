<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendNotificationRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Show send notification form.
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Send notification form',
            ]);
        }

        return view('admin.notifications.create');
    }

    /**
     * Send notification to all users.
     */
    public function store(SendNotificationRequest $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        $count = $this->notificationService->createForAllUsers(
            $validated['type'],
            $validated['title'],
            $validated['message'],
            $validated['icon'] ?? null,
            $validated['priority'] ?? 'normal',
            $validated['action_url'] ?? null,
            $validated['action_text'] ?? null
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Notification sent to {$count} users successfully.",
                'data' => [
                    'users_notified' => $count,
                ],
            ], 201);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notification sent to {$count} users successfully.");
    }
}
