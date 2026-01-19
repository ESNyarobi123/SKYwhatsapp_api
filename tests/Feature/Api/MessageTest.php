<?php

use App\Models\Instance;
use App\Models\Message;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->instance = Instance::factory()->connected()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

it('can send a message', function () {
    $response = $this->postJson('/api/v1/messages/send', [
        'instance_id' => $this->instance->id,
        'to' => '+255123456789',
        'body' => 'Test message',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'message' => ['id', 'message_id', 'status'],
            ],
            'message',
        ]);

    $this->assertDatabaseHas('messages', [
        'user_id' => $this->user->id,
        'instance_id' => $this->instance->id,
        'direction' => 'outbound',
        'status' => 'sent',
    ]);
});

it('requires instance to be connected to send message', function () {
    $disconnectedInstance = Instance::factory()->create(['user_id' => $this->user->id, 'status' => 'disconnected']);

    $response = $this->postJson('/api/v1/messages/send', [
        'instance_id' => $disconnectedInstance->id,
        'to' => '+255123456789',
        'body' => 'Test message',
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'error' => [
                'code' => 'INSTANCE_NOT_CONNECTED',
            ],
        ]);
});

it('can list message history', function () {
    Message::factory()->count(5)->create([
        'user_id' => $this->user->id,
        'instance_id' => $this->instance->id,
    ]);

    $response = $this->getJson('/api/v1/messages');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'messages',
                'pagination',
            ],
        ]);

    expect($response->json('data.messages'))->toHaveCount(5);
});

it('can get message details', function () {
    $message = Message::factory()->create([
        'user_id' => $this->user->id,
        'instance_id' => $this->instance->id,
    ]);

    $response = $this->getJson("/api/v1/messages/{$message->id}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'message' => ['id', 'message_id', 'direction', 'status'],
            ],
        ]);

    expect($response->json('data.message.id'))->toBe($message->id);
});
