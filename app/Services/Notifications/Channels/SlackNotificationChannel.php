<?php

namespace App\Services\Notifications\Channels;

use App\Services\Notifications\Contracts\NotificationChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationChannel implements NotificationChannelInterface
{
    public function send(string $title, string $message, array $data = []): bool
    {
        try {
            $webhookUrl = $data['webhook_url'] ?? config('services.slack.webhook_url');
            if (! $webhookUrl) {
                Log::warning('Slack webhook URL not configured');
                return false;
            }

            $payload = [
                'text' => $title,
                'blocks' => [
                    [
                        'type' => 'header',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => $title,
                        ],
                    ],
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => $message,
                        ],
                    ],
                ],
            ];

            if (! empty($data['org_name'])) {
                $payload['blocks'][] = [
                    'type' => 'context',
                    'elements' => [
                        [
                            'type' => 'mrkdwn',
                            'text' => "Organization: {$data['org_name']}",
                        ],
                    ],
                ];
            }

            Http::post($webhookUrl, $payload);
            return true;
        } catch (\Exception $e) {
            Log::error('Slack notification failed: '.$e->getMessage());
            return false;
        }
    }

    public function getChannelType(): string
    {
        return 'slack';
    }

    public function validateConfig(array $config): array
    {
        $errors = [];
        if (empty($config['webhook_url'] ?? null)) {
            $errors[] = 'webhook_url is required';
        }
        return $errors;
    }
}
