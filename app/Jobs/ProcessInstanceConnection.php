<?php

namespace App\Jobs;

use App\Models\Instance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessInstanceConnection implements ShouldQueue
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
        public Instance $instance
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $serviceUrl = config('services.whatsapp_service.url');

        if (! $serviceUrl) {
            Log::warning('WhatsApp service URL not configured, skipping notification');
            return;
        }

        // Optional: Notify Node.js service about new connection request
        // The Node.js service will also poll for pending connections,
        // so this is just an optimization to speed up the process
        try {
            Http::timeout(5)
                ->withHeaders([
                    'X-API-Key' => config('services.whatsapp_service.api_key'),
                ])
                ->post("{$serviceUrl}/api/notify/connection", [
                    'instance_id' => $this->instance->id,
                ]);
        } catch (\Exception $e) {
            // Silently fail - polling will handle it
            Log::debug('Failed to notify WhatsApp service', [
                'instance_id' => $this->instance->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [5, 30]; // Retry after 5s, 30s
    }
}
