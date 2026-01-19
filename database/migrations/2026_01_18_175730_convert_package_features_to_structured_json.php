<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert existing package features from array of strings to structured JSON
        $packages = DB::table('packages')->whereNotNull('features')->get();

        foreach ($packages as $package) {
            $features = json_decode($package->features, true);

            // Skip if already in structured format or empty
            if (! is_array($features) || empty($features)) {
                continue;
            }

            // Check if it's old format (array of strings)
            if (isset($features[0]) && is_string($features[0])) {
                $structuredFeatures = $this->convertStringFeaturesToStructured($features);

                // Update package with structured features
                DB::table('packages')
                    ->where('id', $package->id)
                    ->update(['features' => json_encode($structuredFeatures)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert structured features back to simple strings (if needed)
        // This is a one-way conversion typically, but we can implement reverse if needed
    }

    /**
     * Convert array of string features to structured JSON format.
     */
    private function convertStringFeaturesToStructured(array $stringFeatures): array
    {
        $structured = [];

        // Try to parse common patterns
        foreach ($stringFeatures as $feature) {
            $feature = strtolower(trim($feature));

            // Parse "X instances" or "Unlimited instances"
            if (preg_match('/(\d+)\s*instances?/i', $feature, $matches)) {
                $structured['instances'] = [
                    'limit' => (int) $matches[1],
                    'period' => 'lifetime',
                ];
            } elseif (preg_match('/unlimited\s*instances?/i', $feature)) {
                $structured['instances'] = [
                    'limit' => -1,
                    'period' => 'lifetime',
                ];
            }

            // Parse "X messages per day/month" or "Unlimited messages"
            if (preg_match('/(\d+)\s*messages?\s*(?:per\s*)?(day|month|year)?/i', $feature, $matches)) {
                $period = isset($matches[2]) ? strtolower($matches[2]) : 'day';
                $structured['messages'] = [
                    'limit' => (int) $matches[1],
                    'period' => $period,
                ];
            } elseif (preg_match('/unlimited\s*messages?/i', $feature)) {
                $structured['messages'] = [
                    'limit' => -1,
                    'period' => 'lifetime',
                ];
            }

            // Parse "X API calls" or similar
            if (preg_match('/(\d+)\s*api\s*calls?/i', $feature, $matches)) {
                $structured['api_calls'] = [
                    'limit' => (int) $matches[1],
                    'period' => 'month',
                ];
            } elseif (preg_match('/unlimited\s*api\s*calls?/i', $feature)) {
                $structured['api_calls'] = [
                    'limit' => -1,
                    'period' => 'lifetime',
                ];
            }

            // Parse "X API keys"
            if (preg_match('/(\d+)\s*api\s*keys?/i', $feature, $matches)) {
                $structured['api_keys'] = [
                    'limit' => (int) $matches[1],
                    'period' => 'lifetime',
                ];
            } elseif (preg_match('/unlimited\s*api\s*keys?/i', $feature)) {
                $structured['api_keys'] = [
                    'limit' => -1,
                    'period' => 'lifetime',
                ];
            }
        }

        // Set default unlimited for features not found
        if (! isset($structured['instances'])) {
            $structured['instances'] = ['limit' => -1, 'period' => 'lifetime'];
        }
        if (! isset($structured['messages'])) {
            $structured['messages'] = ['limit' => -1, 'period' => 'day'];
        }
        if (! isset($structured['api_calls'])) {
            $structured['api_calls'] = ['limit' => -1, 'period' => 'month'];
        }
        if (! isset($structured['api_keys'])) {
            $structured['api_keys'] = ['limit' => -1, 'period' => 'lifetime'];
        }

        return $structured;
    }
};
