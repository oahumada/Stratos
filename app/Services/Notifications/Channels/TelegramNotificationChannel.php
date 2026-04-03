<?php

namespace App\Services\Notifications\Channels;

use App\Services\Notifications\Contracts\NotificationChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationChannel implements NotificationChannelInterface
{
    public function send(string $title, string $message, array $data = []): bool
    {
        try {
            $botToken = $data['bot_token'] ?? config('services.telegram.bot_token');
            $chatId = $data['chat_id'] ?? null;

            if (! $botToken || ! $chatId) {
                Log::warning('Telegram bot_token or chat_id not configured');
                return false;
            }

            $text = "*{$title}*\n\n{$message}";
            if (! empty($data['org_name'])) {
                $text .= "\n\n_Organization: {$data['org_name']}_";
            }

            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram notification failed: '.$e->getMessage());
            return false;
        }
    }

    public function getChannelType(): string
    {
        return 'telegram';
    }

    public function validateConfig(array $config): array
    {
        $errors = [];
        if (empty($config['bot_token'] ?? null)) {
            $errors[] = 'bot_token is required';
        }
        if (empty($config['chat_id'] ?? null)) {
            $errors[] = 'chat_id is required';
        }
        return $errors;
    }
}
