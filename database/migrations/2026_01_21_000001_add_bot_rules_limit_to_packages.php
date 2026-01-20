<?php

use App\Models\Package;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing packages to include bot_rules limit
        $packages = Package::all();

        foreach ($packages as $package) {
            $features = $package->features;

            // Define limits based on package name or price (heuristic)
            // You can adjust these values as needed
            if ($package->isFree()) {
                $limit = 5;
            } elseif (stripos($package->name, 'pro') !== false || stripos($package->name, 'enterprise') !== false || stripos($package->name, 'unlimited') !== false) {
                $limit = -1; // Unlimited
            } else {
                $limit = 20; // Standard/Basic
            }

            $features['bot_rules'] = [
                'limit' => $limit,
                'period' => 'lifetime' // Rules are persistent, not periodic
            ];

            $package->update(['features' => $features]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove bot_rules from features
        $packages = Package::all();

        foreach ($packages as $package) {
            $features = $package->features;
            if (isset($features['bot_rules'])) {
                unset($features['bot_rules']);
                $package->update(['features' => $features]);
            }
        }
    }
};
