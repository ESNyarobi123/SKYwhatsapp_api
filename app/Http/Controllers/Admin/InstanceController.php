<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstanceController extends Controller
{
    /**
     * List all instances with filters.
     */
    public function index(Request $request)
    {
        $query = Instance::with(['user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $instances = $query->latest()->paginate($request->get('per_page', 15));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'instances' => $instances->items()->map(fn ($instance) => [
                        'id' => $instance->id,
                        'user' => [
                            'id' => $instance->user->id,
                            'name' => $instance->user->name,
                            'email' => $instance->user->email,
                        ],
                        'name' => $instance->name,
                        'phone_number' => $instance->phone_number,
                        'status' => $instance->status,
                        'last_connected_at' => $instance->last_connected_at,
                        'messages_count' => $instance->messages()->count(),
                        'created_at' => $instance->created_at,
                    ]),
                    'pagination' => [
                        'current_page' => $instances->currentPage(),
                        'last_page' => $instances->lastPage(),
                        'per_page' => $instances->perPage(),
                        'total' => $instances->total(),
                    ],
                ],
            ]);
        }

        return view('admin.instances.index', compact('instances'));
    }

    /**
     * Get instance details.
     */
    public function show(Request $request, Instance $instance)
    {
        $instance->load(['user', 'messages' => fn ($query) => $query->latest()->limit(20)]);

        $stats = [
            'messages_count' => $instance->messages()->count(),
            'inbound_messages' => $instance->messages()->where('direction', 'inbound')->count(),
            'outbound_messages' => $instance->messages()->where('direction', 'outbound')->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'instance' => [
                        'id' => $instance->id,
                        'user' => [
                            'id' => $instance->user->id,
                            'name' => $instance->user->name,
                            'email' => $instance->user->email,
                        ],
                        'name' => $instance->name,
                        'phone_number' => $instance->phone_number,
                        'status' => $instance->status,
                        'last_connected_at' => $instance->last_connected_at,
                        'created_at' => $instance->created_at,
                    ],
                    'stats' => $stats,
                ],
            ]);
        }

        return view('admin.instances.show', compact('instance', 'stats'));
    }

    /**
     * Reset instance session (clear session data, disconnect).
     */
    public function reset(Request $request, Instance $instance)
    {
        $instance->update([
            'status' => 'disconnected',
            'session_data' => null,
            'qr_code' => null,
            'qr_expires_at' => null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Instance session reset successfully.',
            ]);
        }

        return back()->with('success', 'Instance session reset successfully.');
    }

    /**
     * Delete an instance.
     */
    public function destroy(Request $request, Instance $instance)
    {
        $instance->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Instance deleted successfully.',
            ]);
        }

        return redirect()->route('admin.instances.index')->with('success', 'Instance deleted successfully.');
    }
}
