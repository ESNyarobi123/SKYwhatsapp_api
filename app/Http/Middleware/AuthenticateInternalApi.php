<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateInternalApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->bearerToken() ?? $request->header('X-API-Key');

        $expectedKey = config('services.whatsapp_service.api_key');

        if (! $expectedKey) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INTERNAL_API_NOT_CONFIGURED',
                    'message' => 'Internal API is not configured.',
                ],
            ], 500);
        }

        if (! $apiKey || $apiKey !== $expectedKey) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_INTERNAL_API_KEY',
                    'message' => 'Invalid internal API key.',
                ],
            ], 401);
        }

        return $next($request);
    }
}
