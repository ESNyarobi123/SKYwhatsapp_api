<?php

namespace Database\Factories;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiKey>
 */
class ApiKeyFactory extends Factory
{
    protected $model = ApiKey::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plainKey = 'sk_'.fake()->unique()->regexify('[A-Za-z0-9]{48}');

        return [
            'user_id' => User::factory(),
            'name' => fake()->word().' API Key',
            'key' => Hash::make($plainKey),
            'key_preview' => substr($plainKey, -8),
            'last_used_at' => null,
            'expires_at' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the API key is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the API key is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
