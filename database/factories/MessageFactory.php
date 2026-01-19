<?php

namespace Database\Factories;

use App\Models\Instance;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'instance_id' => Instance::factory(),
            'message_id' => 'msg_'.fake()->unique()->regexify('[A-Za-z0-9]{32}'),
            'direction' => fake()->randomElement(['inbound', 'outbound']),
            'status' => fake()->randomElement(['sent', 'delivered', 'read', 'failed']),
            'to' => '+255'.fake()->numerify('##########'),
            'from' => '+255'.fake()->numerify('##########'),
            'body' => fake()->sentence(),
            'metadata' => null,
        ];
    }
}
