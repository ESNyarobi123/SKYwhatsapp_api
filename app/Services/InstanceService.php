<?php

namespace App\Services;

use App\Jobs\ProcessInstanceConnection;
use App\Models\Instance;
use App\Models\User;

class InstanceService
{
    /**
     * Create a new instance for the user.
     */
    public function create(User $user, string $name, ?string $phoneNumber = null): Instance
    {
        return Instance::create([
            'user_id' => $user->id,
            'name' => $name,
            'phone_number' => $phoneNumber,
            'status' => 'disconnected',
        ]);
    }

    /**
     * Update instance status.
     */
    public function updateStatus(Instance $instance, string $status): bool
    {
        return $instance->update([
            'status' => $status,
            'last_connected_at' => $status === 'connected' ? now() : $instance->last_connected_at,
        ]);
    }

    /**
     * Store QR code for instance.
     */
    public function storeQrCode(Instance $instance, string $qrCode, int $ttlMinutes = 5): bool
    {
        return $instance->update([
            'qr_code' => $qrCode,
            'qr_expires_at' => now()->addMinutes($ttlMinutes),
        ]);
    }

    /**
     * Notify Node.js service about instance connection request.
     */
    public function notifyConnectionRequest(Instance $instance): void
    {
        ProcessInstanceConnection::dispatch($instance);
    }
}
