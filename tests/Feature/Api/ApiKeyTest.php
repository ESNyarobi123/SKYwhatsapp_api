<?php

use App\Models\ApiKey;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can list API keys for authenticated user', function () {
    ApiKey::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/api/api-keys');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'api_keys' => [
                    '*' => ['id', 'name', 'key_preview', 'is_active'],
                ],
            ],
        ]);

    expect($response->json('data.api_keys'))->toHaveCount(3);
});

it('can create a new API key', function () {
    $response = $this->postJson('/api/api-keys', [
        'name' => 'Test API Key',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'api_key' => ['id', 'name', 'key', 'key_preview'],
            ],
            'message',
        ]);

    expect($response->json('data.api_key.key'))->toStartWith('sk_');

    $this->assertDatabaseHas('api_keys', [
        'user_id' => $this->user->id,
        'name' => 'Test API Key',
        'is_active' => true,
    ]);
});

it('can revoke an API key', function () {
    $apiKey = ApiKey::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/api-keys/{$apiKey->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'API key revoked successfully.',
        ]);

    $this->assertDatabaseHas('api_keys', [
        'id' => $apiKey->id,
        'is_active' => false,
    ]);
});

it('prevents accessing other users API keys', function () {
    $otherUser = User::factory()->create();
    $apiKey = ApiKey::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->deleteJson("/api/api-keys/{$apiKey->id}");

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'error' => [
                'code' => 'UNAUTHORIZED',
            ],
        ]);
});
