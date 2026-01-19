<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Support\Str;

class ApiKeyService
{
    /**
     * Generate a new API key.
     */
    public function generateKey(): string
    {
        return 'sk_'.Str::random(48);
    }

    /**
     * Create a new API key for the user.
     */
    public function create(User $user, string $name, ?\DateTime $expiresAt = null): array
    {
        $plainKey = $this->generateKey();
        $keyPreview = substr($plainKey, -8);

        $apiKey = ApiKey::create([
            'user_id' => $user->id,
            'name' => $name,
            'key' => $plainKey,
            'key_preview' => $keyPreview,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        return [
            'api_key' => $apiKey,
            'plain_key' => $plainKey, // Only returned once
        ];
    }

    /**
     * Rotate an API key (create new, revoke old).
     */
    public function rotate(ApiKey $apiKey, string $name): array
    {
        $apiKey->update(['is_active' => false]);

        return $this->create($apiKey->user, $name, $apiKey->expires_at);
    }

    /**
     * Revoke an API key.
     */
    public function revoke(ApiKey $apiKey): bool
    {
        return $apiKey->update(['is_active' => false]);
    }
}
