<?php

namespace App\Services\Lms;

use App\Models\LmsWebhook;
use App\Models\LmsWebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class WebhookService
{
    public function register(int $orgId, string $url, string $secret, array $events): LmsWebhook
    {
        return LmsWebhook::create([
            'organization_id' => $orgId,
            'url' => $url,
            'secret' => $secret,
            'events' => $events,
            'is_active' => true,
            'failure_count' => 0,
        ]);
    }

    public function fire(int $orgId, string $event, array $payload): void
    {
        $webhooks = LmsWebhook::where('organization_id', $orgId)
            ->where('is_active', true)
            ->get()
            ->filter(fn (LmsWebhook $wh) => in_array($event, $wh->events));

        foreach ($webhooks as $webhook) {
            $this->dispatch($webhook, $event, $payload);
        }
    }

    public function logAttempt(int $webhookId, string $event, array $payload, ?int $responseCode, ?string $responseBody, string $status): LmsWebhookLog
    {
        return LmsWebhookLog::create([
            'webhook_id' => $webhookId,
            'event' => $event,
            'payload' => $payload,
            'response_code' => $responseCode,
            'response_body' => $responseBody,
            'status' => $status,
            'attempted_at' => now(),
        ]);
    }

    public function deactivateOnFailure(int $webhookId): void
    {
        $webhook = LmsWebhook::findOrFail($webhookId);
        $webhook->increment('failure_count');

        if ($webhook->failure_count >= 5) {
            $webhook->update(['is_active' => false]);
        }
    }

    public function getForOrganization(int $orgId)
    {
        return LmsWebhook::forOrganization($orgId)->with('logs')->latest()->get();
    }

    public function testWebhook(int $webhookId): LmsWebhookLog
    {
        $webhook = LmsWebhook::findOrFail($webhookId);
        $payload = ['event' => 'test.ping', 'timestamp' => now()->toIso8601String()];

        return $this->dispatch($webhook, 'test.ping', $payload);
    }

    protected function dispatch(LmsWebhook $webhook, string $event, array $payload): LmsWebhookLog
    {
        $signature = hash_hmac('sha256', json_encode($payload), $webhook->secret);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Webhook-Signature' => $signature,
                    'X-Webhook-Event' => $event,
                ])
                ->post($webhook->url, $payload);

            $status = $response->successful() ? 'success' : 'failed';
            $log = $this->logAttempt($webhook->id, $event, $payload, $response->status(), $response->body(), $status);

            $webhook->update(['last_triggered_at' => now()]);

            if ($status === 'failed') {
                $this->deactivateOnFailure($webhook->id);
            } else {
                $webhook->update(['failure_count' => 0]);
            }

            return $log;
        } catch (\Exception $e) {
            $log = $this->logAttempt($webhook->id, $event, $payload, null, $e->getMessage(), 'failed');
            $this->deactivateOnFailure($webhook->id);

            return $log;
        }
    }
}
