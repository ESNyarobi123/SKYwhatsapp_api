<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter',
                'description' => 'Perfect for small businesses getting started with WhatsApp API - Free Trial',
                'price' => 0.00,
                'currency' => 'TZS',
                'duration_days' => 30,
                'features' => [
                    'Up to 1,000 messages/month',
                    '1 WhatsApp instance',
                    '1 API key',
                    'Basic webhook support',
                    'Email support',
                    'API documentation access',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Business',
                'description' => 'Ideal for growing businesses with higher messaging needs',
                'price' => 35000.00,
                'currency' => 'TZS',
                'duration_days' => 30,
                'features' => [
                    'Up to 5,000 messages/month',
                    'Up to 3 WhatsApp instances',
                    'Advanced webhook integration',
                    'Priority email support',
                    'Message templates support',
                    'Usage analytics dashboard',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Professional',
                'description' => 'For established businesses requiring advanced features',
                'price' => 75000.00,
                'currency' => 'TZS',
                'duration_days' => 30,
                'features' => [
                    'Up to 15,000 messages/month',
                    'Up to 10 WhatsApp instances',
                    'Custom webhook endpoints',
                    '24/7 priority support',
                    'Bulk messaging capabilities',
                    'Media attachments (images, videos)',
                    'Message scheduling',
                    'Advanced analytics & reporting',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large organizations with high-volume messaging requirements',
                'price' => 150000.00,
                'currency' => 'TZS',
                'duration_days' => 30,
                'features' => [
                    'Unlimited messages/month',
                    'Unlimited WhatsApp instances',
                    'Custom API integrations',
                    'Dedicated account manager',
                    'Advanced bulk messaging',
                    'Full media support',
                    'AI-powered responses',
                    'Multi-language support',
                    'Custom reporting & analytics',
                    'SLA guarantee',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Annual Pro',
                'description' => 'Professional plan with 2 months free when paid annually',
                'price' => 750000.00,
                'currency' => 'TZS',
                'duration_days' => 365,
                'features' => [
                    'Up to 15,000 messages/month',
                    'Up to 10 WhatsApp instances',
                    'Custom webhook endpoints',
                    '24/7 priority support',
                    'Bulk messaging capabilities',
                    'Media attachments (images, videos)',
                    'Message scheduling',
                    'Advanced analytics & reporting',
                    'Save 2 months (17% discount)',
                ],
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($packages as $package) {
            Package::updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }
    }
}
