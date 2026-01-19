<?php

use App\Models\User;

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'user',
            ],
            'message',
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.user.email'))->toBe('test@example.com');
});

it('returns error with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'error' => [
                'code' => 'INVALID_CREDENTIALS',
            ],
        ]);
});

it('requires email and password', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

it('can logout authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/logout');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
});

it('can get authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/user');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);

    expect($response->json('data.user.id'))->toBe($user->id);
});
