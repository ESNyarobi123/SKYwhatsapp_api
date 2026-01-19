<?php

use App\Models\Instance;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can list instances for authenticated user', function () {
    Instance::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/api/v1/instances');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'instances',
            ],
        ]);

    expect($response->json('data.instances'))->toHaveCount(3);
});

it('can create a new instance', function () {
    $response = $this->postJson('/api/v1/instances', [
        'name' => 'Test Instance',
        'phone_number' => '+255123456789',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'instance' => ['id', 'name', 'status'],
            ],
            'message',
        ]);

    $this->assertDatabaseHas('instances', [
        'user_id' => $this->user->id,
        'name' => 'Test Instance',
        'status' => 'disconnected',
    ]);
});

it('can get instance details', function () {
    $instance = Instance::factory()->create(['user_id' => $this->user->id]);

    $response = $this->getJson("/api/v1/instances/{$instance->id}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'instance' => ['id', 'name', 'status'],
            ],
        ]);

    expect($response->json('data.instance.id'))->toBe($instance->id);
});

it('can delete an instance', function () {
    $instance = Instance::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/v1/instances/{$instance->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Instance deleted successfully.',
        ]);

    $this->assertDatabaseMissing('instances', ['id' => $instance->id]);
});

it('prevents accessing other users instances', function () {
    $otherUser = User::factory()->create();
    $instance = Instance::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/v1/instances/{$instance->id}");

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'error' => [
                'code' => 'UNAUTHORIZED',
            ],
        ]);
});
