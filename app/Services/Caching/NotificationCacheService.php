<?php

namespace App\Services\Caching;

use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Support\Facades\Cache;

class NotificationCacheService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const USER_PREFS_KEY = 'user.notification_prefs:';
    private const ORG_CHANNELS_KEY = 'org.notification_channels:';

    /**
     * Get cached user notification preferences
     */
    public function getUserPreferences(User $user): array
    {
        $key = self::USER_PREFS_KEY . $user->id;

        return Cache::remember($key, self::CACHE_TTL, function () use ($user) {
            return UserNotificationChannel::where('user_id', $user->id)
                ->where('is_active', true)
                ->get(['channel_type', 'channel_config'])
                ->toArray();
        });
    }

    /**
     * Get cached organization notification channels
     */
    public function getOrgChannels(int $organizationId): array
    {
        $key = self::ORG_CHANNELS_KEY . $organizationId;

        return Cache::remember($key, self::CACHE_TTL, function () use ($organizationId) {
            return \App\Models\NotificationChannelSetting::where('organization_id', $organizationId)
                ->where('is_enabled', true)
                ->get(['channel_type', 'global_config'])
                ->toArray();
        });
    }

    /**
     * Invalidate user preferences cache
     */
    public function invalidateUserPreferences(int $userId): void
    {
        Cache::forget(self::USER_PREFS_KEY . $userId);
    }

    /**
     * Invalidate organization channels cache
     */
    public function invalidateOrgChannels(int $organizationId): void
    {
        Cache::forget(self::ORG_CHANNELS_KEY . $organizationId);
    }

    /**
     * Cache notification dispatcher results
     */
    public function cacheDispatchResult(User $user, string $type, array $results, int $ttl = 60): void
    {
        $key = "notification.dispatch:{$user->id}:{$type}:" . now()->minute;
        Cache::put($key, $results, $ttl);
    }

    /**
     * Get cached dispatch results
     */
    public function getDispatchResult(User $user, string $type): ?array
    {
        $key = "notification.dispatch:{$user->id}:{$type}:" . now()->minute;
        return Cache::get($key);
    }

    /**
     * Warm cache for common queries
     */
    public function warmCache(int $organizationId): void
    {
        // Pre-fetch org channels
        $this->getOrgChannels($organizationId);

        // Pre-fetch user prefs for top users
        \App\Models\User::where('organization_id', $organizationId)
            ->orderByDesc('updated_at')
            ->limit(100)
            ->each(fn ($user) => $this->getUserPreferences($user));
    }
}
