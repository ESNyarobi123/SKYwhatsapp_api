<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Basic', 'Pro', 'Enterprise', 'Premium']),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5000, 100000),
            'currency' => 'TZS',
            'duration_days' => fake()->randomElement([30, 90, 365]),
            'features' => [
                fake()->word(),
                fake()->word(),
                fake()->word(),
            ],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }

    /**
     * Indicate that the package is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
