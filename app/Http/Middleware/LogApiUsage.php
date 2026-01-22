<?php

namespace App\Http\Middleware;

use App\Models\UsageLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiUsage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // in milliseconds

        // Only log if we have an API key or a user, or if it's a significant endpoint
        // We can also log everything under /api/v1
        if ($request->is('api/v1/*')) {
            try {
                UsageLog::create([
                    'user_id' => $request->user()?->id,
                    'api_key_id' => $request->api_key_id, // Set by AuthenticateApiKey middleware
                    'endpoint' => $request->path(),
                    'method' => $request->method(),
                    'status_code' => $response->getStatusCode(),
                    'response_time' => $duration,
                ]);
            } catch (\Exception $e) {
                // Fail silently to not impact the API response
                // Log::error('Failed to log API usage: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
