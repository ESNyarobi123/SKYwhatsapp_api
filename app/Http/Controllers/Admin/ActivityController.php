<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UsageLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * List activity logs with filters.
     */
    public function index(Request $request)
    {
        $query = UsageLog::with(['user', 'apiKey']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('endpoint')) {
            $query->where('endpoint', 'like', '%'.$request->endpoint.'%');
        }

        if ($request->has('method')) {
            $query->where('method', $request->method);
        }

        if ($request->has('status_code')) {
            $query->where('status_code', $request->status_code);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->latest()->paginate($request->get('per_page', 50));

        // Calculate stats
        $stats = [
            'total_requests' => UsageLog::count(),
            'successful_requests' => UsageLog::whereBetween('status_code', [200, 299])->count(),
            'failed_requests' => UsageLog::whereBetween('status_code', [400, 599])->count(),
            'average_response_time' => round(UsageLog::avg('response_time') ?? 0, 2),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'activities' => $activities->items(),
                    'stats' => $stats,
                    'pagination' => [
                        'current_page' => $activities->currentPage(),
                        'last_page' => $activities->lastPage(),
                        'per_page' => $activities->perPage(),
                        'total' => $activities->total(),
                    ],
                ],
            ]);
        }

        return view('admin.activity.index', compact('activities', 'stats'));
    }
}
