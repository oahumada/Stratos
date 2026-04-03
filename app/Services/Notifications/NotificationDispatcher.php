<?php

namespace App\Services\Notifications;

use App\Models\NotificationChannelSetting;
use App\Models\User;
use App\Models\UserNotificationChannel;
use App\Services\Notifications\Channels\EmailNotificationChannel;
use App\Services\Notifications\Channels\SlackNotificationChannel;
use App\Services\Notifications\Channels\TelegramNotificationChannel;
use App\Services\Notifications\Contracts\NotificationChannelInterface;
use Illuminate\Support\Facades\Log;

class NotificationDispatcher
{
    protected array $channels = [];

    public function __construct()
    {
        $this->registerDefaultChannels();
    }

    protected function registerDefaultChannels(): void
    {
        $this->channels['slack'] = new SlackNotificationChannel();
        $this->channels['telegram'] = new TelegramNotificationChannel();
        $this->channels['email'] = new EmailNotificationChannel();
    }

    /**
     * Dispatch notification to user's enabled channels
     */
    public function dispatchToUser(
        User $user,
        string $title,
        string $message,
        array $context = []
    ): array {
        $results = [];
        $orgId = $user->organization_id ?? $context['organization_id'] ?? null;

        if (! $orgId) {
            Log::warning('No organization_id for user notification dispatch');
            return $results;
        }

        // Get user's active notification channels
        $userChannels = UserNotificationChannel::where('user_id', $user->id)
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->get();

        // If user has no preferences, default to email
        if ($userChannels->isEmpty()) {
            $results['email'] = $this->sendViaChannel(
                'email',
                $title,
                $message,
                array_merge($context, ['email' => $user->email, 'org_name' => $user->organization?->name])
            );
            return $results;
        }

        foreach ($userChannels as $userChannel) {
            $channelConfig = $userChannel->channel_config ?? [];
            $results[$userChannel->channel_type] = $this->sendViaChannel(
                $userChannel->channel_type,
                $title,
                $message,
                array_merge($context, $channelConfig, ['email' => $user->email, 'org_name' => $user->organization?->name])
            );
        }

        return $results;
    }

    /**
     * Dispatch notification to organization-wide enabled channels (broadcast)
     */
    public function dispatchToOrganization(
        int $organizationId,
        string $title,
        string $message,
        array $context = []
    ): array {
        $results = [];

        // Get org's enabled channel settings
        $enabledChannels = NotificationChannelSetting::where('organization_id', $organizationId)
            ->where('is_enabled', true)
            ->get();

        foreach ($enabledChannels as $setting) {
            $config = $setting->global_config ?? [];
            $results[$setting->channel_type] = $this->sendViaChannel(
                $setting->channel_type,
                $title,
                $message,
                array_merge($context, $config)
            );
        }

        return $results;
    }

    /**
     * Send via single channel
     */
    protected function sendViaChannel(
        string $channelType,
        string $title,
        string $message,
        array $data = []
    ): bool {
        if (! isset($this->channels[$channelType])) {
            Log::warning("Unknown notification channel: {$channelType}");
            return false;
        }

        try {
            $channel = $this->channels[$channelType];
            return $channel->send($title, $message, $data);
        } catch (\Exception $e) {
            Log::error("Notification dispatch failed for channel {$channelType}: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Register custom channel
     */
    public function registerChannel(string $type, NotificationChannelInterface $channel): void
    {
        $this->channels[$type] = $channel;
    }

    /**
     * Get available channels
     */
    public function getAvailableChannels(): array
    {
        return array_keys($this->channels);
    }
}
