<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    /**
     * List all API keys across all users.
     */
    public function index(Request $request)
    {
        $query = ApiKey::with(['user']);

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $apiKeys = $query->latest()->paginate($request->get('per_page', 15));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'api_keys' => $apiKeys->items()->map(fn ($key) => [
                        'id' => $key->id,
                        'user' => [
                            'id' => $key->user->id,
                            'name' => $key->user->name,
                            'email' => $key->user->email,
                        ],
                        'name' => $key->name,
                        'key_preview' => $key->key_preview,
                        'last_used_at' => $key->last_used_at,
                        'expires_at' => $key->expires_at,
                        'is_active' => $key->is_active,
                        'created_at' => $key->created_at,
                    ]),
                    'pagination' => [
                        'current_page' => $apiKeys->currentPage(),
                        'last_page' => $apiKeys->lastPage(),
                        'per_page' => $apiKeys->perPage(),
                        'total' => $apiKeys->total(),
                    ],
                ],
            ]);
        }

        return view('admin.api-keys.index', compact('apiKeys'));
    }

    /**
     * Show API key details.
     */
    public function show(Request $request, ApiKey $apiKey)
    {
        $apiKey->load(['user']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'api_key' => [
                        'id' => $apiKey->id,
                        'user' => [
                            'id' => $apiKey->user->id,
                            'name' => $apiKey->user->name,
                            'email' => $apiKey->user->email,
                        ],
                        'name' => $apiKey->name,
                        'key_preview' => $apiKey->key_preview,
                        'last_used_at' => $apiKey->last_used_at,
                        'expires_at' => $apiKey->expires_at,
                        'is_active' => $apiKey->is_active,
                        'created_at' => $apiKey->created_at,
                    ],
                ],
            ]);
        }

        return view('admin.api-keys.show', compact('apiKey'));
    }

    /**
     * Revoke an API key.
     */
    public function revoke(Request $request, ApiKey $apiKey)
    {
        $apiKey->update(['is_active' => false]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'API key revoked successfully.',
            ]);
        }

        return back()->with('success', 'API key revoked successfully.');
    }

    /**
     * Reactivate a revoked API key.
     */
    public function reactivate(Request $request, ApiKey $apiKey)
    {
        $apiKey->update(['is_active' => true]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'API key reactivated successfully.',
            ]);
        }

        return back()->with('success', 'API key reactivated successfully.');
    }
}
