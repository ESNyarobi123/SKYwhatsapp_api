<?php

use App\Models\Subscription;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can list subscriptions for authenticated user', function () {
    Subscription::factory()->count(2)->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/api/subscriptions');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'subscriptions',
            ],
        ]);

    expect($response->json('data.subscriptions'))->toHaveCount(2);
});

it('can get active subscription', function () {
    Subscription::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'active',
        'expires_at' => now()->addMonth(),
    ]);

    $response = $this->getJson('/api/subscriptions/active');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'subscription' => ['id', 'status', 'expires_at'],
            ],
        ]);
});

it('returns error when no active subscription', function () {
    $response = $this->getJson('/api/subscriptions/active');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'error' => [
                'code' => 'NO_ACTIVE_SUBSCRIPTION',
            ],
        ]);
});

it('can get subscription details', function () {
    $subscription = Subscription::factory()->create(['user_id' => $this->user->id]);

    $response = $this->getJson("/api/subscriptions/{$subscription->id}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'subscription' => ['id', 'plan_name', 'status'],
            ],
        ]);
});

it('can cancel a subscription', function () {
    $subscription = Subscription::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'active',
    ]);

    $response = $this->postJson("/api/subscriptions/{$subscription->id}/cancel");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Subscription cancelled successfully.',
        ]);

    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'status' => 'cancelled',
    ]);
});
