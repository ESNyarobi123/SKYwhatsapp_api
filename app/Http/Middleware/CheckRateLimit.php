<?php

namespace App\Http\Middleware;

use App\Services\UsageTrackingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRateLimit
{
    public function __construct(
        private UsageTrackingService $usageTrackingService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $limit = 100, int $decayMinutes = 60): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $endpoint = $request->route()?->uri() ?? $request->path();

        if (! $this->usageTrackingService->checkRateLimit($user, $endpoint, $limit, $decayMinutes)) {
            $remaining = $this->usageTrackingService->remainingAttempts($user, $endpoint, $limit);
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'RATE_LIMIT_EXCEEDED',
                    'message' => 'Rate limit exceeded. Please try again later.',
                ],
            ], 429)->withHeaders([
                'X-RateLimit-Limit' => $limit,
                'X-RateLimit-Remaining' => $remaining,
                'Retry-After' => $decayMinutes * 60,
            ]);
        }

        $response = $next($request);

        // Track usage after request
        $this->usageTrackingService->logUsage(
            $user,
            $request->has('api_key_id') ? \App\Models\ApiKey::find($request->get('api_key_id')) : null,
            $endpoint,
            $request->method(),
            $response->getStatusCode()
        );

        $remaining = $this->usageTrackingService->remainingAttempts($user, $endpoint, $limit);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $limit,
            'X-RateLimit-Remaining' => $remaining,
        ]);
    }
}
