<?php

namespace App\Console\Commands;

use App\Models\ScheduledMessage;
use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendScheduledMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled messages that are due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dueMessages = ScheduledMessage::due()
            ->with(['instance', 'user'])
            ->get();

        if ($dueMessages->isEmpty()) {
            $this->info('No scheduled messages to send.');
            return 0;
        }

        $this->info("Found {$dueMessages->count()} scheduled messages to send.");

        foreach ($dueMessages as $scheduledMessage) {
            try {
                // Skip if instance is not connected
                if (!$scheduledMessage->instance || $scheduledMessage->instance->status !== 'connected') {
                    $scheduledMessage->markAsFailed('Instance is not connected');
                    $this->warn("Skipped: Instance not connected for message #{$scheduledMessage->id}");
                    continue;
                }

                // Prepare the message
                $recipient = $scheduledMessage->recipient;
                
                // Format phone number if needed
                if (!str_contains($recipient, '@')) {
                    $recipient = preg_replace('/[^0-9]/', '', $recipient);
                    $recipient = $recipient . '@s.whatsapp.net';
                }

                // Send the message via the WhatsApp instance
                $response = $this->sendMessage(
                    $scheduledMessage->instance,
                    $recipient,
                    $scheduledMessage->content,
                    $scheduledMessage->message_type
                );

                if ($response['success']) {
                    // Mark as sent
                    $scheduledMessage->markAsSent();
                    
                    // Log the message
                    Message::create([
                        'user_id' => $scheduledMessage->user_id,
                        'instance_id' => $scheduledMessage->instance_id,
                        'direction' => 'outbound',
                        'status' => 'sent',
                        'to' => $scheduledMessage->recipient,
                        'body' => $scheduledMessage->content,
                        'message_type' => $scheduledMessage->message_type,
                    ]);

                    $this->info("Sent scheduled message #{$scheduledMessage->id}");
                } else {
                    $scheduledMessage->markAsFailed($response['error'] ?? 'Unknown error');
                    $this->error("Failed to send message #{$scheduledMessage->id}: " . ($response['error'] ?? 'Unknown error'));
                }
            } catch (\Exception $e) {
                $scheduledMessage->markAsFailed($e->getMessage());
                $this->error("Exception for message #{$scheduledMessage->id}: {$e->getMessage()}");
                Log::error('Scheduled message send error', [
                    'message_id' => $scheduledMessage->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return 0;
    }

    /**
     * Send message via WhatsApp instance.
     */
    protected function sendMessage($instance, $to, $content, $type = 'text'): array
    {
        try {
            $waApiUrl = config('services.whatsapp.api_url', 'http://localhost:3000');
            
            $endpoint = match ($type) {
                'image' => 'send-image',
                'document' => 'send-document',
                default => 'send-message',
            };

            $response = Http::timeout(30)->post("{$waApiUrl}/{$endpoint}", [
                'instance_id' => $instance->instance_id,
                'to' => $to,
                'message' => $content,
            ]);

            if ($response->successful()) {
                return ['success' => true];
            }

            return [
                'success' => false,
                'error' => $response->json('error') ?? $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
