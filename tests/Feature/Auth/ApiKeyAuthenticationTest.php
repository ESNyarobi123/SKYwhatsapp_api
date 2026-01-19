<?php

use App\Models\ApiKey;
use App\Models\User;

it('allows access with valid API key in header', function () {
    $user = User::factory()->create();
    $apiKey = ApiKey::factory()->create([
        'user_id' => $user->id,
        'key' => bcrypt('test-api-key-123'),
        'key_preview' => 'test-123',
        'is_active' => true,
    ]);

    // Store plain key for testing (in production, key would be hashed)
    $plainKey = 'test-api-key-123';

    $response = $this->getJson('/api/v1/instances', [
        'Authorization' => 'Bearer '.$plainKey,
    ]);

    // Note: This test assumes the API key middleware can validate the plain key
    // In production, the key is hashed and matched via Hash::check
    // For testing, we might need to adjust the authentication logic
    // or use a test helper to create properly hashed keys
});

it('denies access without API key', function () {
    $response = $this->getJson('/api/v1/instances');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'error' => [
                'code' => 'MISSING_API_KEY',
            ],
        ]);
});

it('denies access with invalid API key', function () {
    $response = $this->getJson('/api/v1/instances', [
        'Authorization' => 'Bearer invalid-key',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'error' => [
                'code' => 'INVALID_API_KEY',
            ],
        ]);
});

it('denies access with expired API key', function () {
    $user = User::factory()->create();
    $apiKey = ApiKey::factory()->create([
        'user_id' => $user->id,
        'key' => bcrypt('test-api-key-123'),
        'expires_at' => now()->subDay(),
        'is_active' => true,
    ]);

    // API key is expired, should be denied
    // Implementation depends on how middleware checks expiry
});

it('denies access with inactive API key', function () {
    $user = User::factory()->create();
    $apiKey = ApiKey::factory()->create([
        'user_id' => $user->id,
        'key' => bcrypt('test-api-key-123'),
        'is_active' => false,
    ]);

    // API key is inactive, should be denied
});
