<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'icon',
        'rules',
        'is_active',
        'is_premium',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'rules' => 'array',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
        ];
    }

    /**
     * Get the rules count.
     */
    public function getRulesCountAttribute(): int
    {
        return count($this->rules ?? []);
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for free templates.
     */
    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    /**
     * Scope for category filter.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get available categories.
     */
    public static function getCategories(): array
    {
        return [
            'customer_support' => [
                'name' => 'Customer Support',
                'icon' => '🎧',
                'color' => '#3B82F6',
            ],
            'faq' => [
                'name' => 'FAQ',
                'icon' => '❓',
                'color' => '#8B5CF6',
            ],
            'welcome' => [
                'name' => 'Welcome Messages',
                'icon' => '👋',
                'color' => '#10B981',
            ],
            'order_status' => [
                'name' => 'Order Status',
                'icon' => '📦',
                'color' => '#F59E0B',
            ],
            'marketing' => [
                'name' => 'Marketing',
                'icon' => '📢',
                'color' => '#EC4899',
            ],
            'appointment' => [
                'name' => 'Appointments',
                'icon' => '📅',
                'color' => '#06B6D4',
            ],
        ];
    }
}
