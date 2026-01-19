<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureUsage extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'feature_name',
        'period_type',
        'period_start',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'datetime',
            'usage_count' => 'integer',
        ];
    }

    /**
     * Get the user that owns this feature usage.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription for this feature usage.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
