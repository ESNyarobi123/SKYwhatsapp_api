<?php

namespace App\Services;

use App\Models\FeatureUsage;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class FeatureLimitService
{
    /**
     * Check if user can perform action based on feature limit.
     */
    public function checkLimit(User $user, string $featureName): bool
    {
        $subscription = $user->activeSubscription;

        if (! $subscription || ! $subscription->isActive()) {
            return false;
        }

        $package = $subscription->package;

        if (! $package) {
            return false;
        }

        $limit = $package->getFeatureLimit($featureName);

        // If limit is null or -1, feature is unlimited
        if ($limit === null || $limit === -1) {
            return true;
        }

        $usage = $this->getUsageCount($user, $featureName);

        return $usage < $limit;
    }

    /**
     * Get current usage count for a feature.
     */
    public function getUsageCount(User $user, string $featureName, ?string $period = null): int
    {
        $subscription = $user->activeSubscription;

        if (! $subscription) {
            return 0;
        }

        $package = $subscription->package;

        if (! $package) {
            return 0;
        }

        // Get period from package if not provided
        if (! $period) {
            $period = $package->getFeaturePeriod($featureName) ?? 'lifetime';
        }

        // For lifetime features (instances, api_keys), count actual records
        if ($period === 'lifetime' && in_array($featureName, ['instances', 'api_keys'])) {
            return match ($featureName) {
                'instances' => $user->instances()->count(),
                'api_keys' => $user->apiKeys()->count(),
                default => 0,
            };
        }

        // For period-based features, use usage counter
        $periodStart = $this->getPeriodStart($period, $subscription);

        // Find or create feature usage record
        $featureUsage = FeatureUsage::firstOrCreate(
            [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'feature_name' => $featureName,
                'period_type' => $period,
            ],
            [
                'period_start' => $periodStart,
                'usage_count' => 0,
            ]
        );

        // If period has changed (e.g., new day/month), reset usage
        if ($featureUsage->period_start && $featureUsage->period_start->ne($periodStart)) {
            $featureUsage->update([
                'period_start' => $periodStart,
                'usage_count' => 0,
            ]);
        }

        return $featureUsage->usage_count;
    }

    /**
     * Get remaining limit for a feature.
     */
    public function getRemainingLimit(User $user, string $featureName): ?int
    {
        $subscription = $user->activeSubscription;

        if (! $subscription) {
            return null;
        }

        $package = $subscription->package;

        if (! $package) {
            return null;
        }

        $limit = $package->getFeatureLimit($featureName);

        // If unlimited, return null
        if ($limit === null || $limit === -1) {
            return null;
        }

        $usage = $this->getUsageCount($user, $featureName);

        return max(0, $limit - $usage);
    }

    /**
     * Increment usage count for a feature.
     */
    public function incrementUsage(User $user, string $featureName): void
    {
        $subscription = $user->activeSubscription;

        if (! $subscription) {
            return;
        }

        $package = $subscription->package;

        if (! $package) {
            return;
        }

        $period = $package->getFeaturePeriod($featureName) ?? 'lifetime';

        // For lifetime features (instances, api_keys), usage is tracked by actual record count
        // No need to increment counter - it's counted dynamically
        if ($period === 'lifetime' && in_array($featureName, ['instances', 'api_keys'])) {
            return;
        }

        // For period-based features, use usage counter
        $periodStart = $this->getPeriodStart($period, $subscription);

        // Find or create feature usage record
        $featureUsage = FeatureUsage::firstOrCreate(
            [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'feature_name' => $featureName,
                'period_type' => $period,
            ],
            [
                'period_start' => $periodStart,
                'usage_count' => 0,
            ]
        );

        // If period has changed, reset usage before incrementing
        if ($featureUsage->period_start && $featureUsage->period_start->ne($periodStart)) {
            $featureUsage->update([
                'period_start' => $periodStart,
                'usage_count' => 1,
            ]);
        } else {
            $featureUsage->increment('usage_count');
        }
    }

    /**
     * Check if user can create more instances.
     */
    public function canCreateInstance(User $user): bool
    {
        if (! $user->hasActiveSubscription()) {
            return false;
        }

        return $this->checkLimit($user, 'instances');
    }

    /**
     * Check if user can send messages (messages per day limit).
     */
    public function canSendMessage(User $user): bool
    {
        if (! $user->hasActiveSubscription()) {
            return false;
        }

        return $this->checkLimit($user, 'messages');
    }

    /**
     * Check if user can create more API keys.
     */
    public function canCreateApiKey(User $user): bool
    {
        if (! $user->hasActiveSubscription()) {
            return false;
        }

        return $this->checkLimit($user, 'api_keys');
    }

    /**
     * Check if user can make API calls.
     */
    public function canMakeApiCall(User $user): bool
    {
        if (! $user->hasActiveSubscription()) {
            return false;
        }

        return $this->checkLimit($user, 'api_calls');
    }

    /**
     * Get period start datetime based on period type.
     */
    private function getPeriodStart(string $period, ?Subscription $subscription = null): Carbon
    {
        return match ($period) {
            'day' => now()->startOfDay(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => $subscription?->created_at?->startOfDay() ?? now()->startOfDay(), // lifetime uses subscription start
        };
    }

    /**
     * Get all feature usage statistics for a user.
     */
    public function getFeatureUsageStats(User $user): array
    {
        $subscription = $user->activeSubscription;

        if (! $subscription || ! $subscription->package) {
            return [];
        }

        $package = $subscription->package;
        $features = $package->features ?? [];

        if (! is_array($features)) {
            return [];
        }

        $stats = [];

        foreach ($features as $featureName => $featureConfig) {
            if (! is_array($featureConfig) || ! isset($featureConfig['limit'])) {
                continue;
            }

            $limit = $featureConfig['limit'];
            $usage = $this->getUsageCount($user, $featureName);
            $remaining = $limit === -1 || $limit === null ? null : max(0, $limit - $usage);

            $stats[$featureName] = [
                'limit' => $limit === -1 ? null : $limit,
                'usage' => $usage,
                'remaining' => $remaining,
                'period' => $featureConfig['period'] ?? 'lifetime',
                'is_unlimited' => $limit === -1 || $limit === null,
            ];
        }

        return $stats;
    }
}
