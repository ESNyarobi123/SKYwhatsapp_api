<?php

use App\Models\User;

it('can register a new user', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'user',
            ],
            'message',
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.user.email'))->toBe('test@example.com');
    expect($response->json('data.user.role'))->toBe('user');

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role' => 'user',
    ]);
});

it('requires name, email and password for registration', function () {
    $response = $this->postJson('/api/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('validates email uniqueness', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('requires password confirmation', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
