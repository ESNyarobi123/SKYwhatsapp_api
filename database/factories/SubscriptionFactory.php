<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_name' => fake()->randomElement(['Basic', 'Pro', 'Enterprise']),
            'status' => 'active',
            'expires_at' => now()->addMonth(),
            'renewal_date' => now()->addMonth(),
            'amount' => fake()->randomFloat(2, 1000, 50000),
            'payment_provider' => fake()->randomElement(['mpesa', 'tigopesa', 'airtelmoney', 'stripe']),
            'payment_reference' => fake()->unique()->regexify('[A-Z0-9]{16}'),
        ];
    }

    /**
     * Indicate that the subscription is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expires_at' => now()->subDay(),
        ]);
    }
}
