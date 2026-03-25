<?php

namespace App\Services\Automation;

use App\Models\Organization;
use App\Models\WebhookRegistry;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * WebhookRegistryService
 *
 * Manages custom webhooks for outbound event notifications.
 * Handles webhook delivery, signing, retry logic, and security.
 */
class WebhookRegistryService
{
    /**
     * Register a new webhook for organization
     */
    public function registerWebhook(
        Organization $organization,
        string $url,
        array $eventFilters = [],
        bool $active = true
    ): WebhookRegistry {
        // Validate URL
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid webhook URL');
        }

        // Generate signing secret
        $secret = Str::random(64);

        return WebhookRegistry::create([
            'organization_id' => $organization->id,
            'webhook_url' => $url,
            'event_filters' => $eventFilters ?: ['*'],  // Subscribe to all events if not specified
            'signing_secret' => Hash::make($secret),
            'raw_secret' => $secret,  // Store raw for return to user (only shown once)
            'is_active' => $active,
            'last_triggered_at' => null,
            'failure_count' => 0,
            'metadata' => [
                'created_user_agent' => request()->userAgent(),
                'created_ip' => request()->ip(),
            ],
        ]);
    }

    /**
     * Update webhook configuration
     */
    public function updateWebhook(
        WebhookRegistry $webhook,
        array $updates
    ): WebhookRegistry {
        $allowed = ['event_filters', 'is_active'];
        $updateData = array_only($updates, $allowed);

        $webhook->update($updateData);

        return $webhook->refresh();
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook(WebhookRegistry $webhook): bool
    {
        return $webhook->delete();
    }

    /**
     * Test webhook delivery
     */
    public function testWebhook(WebhookRegistry $webhook): array
    {
        $testPayload = [
            'event' => 'test.webhook',
            'organization_id' => $webhook->organization_id,
            'timestamp' => now()->toIso8601String(),
            'data' => ['test' => true],
        ];

        return $this->deliverWebhook($webhook, $testPayload);
    }

    /**
     * Deliver event to webhook
     */
    public function deliverWebhook(
        WebhookRegistry $webhook,
        array $payload,
        int $retryCount = 0
    ): array {
        if (! $webhook->is_active) {
            return [
                'webhook_id' => $webhook->id,
                'status' => 'skipped',
                'reason' => 'webhook_inactive',
            ];
        }

        // Check event filters
        if (! $this->eventMatches($payload['event'] ?? 'unknown', $webhook->event_filters)) {
            return [
                'webhook_id' => $webhook->id,
                'status' => 'skipped',
                'reason' => 'event_filtered',
            ];
        }

        try {
            // Generate signature
            $signature = $this->generateSignature($payload, $webhook->raw_secret);

            // Send request
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Webhook-Signature' => $signature,
                    'X-Webhook-ID' => $webhook->id,
                    'X-Organization-ID' => $webhook->organization_id,
                    'X-Delivery-Timestamp' => now()->toIso8601String(),
                    'Content-Type' => 'application/json',
                ])
                ->post($webhook->webhook_url, $payload);

            if ($response->successful()) {
                // Update success metadata
                $webhook->update([
                    'last_triggered_at' => now(),
                    'failure_count' => 0,
                ]);

                Log::info("Webhook delivered successfully: {$webhook->id}");

                return [
                    'webhook_id' => $webhook->id,
                    'status' => 'delivered',
                    'status_code' => $response->status(),
                ];
            } else {
                throw new \Exception("HTTP {$response->status()}: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("Webhook delivery failed: {$e->getMessage()}", [
                'webhook_id' => $webhook->id,
                'retry_count' => $retryCount,
            ]);

            // Increment failure count
            $webhook->increment('failure_count');

            // Retry with exponential backoff
            if ($retryCount < 3) {
                $delaySeconds = 60 * pow(2, $retryCount);  // 60s, 120s, 240s

                // Queue retry (or use job)
                // dispatch(new DeliverWebhookJob($webhook, $payload, $retryCount + 1))
                //     ->delay(now()->addSeconds($delaySeconds));
            }

            return [
                'webhook_id' => $webhook->id,
                'status' => 'failed',
                'error' => $e->getMessage(),
                'retry_count' => $retryCount,
            ];
        }
    }

    /**
     * Broadcast event to matching webhooks
     */
    public function broadcastEvent(
        string $organizationId,
        array $payload
    ): array {
        $webhooks = WebhookRegistry::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->get();

        $results = [];

        foreach ($webhooks as $webhook) {
            $results[] = $this->deliverWebhook($webhook, $payload);
        }

        return [
            'organization_id' => $organizationId,
            'total_webhooks' => count($webhooks),
            'successful' => collect($results)->where('status', 'delivered')->count(),
            'failed' => collect($results)->where('status', 'failed')->count(),
            'deliveries' => $results,
        ];
    }

    /**
     * Check if event matches webhook filters
     */
    protected function eventMatches(string $event, array $filters): bool
    {
        if (in_array('*', $filters)) {
            return true;
        }

        foreach ($filters as $filter) {
            if (str_contains($filter, '*')) {
                // Wildcard matching: "anomaly.*" matches "anomaly.latency"
                $pattern = str_replace('*', '.*', preg_quote($filter, '/'));
                if (preg_match("/^{$pattern}$/", $event)) {
                    return true;
                }
            } elseif ($filter === $event) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate HMAC signature for webhook payload
     */
    protected function generateSignature(array $payload, string $secret): string
    {
        $json = json_encode($payload);

        return hash_hmac('sha256', $json, $secret);
    }

    /**
     * Verify webhook signature (for inbound webhook handlers)
     */
    public static function verifySignature(
        string $signature,
        array $payload,
        string $secret
    ): bool {
        $json = json_encode($payload);
        $expectedSignature = hash_hmac('sha256', $json, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get webhook delivery history (paginated)
     */
    public function getDeliveryHistory(
        WebhookRegistry $webhook,
        int $perPage = 20
    ) {
        // In production, query from dedicated webhook_deliveries table
        // For now, return empty paginated collection
        // DB schema would include: webhook_id, event, payload, response, status, delivered_at, retry_count

        return collect();  // Would be paginated results
    }

    /**
     * Get webhook statistics
     */
    public function getWebhookStats(WebhookRegistry $webhook): array
    {
        return [
            'webhook_id' => $webhook->id,
            'url' => $webhook->webhook_url,
            'is_active' => $webhook->is_active,
            'last_triggered_at' => $webhook->last_triggered_at,
            'failure_count' => $webhook->failure_count,
            'event_filters' => $webhook->event_filters,
            'health' => $webhook->failure_count < 10 ? 'healthy' : ($webhook->failure_count < 50 ? 'degraded' : 'critical'),
        ];
    }
}
