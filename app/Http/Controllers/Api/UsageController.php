<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UsageTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function __construct(
        private UsageTrackingService $usageTrackingService
    ) {}

    /**
     * Get usage statistics for the authenticated user.
     */
    public function index(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $user = $request->user();

        $stats = $this->usageTrackingService->getUsageStats($user, $days);
        $logs = $user->usageLogs()->with('apiKey')->latest()->paginate(20);

        // Get feature usage statistics
        $featureLimitService = app(\App\Services\FeatureLimitService::class);
        $featureUsageStats = $featureLimitService->getFeatureUsageStats($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'usage' => $stats,
                    'feature_usage' => $featureUsageStats,
                ],
            ]);
        }

        return view('dashboard.usage.index', compact('stats', 'logs', 'days', 'featureUsageStats'));
    }

    /**
     * Get detailed usage logs.
     */
    public function logs(Request $request): JsonResponse
    {
        $query = $request->user()->usageLogs()->with('apiKey')->latest();

        if ($request->has('api_key_id')) {
            $query->where('api_key_id', $request->api_key_id);
        }

        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                ],
            ],
        ]);
    }
}
