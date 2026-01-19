<?php

use App\Models\ApiKey;
use App\Models\Instance;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;

it('allows admin to access dashboard', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->getJson('/admin/dashboard');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'overview' => [
                    'users',
                    'subscriptions',
                    'api_keys',
                    'instances',
                    'usage',
                    'revenue',
                ],
            ],
        ]);
});

it('allows admin to access analytics', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->getJson('/admin/analytics');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'analytics' => [
                    'period_days',
                    'users',
                    'subscriptions',
                    'revenue',
                    'usage',
                    'instances',
                ],
            ],
        ]);
});

it('prevents non-admin users from accessing dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/admin/dashboard');

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'error' => [
                'code' => 'FORBIDDEN',
            ],
        ]);
});

it('allows admin to list all users', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(5)->create();

    $response = $this->actingAs($admin)->getJson('/admin/users');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'users',
                'pagination',
            ],
        ]);
});

it('allows admin to list all subscriptions', function () {
    $admin = User::factory()->admin()->create();
    Subscription::factory()->count(3)->create();

    $response = $this->actingAs($admin)->getJson('/admin/subscriptions');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'subscriptions',
                'pagination',
            ],
        ]);
});
