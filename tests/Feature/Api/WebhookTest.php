<?php

use App\Models\Instance;
use App\Models\User;
use App\Models\Webhook;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->instance = Instance::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

it('can list webhooks for authenticated user', function () {
    Webhook::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/api/v1/webhooks');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'webhooks' => [
                    '*' => ['id', 'url', 'events', 'is_active'],
                ],
            ],
        ]);

    expect($response->json('data.webhooks'))->toHaveCount(3);
});

it('can create a new webhook', function () {
    $response = $this->postJson('/api/v1/webhooks', [
        'url' => 'https://example.com/webhook',
        'events' => ['message.inbound', 'message.status'],
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'webhook' => ['id', 'url', 'events', 'secret'],
            ],
            'message',
        ]);

    $this->assertDatabaseHas('webhooks', [
        'user_id' => $this->user->id,
        'url' => 'https://example.com/webhook',
        'is_active' => true,
    ]);
});

it('can update a webhook', function () {
    $webhook = Webhook::factory()->create(['user_id' => $this->user->id]);

    $response = $this->putJson("/api/v1/webhooks/{$webhook->id}", [
        'url' => 'https://updated.com/webhook',
        'events' => ['instance.connected'],
        'is_active' => false,
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('webhooks', [
        'id' => $webhook->id,
        'url' => 'https://updated.com/webhook',
        'is_active' => false,
    ]);
});

it('can delete a webhook', function () {
    $webhook = Webhook::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/v1/webhooks/{$webhook->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Webhook deleted successfully.',
        ]);

    $this->assertDatabaseMissing('webhooks', ['id' => $webhook->id]);
});
