<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the active subscription for the user.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    /**
     * Get all API keys for the user.
     */
    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    /**
     * Get all instances for the user.
     */
    public function instances()
    {
        return $this->hasMany(Instance::class);
    }

    /**
     * Get all messages for the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get all usage logs for the user.
     */
    public function usageLogs()
    {
        return $this->hasMany(UsageLog::class);
    }

    /**
     * Get all webhooks for the user.
     */
    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }

    /**
     * Get all payments for the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        $subscription = $this->activeSubscription;

        return $subscription !== null && $subscription->isActive();
    }

    /**
     * Check if user's active subscription is free trial.
     */
    public function isFreeTrial(): bool
    {
        $subscription = $this->activeSubscription;
        if (! $subscription || ! $subscription->isActive()) {
            return false;
        }

        $package = $subscription->package;

        return $package && $package->isFree();
    }

    /**
     * Check if user can create more instances.
     */
    public function canCreateInstance(): bool
    {
        $service = app(\App\Services\FeatureLimitService::class);

        return $service->canCreateInstance($this);
    }

    /**
     * Check if user can create more API keys.
     */
    public function canCreateApiKey(): bool
    {
        $service = app(\App\Services\FeatureLimitService::class);

        return $service->canCreateApiKey($this);
    }

    /**
     * Check if user can send messages (messages per day limit).
     */
    public function canSendMessage(): bool
    {
        $service = app(\App\Services\FeatureLimitService::class);

        return $service->canSendMessage($this);
    }

    /**
     * Get all support tickets created by the user.
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Get all support tickets assigned to this user (admin).
     */
    public function assignedTickets()
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    /**
     * Get feature usage for a specific feature.
     */
    public function getFeatureUsage(string $featureName): array
    {
        $service = app(\App\Services\FeatureLimitService::class);
        $stats = $service->getFeatureUsageStats($this);

        return $stats[$featureName] ?? [
            'limit' => null,
            'usage' => 0,
            'remaining' => null,
            'period' => 'lifetime',
            'is_unlimited' => true,
        ];
    }
}
