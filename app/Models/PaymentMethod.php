<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PaymentMethod extends Model
{
    protected $fillable = [
        'method',
        'name',
        'is_enabled',
        'config',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'config' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Check if a specific payment method is enabled.
     */
    public static function isEnabled(string $method): bool
    {
        return static::where('method', $method)->where('is_enabled', true)->exists();
    }

    /**
     * Get all enabled payment methods.
     */
    public static function getEnabledMethods(): \Illuminate\Database\Eloquent\Collection
    {
        return static::enabled()->orderBy('sort_order')->get();
    }

    /**
     * Get configuration for a specific payment method.
     */
    public static function getConfig(string $method): ?array
    {
        $paymentMethod = static::where('method', $method)->first();

        return $paymentMethod?->config;
    }

    /**
     * Scope to filter enabled methods.
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
