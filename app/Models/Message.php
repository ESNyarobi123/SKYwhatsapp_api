<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'instance_id',
        'message_id',
        'direction',
        'status',
        'to',
        'from',
        'body',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instance that sent/received the message.
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Extract phone number from JID (Jabber ID).
     * 
     * JID format examples:
     * - 5047106674842@lid (individual chat)
     * - 120363421596028591@g.us (group chat)
     * - 255712345678@s.whatsapp.net (standard format)
     */
    public function extractPhoneNumber(string $jid): ?string
    {
        if (empty($jid)) {
            return null;
        }
        
        // Remove @lid, @g.us, @s.whatsapp.net, etc.
        $phoneNumber = preg_replace('/@.*$/', '', $jid);
        
        // Remove any non-digit characters except +
        $phoneNumber = preg_replace('/[^\d+]/', '', $phoneNumber);
        
        if (empty($phoneNumber)) {
            return null;
        }
        
        // If it's a group ID (starts with many digits, usually 15+), return as is
        if (strlen($phoneNumber) > 15) {
            return $phoneNumber;
        }
        
        // Format phone number for display
        return $this->formatPhoneNumber($phoneNumber);
    }

    /**
     * Format phone number for display.
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove leading + if exists for processing
        $hasPlus = str_starts_with($phoneNumber, '+');
        $digits = $hasPlus ? substr($phoneNumber, 1) : $phoneNumber;
        
        // Handle Tanzanian numbers (255...)
        if (strlen($digits) >= 12 && str_starts_with($digits, '255')) {
            // Format: +255 712 345 678
            $country = substr($digits, 0, 3);
            $operator = substr($digits, 3, 3);
            $rest = substr($digits, 6);
            return ($hasPlus ? '+' : '') . $country . ' ' . $operator . ' ' . $rest;
        }
        
        // Handle local Tanzanian numbers (07... or 06...)
        if (strlen($digits) >= 9 && (str_starts_with($digits, '07') || str_starts_with($digits, '06'))) {
            // Format: 0712 345 678
            $operator = substr($digits, 0, 3);
            $rest = substr($digits, 3);
            return $operator . ' ' . chunk_split($rest, 3, ' ');
        }
        
        // Handle other formats
        if (strlen($digits) >= 10) {
            // Try to format as: XXX XXX XXXX
            return ($hasPlus ? '+' : '') . chunk_split($digits, 3, ' ');
        }
        
        // Return as is if can't format
        return $phoneNumber;
    }

    /**
     * Get formatted phone number for display.
     */
    public function getFormattedPhoneNumberAttribute(): string
    {
        $phoneNumber = $this->direction === 'inbound' 
            ? $this->extractPhoneNumber($this->from)
            : $this->extractPhoneNumber($this->to);
        
        return $phoneNumber ?? ($this->direction === 'inbound' ? $this->from : $this->to);
    }

    /**
     * Get the contact phone number (for inbound messages, it's the sender).
     */
    public function getContactPhoneNumberAttribute(): string
    {
        if ($this->direction === 'inbound') {
            $phone = $this->extractPhoneNumber($this->from);
            return $phone ?? $this->from;
        }
        
        $phone = $this->extractPhoneNumber($this->to);
        return $phone ?? $this->to;
    }

    /**
     * Check if message has media/attachments.
     */
    public function hasMedia(): bool
    {
        $metadata = $this->metadata ?? [];
        return isset($metadata['hasMedia']) && $metadata['hasMedia'] === true;
    }

    /**
     * Get media URL if available.
     */
    public function getMediaUrl(): ?string
    {
        $metadata = $this->metadata ?? [];
        return $metadata['mediaUrl'] ?? $metadata['media_url'] ?? null;
    }

    /**
     * Get media type if available.
     */
    public function getMediaType(): ?string
    {
        $metadata = $this->metadata ?? [];
        return $metadata['mediaType'] ?? $metadata['media_type'] ?? $metadata['mimetype'] ?? null;
    }
}
