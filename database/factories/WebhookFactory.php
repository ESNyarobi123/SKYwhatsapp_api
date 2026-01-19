<?php

namespace Database\Factories;

use App\Models\Instance;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Webhook>
 */
class WebhookFactory extends Factory
{
    protected $model = Webhook::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'instance_id' => null,
            'url' => fake()->url(),
            'events' => ['message.inbound', 'message.status'],
            'secret' => fake()->regexify('[A-Za-z0-9]{32}'),
            'is_active' => true,
        ];
    }
}
