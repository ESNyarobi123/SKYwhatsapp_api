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
        $packages = Package::all();

        foreach ($packages as $package) {
            $features = $package->features ?? [];
            $name = strtolower($package->name);

            // Determine bot features based on package tier
            if ($package->isFree()) {
                // Free Trial - Basic bot
                $features['bot_type'] = 'simple';
                $features['bot_rules'] = ['limit' => 3, 'period' => 'lifetime'];
                $features['bot_menus'] = false;
                $features['bot_buttons'] = false;
                $features['bot_analytics'] = false;
                $features['priority_support'] = false;
            } elseif (str_contains($name, 'basic') || str_contains($name, 'starter')) {
                // Basic - Simple bot with more rules
                $features['bot_type'] = 'simple';
                $features['bot_rules'] = ['limit' => 10, 'period' => 'lifetime'];
                $features['bot_menus'] = false;
                $features['bot_buttons'] = false;
                $features['bot_analytics'] = false;
                $features['priority_support'] = false;
            } elseif (str_contains($name, 'pro') || str_contains($name, 'professional') || str_contains($name, 'business')) {
                // Pro - Advanced bot
                $features['bot_type'] = 'advanced';
                $features['bot_rules'] = ['limit' => 50, 'period' => 'lifetime'];
                $features['bot_menus'] = true;
                $features['bot_buttons'] = true;
                $features['bot_analytics'] = false;
                $features['priority_support'] = true;
            } elseif (str_contains($name, 'enterprise') || str_contains($name, 'unlimited') || str_contains($name, 'premium')) {
                // Enterprise - Full bot features
                $features['bot_type'] = 'advanced';
                $features['bot_rules'] = ['limit' => -1, 'period' => 'lifetime']; // Unlimited
                $features['bot_menus'] = true;
                $features['bot_buttons'] = true;
                $features['bot_analytics'] = true;
                $features['priority_support'] = true;
            } else {
                // Default fallback (treat as Basic)
                $features['bot_type'] = 'simple';
                $features['bot_rules'] = ['limit' => 5, 'period' => 'lifetime'];
                $features['bot_menus'] = false;
                $features['bot_buttons'] = false;
                $features['bot_analytics'] = false;
                $features['priority_support'] = false;
            }

            $package->update(['features' => $features]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $packages = Package::all();

        foreach ($packages as $package) {
            $features = $package->features ?? [];
            
            // Remove bot-related features
            unset($features['bot_type']);
            unset($features['bot_rules']);
            unset($features['bot_menus']);
            unset($features['bot_buttons']);
            unset($features['bot_analytics']);
            unset($features['priority_support']);
            
            $package->update(['features' => $features]);
        }
    }
};
