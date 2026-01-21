<?php

use App\Models\Package;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define the 5 package tiers
        $packages = [
            [
                'name' => 'Free Trial',
                'description' => 'Perfect for exploring the platform',
                'price' => 0,
                'price_tsz' => 0,
                'price_usd' => 0,
                'price_usdt' => 0,
                'currency' => 'USD',
                'duration_days' => 7,
                'sort_order' => 1,
                'is_active' => true,
                'features' => [
                    'instances' => ['limit' => 1, 'period' => 'lifetime'],
                    'messages' => ['limit' => 50, 'period' => 'day'],
                    'api_keys' => ['limit' => 1, 'period' => 'lifetime'],
                    'bot_type' => 'simple',
                    'bot_rules' => ['limit' => 3, 'period' => 'lifetime'],
                    'bot_menus' => false,
                    'bot_buttons' => false,
                    'bot_analytics' => false,
                    'priority_support' => false,
                ],
            ],
            [
                'name' => 'Starter',
                'description' => 'For small businesses and developers',
                'price' => 25000, // TZS
                'price_tsz' => 25000,
                'price_usd' => 9.99,
                'price_usdt' => 9.99,
                'currency' => 'TZS',
                'duration_days' => 30,
                'sort_order' => 2,
                'is_active' => true,
                'features' => [
                    'instances' => ['limit' => 2, 'period' => 'lifetime'],
                    'messages' => ['limit' => 500, 'period' => 'day'],
                    'api_keys' => ['limit' => 3, 'period' => 'lifetime'],
                    'bot_type' => 'simple',
                    'bot_rules' => ['limit' => 15, 'period' => 'lifetime'],
                    'bot_menus' => false,
                    'bot_buttons' => true,
                    'bot_analytics' => false,
                    'priority_support' => false,
                ],
            ],
            [
                'name' => 'Professional',
                'description' => 'For growing businesses and agencies',
                'price' => 75000, // TZS
                'price_tsz' => 75000,
                'price_usd' => 29.99,
                'price_usdt' => 29.99,
                'currency' => 'TZS',
                'duration_days' => 30,
                'sort_order' => 3,
                'is_active' => true,
                'features' => [
                    'instances' => ['limit' => 5, 'period' => 'lifetime'],
                    'messages' => ['limit' => 3000, 'period' => 'day'],
                    'api_keys' => ['limit' => 10, 'period' => 'lifetime'],
                    'bot_type' => 'advanced',
                    'bot_rules' => ['limit' => 50, 'period' => 'lifetime'],
                    'bot_menus' => true,
                    'bot_buttons' => true,
                    'bot_analytics' => true,
                    'priority_support' => true,
                ],
            ],
            [
                'name' => 'Business',
                'description' => 'For established businesses with multiple teams',
                'price' => 200000, // TZS
                'price_tsz' => 200000,
                'price_usd' => 79.99,
                'price_usdt' => 79.99,
                'currency' => 'TZS',
                'duration_days' => 30,
                'sort_order' => 4,
                'is_active' => true,
                'features' => [
                    'instances' => ['limit' => 15, 'period' => 'lifetime'],
                    'messages' => ['limit' => 10000, 'period' => 'day'],
                    'api_keys' => ['limit' => 25, 'period' => 'lifetime'],
                    'bot_type' => 'advanced',
                    'bot_rules' => ['limit' => 200, 'period' => 'lifetime'],
                    'bot_menus' => true,
                    'bot_buttons' => true,
                    'bot_analytics' => true,
                    'priority_support' => true,
                ],
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large organizations and white-label resellers',
                'price' => 500000, // TZS
                'price_tsz' => 500000,
                'price_usd' => 199.99,
                'price_usdt' => 199.99,
                'currency' => 'TZS',
                'duration_days' => 30,
                'sort_order' => 5,
                'is_active' => true,
                'features' => [
                    'instances' => ['limit' => -1, 'period' => 'lifetime'], // Unlimited
                    'messages' => ['limit' => -1, 'period' => 'day'], // Unlimited
                    'api_keys' => ['limit' => -1, 'period' => 'lifetime'], // Unlimited
                    'bot_type' => 'advanced',
                    'bot_rules' => ['limit' => -1, 'period' => 'lifetime'], // Unlimited
                    'bot_menus' => true,
                    'bot_buttons' => true,
                    'bot_analytics' => true,
                    'priority_support' => true,
                    'white_label' => true,
                    'dedicated_support' => true,
                ],
            ],
        ];

        // First, deactivate all existing packages
        Package::query()->update(['is_active' => false]);

        // Create or update packages based on name
        foreach ($packages as $packageData) {
            Package::updateOrCreate(
                ['name' => $packageData['name']],
                $packageData
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reactivate old packages and deactivate new ones
        Package::whereIn('name', ['Free Trial', 'Starter', 'Professional', 'Business', 'Enterprise'])
            ->update(['is_active' => false]);
    }
};
