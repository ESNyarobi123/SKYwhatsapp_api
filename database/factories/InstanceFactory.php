<?php

namespace Database\Factories;

use App\Models\Instance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instance>
 */
class InstanceFactory extends Factory
{
    protected $model = Instance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->company().' Instance',
            'phone_number' => '+255'.fake()->numerify('##########'),
            'status' => 'disconnected',
            'qr_code' => null,
            'qr_expires_at' => null,
            'session_data' => null,
            'last_connected_at' => null,
        ];
    }

    /**
     * Indicate that the instance is connected.
     */
    public function connected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'connected',
            'last_connected_at' => now(),
        ]);
    }
}
