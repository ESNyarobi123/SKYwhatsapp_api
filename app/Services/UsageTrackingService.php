<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\UsageLog;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class UsageTrackingService
{
    /**
     * Log API usage.
     */
    public function logUsage(
        User $user,
        ?ApiKey $apiKey,
        string $endpoint,
        string $method,
        int $statusCode,
        ?int $responseTime = null
    ): UsageLog {
        return UsageLog::create([
            'user_id' => $user->id,
            'api_key_id' => $apiKey?->id,
            'endpoint' => $endpoint,
            'method' => $method,
            'status_code' => $statusCode,
            'response_time' => $responseTime,
        ]);
    }

    /**
     * Check rate limit for user.
     */
    public function checkRateLimit(User $user, string $endpoint, int $limit = 100, int $decayMinutes = 60): bool
    {
        $key = "rate_limit:{$user->id}:{$endpoint}";

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return false;
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return true;
    }

    /**
     * Get remaining rate limit attempts.
     */
    public function remainingAttempts(User $user, string $endpoint, int $limit = 100): int
    {
        $key = "rate_limit:{$user->id}:{$endpoint}";

        return max(0, $limit - RateLimiter::attempts($key));
    }

    /**
     * Get usage statistics for user.
     */
    public function getUsageStats(User $user, ?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $totalRequests = $user->usageLogs()
            ->where('created_at', '>=', $startDate)
            ->count();

        $successfulRequests = $user->usageLogs()
            ->where('created_at', '>=', $startDate)
            ->whereBetween('status_code', [200, 299])
            ->count();

        $failedRequests = $user->usageLogs()
            ->where('created_at', '>=', $startDate)
            ->where('status_code', '>=', 400)
            ->count();

        $avgResponseTime = $user->usageLogs()
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('response_time')
            ->avg('response_time');

        return [
            'total_requests' => $totalRequests,
            'successful_requests' => $successfulRequests,
            'failed_requests' => $failedRequests,
            'average_response_time_ms' => $avgResponseTime ? round($avgResponseTime, 2) : null,
            'period_days' => $days,
        ];
    }
}
