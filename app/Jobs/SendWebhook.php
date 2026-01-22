<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Services\WebhookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhook implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Webhook $webhook,
        public string $event,
        public array $payload
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WebhookService $webhookService): void
    {
        if (! $this->webhook->is_active) {
            return;
        }

        $payload = [
            'event' => $this->event,
            'data' => $this->payload,
            'timestamp' => now()->toIso8601String(),
        ];

        $payloadJson = json_encode($payload);
        $signature = $webhookService->generateSignature($payloadJson, $this->webhook->secret);

        try {
            $response = Http::timeout(10)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Force IPv4 to fix Loopback/DNS issues
                        CURLOPT_SSL_VERIFYPEER => false,        // Optional: Ignore SSL for internal loopback
                        CURLOPT_SSL_VERIFYHOST => 0,
                    ]
                ])
                ->withHeaders([
                    'X-Signature' => $signature,
                    'X-Event' => $this->event,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->webhook->url, $payload);

            if (! $response->successful()) {
                throw new \Exception("Webhook request failed with status: {$response->status()}");
            }
        } catch (\Exception $e) {
            Log::warning('Webhook delivery failed', [
                'webhook_id' => $this->webhook->id,
                'url' => $this->webhook->url,
                'event' => $this->event,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [5, 30, 120]; // Retry after 5s, 30s, 120s
    }
}
