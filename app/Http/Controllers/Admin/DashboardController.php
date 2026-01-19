<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Instance;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UsageLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard overview.
     */
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $activeUsers = User::whereHas('activeSubscription')->count();
        $suspendedUsers = User::where('role', 'suspended')->count();

        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $expiredSubscriptions = Subscription::where('status', 'expired')->count();

        $totalApiKeys = ApiKey::count();
        $activeApiKeys = ApiKey::where('is_active', true)->count();
        $revokedApiKeys = ApiKey::where('is_active', false)->count();

        $totalInstances = Instance::count();
        $connectedInstances = Instance::where('status', 'connected')->count();
        $disconnectedInstances = Instance::where('status', 'disconnected')->count();

        $totalRequests = UsageLog::count();
        $successfulRequests = UsageLog::whereBetween('status_code', [200, 299])->count();
        $failedRequests = UsageLog::where('status_code', '>=', 400)->count();

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        // Get recent activity
        $recentPayments = Payment::with('user')->latest()->limit(5)->get();
        $recentUsers = User::latest()->limit(5)->get();
        $recentActivity = UsageLog::with('user')->latest()->limit(10)->get();

        // Get pending actions
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->count();

        $pendingPayments = Payment::where('status', 'pending')->count();

        $overview = [
            'users' => [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'suspended' => $suspendedUsers,
            ],
            'subscriptions' => [
                'total' => $totalSubscriptions,
                'active' => $activeSubscriptions,
                'expired' => $expiredSubscriptions,
            ],
            'api_keys' => [
                'total' => $totalApiKeys,
                'active' => $activeApiKeys,
                'revoked' => $revokedApiKeys,
            ],
            'instances' => [
                'total' => $totalInstances,
                'connected' => $connectedInstances,
                'disconnected' => $disconnectedInstances,
            ],
            'usage' => [
                'total_requests' => $totalRequests,
                'successful' => $successfulRequests,
                'failed' => $failedRequests,
            ],
            'revenue' => [
                'total' => (float) $totalRevenue,
                'today' => (float) $todayRevenue,
            ],
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => $overview,
                    'recent_payments' => $recentPayments,
                    'recent_users' => $recentUsers,
                    'recent_activity' => $recentActivity,
                    'pending_actions' => [
                        'expired_subscriptions' => $expiredSubscriptions,
                        'pending_payments' => $pendingPayments,
                    ],
                ],
            ]);
        }

        return view('admin.dashboard', compact('overview', 'recentPayments', 'recentUsers', 'recentActivity', 'expiredSubscriptions', 'pendingPayments'));
    }

    /**
     * Get detailed analytics.
     */
    public function analytics(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $startDate = now()->subDays($days);

        // User analytics
        $usersByPeriod = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Subscription analytics
        $subscriptionsByStatus = Subscription::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Revenue analytics
        $revenueByProvider = Payment::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('provider, SUM(amount) as total')
            ->groupBy('provider')
            ->get();

        $revenueByPeriod = Payment::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Usage analytics
        $usageByPeriod = UsageLog::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $avgResponseTime = UsageLog::where('created_at', '>=', $startDate)
            ->whereNotNull('response_time')
            ->avg('response_time');

        // Instance analytics
        $instancesByStatus = Instance::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $analytics = [
            'period_days' => $days,
            'users' => [
                'growth' => $usersByPeriod,
            ],
            'subscriptions' => [
                'by_status' => $subscriptionsByStatus,
            ],
            'revenue' => [
                'by_provider' => $revenueByProvider,
                'by_period' => $revenueByPeriod,
            ],
            'usage' => [
                'by_period' => $usageByPeriod,
                'average_response_time_ms' => $avgResponseTime ? round($avgResponseTime, 2) : null,
            ],
            'instances' => [
                'by_status' => $instancesByStatus,
            ],
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['analytics' => $analytics],
            ]);
        }

        return view('admin.analytics', compact('analytics', 'days'));
    }
}
