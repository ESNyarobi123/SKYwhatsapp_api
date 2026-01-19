<?php

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->subscription = Subscription::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

it('can initiate a payment', function () {
    $response = $this->postJson('/api/payments/initiate', [
        'subscription_id' => $this->subscription->id,
        'provider' => 'mpesa',
        'amount' => 10000.00,
        'phone_number' => '+255123456789',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'payment' => ['id', 'reference', 'status', 'provider'],
            ],
            'message',
        ]);

    $this->assertDatabaseHas('payments', [
        'user_id' => $this->user->id,
        'subscription_id' => $this->subscription->id,
        'provider' => 'mpesa',
        'status' => 'pending',
    ]);
});

it('requires subscription_id, provider, and amount for payment', function () {
    $response = $this->postJson('/api/payments/initiate', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['subscription_id', 'provider', 'amount']);
});

it('can get payment details', function () {
    $payment = Payment::factory()->create([
        'user_id' => $this->user->id,
        'subscription_id' => $this->subscription->id,
    ]);

    $response = $this->getJson("/api/payments/{$payment->id}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'payment' => ['id', 'amount', 'status', 'provider'],
            ],
        ]);
});

it('prevents accessing other users payments', function () {
    $otherUser = User::factory()->create();
    $payment = Payment::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/payments/{$payment->id}");

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'error' => [
                'code' => 'UNAUTHORIZED',
            ],
        ]);
});
