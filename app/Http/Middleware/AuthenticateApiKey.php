<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->bearerToken() ?? $request->header('X-API-Key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'MISSING_API_KEY',
                    'message' => 'API key is required.',
                ],
            ], 401);
        }

        $apiKeyModel = ApiKey::query()
            ->where('is_active', true)
            ->where(function ($query) use ($apiKey) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get()
            ->first(function ($key) use ($apiKey) {
                return Hash::check($apiKey, $key->key);
            });

        if (! $apiKeyModel || ! $apiKeyModel->isValid()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_API_KEY',
                    'message' => 'Invalid or expired API key.',
                ],
            ], 401);
        }

        // Update last used timestamp
        $apiKeyModel->update(['last_used_at' => now()]);

        // Set user and API key on request
        $request->setUserResolver(fn () => $apiKeyModel->user);
        $request->merge(['api_key_id' => $apiKeyModel->id]);

        return $next($request);
    }
}
