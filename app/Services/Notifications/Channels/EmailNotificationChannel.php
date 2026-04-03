<?php

namespace App\Services\Notifications\Channels;

use App\Services\Notifications\Contracts\NotificationChannelInterface;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationChannel implements NotificationChannelInterface
{
    public function send(string $title, string $message, array $data = []): bool
    {
        try {
            $email = $data['email'] ?? null;
            if (! $email) {
                Log::warning('Email address not provided');
                return false;
            }

            Mail::raw("{$title}\n\n{$message}", function ($msg) use ($email, $title) {
                $msg->to($email)
                    ->subject($title);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email notification failed: '.$e->getMessage());
            return false;
        }
    }

    public function getChannelType(): string
    {
        return 'email';
    }

    public function validateConfig(array $config): array
    {
        // Email doesn't need pre-config; validated at send time
        return [];
    }
}
