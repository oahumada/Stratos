<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HybridGatewayService
{
    protected ?string $n8nUrl;

    public function __construct()
    {
        $this->n8nUrl = config('services.n8n.webhook_url');
    }

    /**
     * Trigger an external workflow (e.g., n8n, Zapier) for complex hybrid automation.
     */
    public function triggerExternalWorkflow(string $event, array $payload): bool
    {
        if (!$this->n8nUrl) {
            Log::warning('N8N_WEBHOOK_URL not configured. Hybrid gateway action skipped.');
            return false;
        }

        try {
            $response = Http::timeout(5)
                ->post($this->n8nUrl, [
                    'event' => $event,
                    'timestamp' => now()->toIso8601String(),
                    'payload' => $payload
                ]);

            if ($response->successful()) {
                Log::info("Hybrid gateway triggered for event: $event");
                return true;
            }

            Log::error("Hybrid gateway failed to trigger for event: $event", ['status' => $response->status()]);
            return false;
        } catch (\Exception $e) {
            Log::error("Exception in Hybrid Gateway trigger", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
