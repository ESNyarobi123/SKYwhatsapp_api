<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    /** @use HasFactory<\Database\Factories\PackageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'price_tsz',
        'price_usd',
        'price_usdt',
        'duration_days',
        'features',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_tsz' => 'decimal:2',
            'price_usd' => 'decimal:2',
            'price_usdt' => 'decimal:2',
            'duration_days' => 'integer',
            'features' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get all subscriptions for this package.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if package is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if package is free (all prices = 0).
     */
    public function isFree(): bool
    {
        $usdPrice = (float) ($this->price_usd ?? $this->price ?? 0);
        $tszPrice = (float) ($this->price_tsz ?? 0);
        $usdtPrice = (float) ($this->price_usdt ?? 0);

        return $usdPrice === 0.0 && $tszPrice === 0.0 && $usdtPrice === 0.0;
    }

    /**
     * Get price for a specific currency with automatic conversion.
     * Base currency is USD. Converts from USD to target currency.
     */
    public function getPriceForCurrency(string $currency): float
    {
        // Get base price in USD
        $basePriceUsd = (float) ($this->price_usd ?? $this->price ?? 0);

        if ($basePriceUsd === 0.0) {
            return 0.0;
        }

        // Convert based on currency
        $converter = app(\App\Services\CurrencyConversionService::class);

        return match (strtoupper($currency)) {
            'USD' => round($basePriceUsd, 2),
            'TZS' => round($converter->fromUsd($basePriceUsd, 'TZS'), 2),
            'USDT' => round($converter->fromUsd($basePriceUsd, 'USDT'), 2),
            default => round($basePriceUsd, 2), // Default to USD
        };
    }

    /**
     * Get currency based on payment method.
     */
    public function getCurrencyForPaymentMethod(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'zenopay_mobile' => 'TZS',
            'paypal' => 'USD',
            'trc20' => 'USDT',
            default => 'USD',
        };
    }

    /**
     * Get feature limit value for a specific feature.
     * Returns limit value, null if unlimited, or null if feature doesn't exist.
     */
    public function getFeatureLimit(string $featureName): ?int
    {
        $features = $this->features;

        // If features is array of strings (old format), return null (unlimited)
        if (is_array($features) && isset($features[0]) && is_string($features[0])) {
            return null; // Old format, treat as unlimited
        }

        if (! is_array($features) || ! isset($features[$featureName])) {
            return null;
        }

        $featureConfig = $features[$featureName];

        if (! is_array($featureConfig) || ! isset($featureConfig['limit'])) {
            return null;
        }

        $limit = $featureConfig['limit'];

        // -1 means unlimited
        return $limit === -1 ? null : (int) $limit;
    }

    /**
     * Get period type for a specific feature.
     */
    public function getFeaturePeriod(string $featureName): ?string
    {
        $features = $this->features;

        // If features is array of strings (old format), return default based on feature name
        if (is_array($features) && isset($features[0]) && is_string($features[0])) {
            // Default periods for old format
            return match ($featureName) {
                'instances', 'api_keys' => 'lifetime',
                'messages' => 'day',
                'api_calls' => 'month',
                default => 'lifetime',
            };
        }

        if (! is_array($features) || ! isset($features[$featureName])) {
            // Default periods if feature not found
            return match ($featureName) {
                'instances', 'api_keys' => 'lifetime',
                'messages' => 'day',
                'api_calls' => 'month',
                default => 'lifetime',
            };
        }

        $featureConfig = $features[$featureName];

        if (! is_array($featureConfig) || ! isset($featureConfig['period'])) {
            // Default periods if period not set
            return match ($featureName) {
                'instances', 'api_keys' => 'lifetime',
                'messages' => 'day',
                'api_calls' => 'month',
                default => 'lifetime',
            };
        }

        return $featureConfig['period'];
    }

    /**
     * Check if package has a limit defined for a feature.
     */
    public function hasFeatureLimit(string $featureName): bool
    {
        return $this->getFeatureLimit($featureName) !== null;
    }
}
