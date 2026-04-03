<?php

namespace App\Services\Notifications\Contracts;

interface NotificationChannelInterface
{
    /**
     * Send notification via this channel
     *
     * @param string $title
     * @param string $message
     * @param array $data Additional context (user, org, etc.)
     * @return bool Success status
     */
    public function send(string $title, string $message, array $data = []): bool;

    /**
     * Get channel type identifier
     */
    public function getChannelType(): string;

    /**
     * Validate channel configuration
     */
    public function validateConfig(array $config): array; // returns errors or empty array if valid
}
