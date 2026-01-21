<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'permissions',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'joined_at' => 'datetime',
        ];
    }

    /**
     * Get the team.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if member has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if member is admin or owner.
     */
    public function isAdminOrOwner(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }

    /**
     * Check if member has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        // Owners have all permissions
        if ($this->role === 'owner') {
            return true;
        }

        // Check role-based permissions
        $rolePermissions = $this->getRolePermissions();
        if (in_array($permission, $rolePermissions)) {
            return true;
        }

        // Check custom permissions
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Get permissions based on role.
     */
    protected function getRolePermissions(): array
    {
        return match ($this->role) {
            'owner' => ['*'], // All permissions
            'admin' => [
                'instances.view', 'instances.create', 'instances.edit', 'instances.delete',
                'messages.view', 'messages.send',
                'bot.view', 'bot.edit',
                'api_keys.view', 'api_keys.create',
                'webhooks.view', 'webhooks.edit',
                'team.view', 'team.invite',
            ],
            'member' => [
                'instances.view',
                'messages.view', 'messages.send',
                'bot.view',
                'team.view',
            ],
            'viewer' => [
                'instances.view',
                'messages.view',
                'team.view',
            ],
            default => [],
        };
    }

    /**
     * Available roles.
     */
    public static function getRoles(): array
    {
        return [
            'admin' => [
                'name' => 'Admin',
                'description' => 'Full access except team ownership',
                'color' => '#8B5CF6',
            ],
            'member' => [
                'name' => 'Member',
                'description' => 'Can send messages and view data',
                'color' => '#3B82F6',
            ],
            'viewer' => [
                'name' => 'Viewer',
                'description' => 'Read-only access',
                'color' => '#6B7280',
            ],
        ];
    }
}
