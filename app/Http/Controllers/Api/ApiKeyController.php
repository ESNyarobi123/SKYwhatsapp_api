<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateApiKeyRequest;
use App\Models\ApiKey;
use App\Services\ApiKeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function __construct(
        private ApiKeyService $apiKeyService
    ) {}

    /**
     * List all API keys for the authenticated user.
     */
    /**
     * Check if user is authorized to perform action on API key.
     */
    private function authorizeApiKeyAction($user, $permission, $apiKey = null)
    {
        // 1. Check direct ownership (if key provided)
        if ($apiKey && $apiKey->user_id === $user->id) {
            return true;
        }

        // 2. Check team membership
        if ($user->current_team_id) {
            $team = $user->currentTeam;
            
            // If key provided, it must belong to team owner
            if ($apiKey && $team && $apiKey->user_id !== $team->owner_id) {
                return false;
            }

            // Check if user has permission in this team
            if ($team) {
                $member = $team->members()->where('user_id', $user->id)->first();
                if ($member && $member->hasPermission($permission)) {
                    return true;
                }
            }
        }

        // If no team and no direct ownership (or key not provided but checking general permission)
        if (!$apiKey && !$user->current_team_id) {
            return true;
        }

        return false;
    }

    /**
     * List all API keys for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        // Check permission
        if (!$this->authorizeApiKeyAction($user, 'api_keys.view')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized');
        }

        $apiKeys = $owner->apiKeys()->latest()->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'api_keys' => $apiKeys->map(fn ($key) => [
                        'id' => $key->id,
                        'name' => $key->name,
                        'key_preview' => $key->key_preview,
                        'last_used_at' => $key->last_used_at,
                        'expires_at' => $key->expires_at,
                        'is_active' => $key->is_active,
                        'created_at' => $key->created_at,
                    ]),
                ],
            ]);
        }

        return view('dashboard.api-keys.index', compact('apiKeys'));
    }

    /**
     * Create a new API key.
     */
    public function store(CreateApiKeyRequest $request)
    {
        $user = $request->user();

        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        // Check permission
        if (!$this->authorizeApiKeyAction($user, 'api_keys.create')) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not have permission to create API keys.',
                ],
            ], 403);
        }

        // Check if OWNER has active subscription
        if (! $owner->hasActiveSubscription()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'SUBSCRIPTION_REQUIRED',
                        'message' => 'Active subscription is required to create API keys. Please select a package first.',
                    ],
                ], 403);
            }

            return redirect()->route('dashboard.api-keys')
                ->with('error', 'Active subscription is required to create API keys. Please select a package first.');
        }

        // Check if OWNER can create more API keys
        if (! $owner->canCreateApiKey()) {
            $featureLimitService = app(\App\Services\FeatureLimitService::class);
            $usage = $featureLimitService->getFeatureUsageStats($owner);
            $apiKeyUsage = $usage['api_keys'] ?? null;

            $message = 'API key limit reached for your plan.';
            if ($apiKeyUsage && $apiKeyUsage['limit'] !== null) {
                $message = "API key limit reached ({$apiKeyUsage['usage']}/{$apiKeyUsage['limit']}). Please upgrade your plan to create more API keys.";
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'API_KEY_LIMIT_REACHED',
                        'message' => $message,
                    ],
                ], 403);
            }

            return redirect()->route('dashboard.api-keys')->with('error', $message);
        }

        $expiresAt = $request->expires_at ? new \DateTime($request->expires_at) : null;

        $result = $this->apiKeyService->create(
            $owner, // Create for OWNER
            $request->name,
            $expiresAt
        );

        // Increment API key usage for OWNER
        $featureLimitService = app(\App\Services\FeatureLimitService::class);
        $featureLimitService->incrementUsage($owner, 'api_keys');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'api_key' => [
                        'id' => $result['api_key']->id,
                        'name' => $result['api_key']->name,
                        'key' => $result['plain_key'], // Only shown once
                        'key_preview' => $result['api_key']->key_preview,
                        'expires_at' => $result['api_key']->expires_at,
                    ],
                ],
                'message' => 'API key created successfully. Save this key securely as it will not be shown again.',
            ], 201);
        }

        // For web requests, store the API key in session temporarily to show it once
        $request->session()->flash('api_key_created', [
            'key' => $result['plain_key'],
            'name' => $result['api_key']->name,
        ]);

        return redirect()->route('dashboard.api-keys')
            ->with('success', 'API key created successfully. Save this key securely as it will not be shown again.');
    }

    /**
     * Rotate an API key.
     */
    public function rotate(Request $request, ApiKey $apiKey): JsonResponse
    {
        $user = $request->user();
        
        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        if ($apiKey->user_id !== $owner->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this API key.',
                ],
            ], 403);
        }

        // Check permission (rotate is effectively edit/create)
        if (!$this->authorizeApiKeyAction($user, 'api_keys.create', $apiKey)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $result = $this->apiKeyService->rotate($apiKey, $request->name);

        return response()->json([
            'success' => true,
            'data' => [
                'api_key' => [
                    'id' => $result['api_key']->id,
                    'name' => $result['api_key']->name,
                    'key' => $result['plain_key'], // Only shown once
                    'key_preview' => $result['api_key']->key_preview,
                    'expires_at' => $result['api_key']->expires_at,
                ],
            ],
            'message' => 'API key rotated successfully. The old key has been revoked.',
        ]);
    }

    /**
     * Revoke an API key.
     */
    public function destroy(Request $request, ApiKey $apiKey): JsonResponse
    {
        $user = $request->user();
        
        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        if ($apiKey->user_id !== $owner->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You do not own this API key.',
                ],
            ], 403);
        }

        // Check permission (delete is usually restricted, but for API keys maybe same as create?)
        // Let's assume 'api_keys.create' implies management, or we need a specific 'api_keys.delete'
        // TeamMember model has 'api_keys.create' but not explicit delete. Let's use create for now as management.
        if (!$this->authorizeApiKeyAction($user, 'api_keys.create', $apiKey)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->apiKeyService->revoke($apiKey);

        return response()->json([
            'success' => true,
            'message' => 'API key revoked successfully.',
        ]);
    }
}
