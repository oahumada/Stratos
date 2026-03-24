<?php

namespace App\Services\Mobile;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;

/**
 * DeviceTokenService - Centralized device token management
 *
 * Responsibilities:
 * 1. Register new device tokens
 * 2. Update existing tokens (refresh, platform change)
 * 3. Deactivate tokens (logout, uninstall)
 * 4. Get active tokens for user/organization
 * 5. Track device metadata (app version, OS version, etc)
 * 6. Cleanup stale tokens
 *
 * Multi-tenancy:
 * - All operations scoped by organization_id
 * - Users can have multiple devices (iOS + Android)
 * - Each user can have multiple tokens per platform (e.g., old phone + new phone)
 */
class DeviceTokenService
{
    /**
     * Register or update device token
     */
    public function register(
        int $userId,
        int $organizationId,
        string $token,
        string $platform,
        ?array $metadata = null
    ): DeviceToken {
        $existing = DeviceToken::where('organization_id', $organizationId)
            ->where('user_id', $userId)
            ->where('platform', $platform)
            ->where('token', $token)
            ->first();

        if ($existing) {
            // Token already registered, just update metadata
            $existing->update([
                'is_active' => true,
                'last_used_at' => now(),
                'metadata' => array_merge($existing->metadata ?? [], $metadata ?? []),
            ]);

            return $existing;
        }

        // Check if user has too many devices on this platform (cleanup old ones)
        $existingDevices = DeviceToken::where('user_id', $userId)
            ->where('platform', $platform)
            ->where('organization_id', $organizationId)
            ->orderBy('last_used_at', 'asc')
            ->get();

        if ($existingDevices->count() >= 5) {
            // Deactivate oldest device
            $existingDevices->first()->update(['is_active' => false]);

            Log::info('Old device token deactivated', [
                'user_id' => $userId,
                'platform' => $platform,
            ]);
        }

        // Register new device
        $deviceToken = DeviceToken::create([
            'user_id' => $userId,
            'organization_id' => $organizationId,
            'token' => $token,
            'platform' => $platform,
            'is_active' => true,
            'last_used_at' => now(),
            'metadata' => $metadata ?? [],
        ]);

        Log::info('Device token registered', [
            'device_id' => $deviceToken->id,
            'user_id' => $userId,
            'platform' => $platform,
        ]);

        return $deviceToken;
    }

    /**
     * Update device metadata (app version, push settings, etc)
     */
    public function updateMetadata(
        int $deviceId,
        array $metadata
    ): bool {
        return DeviceToken::find($deviceId)?->update([
            'metadata' => array_merge(
                DeviceToken::find($deviceId)->metadata ?? [],
                $metadata
            ),
            'last_used_at' => now(),
        ]) ?? false;
    }

    /**
     * Mark device as inactive (logout)
     */
    public function deactivate(int $deviceId): bool
    {
        return DeviceToken::find($deviceId)?->update([
            'is_active' => false,
            'last_used_at' => now(),
        ]) ?? false;
    }

    /**
     * Get active devices for user
     */
    public function getActiveDevices(int $userId, int $organizationId): array
    {
        return DeviceToken::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->get()
            ->map(fn ($device) => [
                'id' => $device->id,
                'platform' => $device->platform,
                'token' => substr($device->token, 0, 20).'...', // Mask token
                'last_used_at' => $device->last_used_at->toIso8601String(),
                'metadata' => $device->metadata,
            ])
            ->toArray();
    }

    /**
     * Check if user has any active devices
     */
    public function hasActiveDevices(int $userId, int $organizationId): bool
    {
        return DeviceToken::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get preferred device for notifications (most recently used)
     */
    public function getPreferredDevice(int $userId, int $organizationId): ?DeviceToken
    {
        return DeviceToken::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->latest('last_used_at')
            ->first();
    }

    /**
     * Clean up inactive devices older than 30 days
     */
    public function cleanupInactiveDevices(int $organizationId, int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);

        $count = DeviceToken::where('organization_id', $organizationId)
            ->where('is_active', false)
            ->where('last_used_at', '<', $cutoffDate)
            ->delete();

        Log::info('Cleaned up inactive device tokens', [
            'organization_id' => $organizationId,
            'count' => $count,
        ]);

        return $count;
    }

    /**
     * Get device token statistics for organization
     */
    public function getOrganizationStats(int $organizationId): array
    {
        $all = DeviceToken::where('organization_id', $organizationId)->count();
        $active = DeviceToken::where('organization_id', $organizationId)->where('is_active', true)->count();
        $ios = DeviceToken::where('organization_id', $organizationId)->where('platform', 'ios')->count();
        $android = DeviceToken::where('organization_id', $organizationId)->where('platform', 'android')->count();

        return [
            'total_tokens' => $all,
            'active_tokens' => $active,
            'inactive_tokens' => $all - $active,
            'ios_tokens' => $ios,
            'android_tokens' => $android,
            'ios_percentage' => $all > 0 ? round(($ios / $all) * 100, 2) : 0,
            'android_percentage' => $all > 0 ? round(($android / $all) * 100, 2) : 0,
        ];
    }

    /**
     * Validate device token format
     */
    public function validateToken(string $token, string $platform): bool
    {
        if (empty($token)) {
            return false;
        }

        // FCM tokens: typically 100-500 alphanumeric + underscores/hyphens, or base64
        if ($platform === 'android') {
            // Allow alphanumeric, underscore, hyphen, or base64 characters (including +, /, =)
            return strlen($token) >= 50 && preg_match('/^[a-zA-Z0-9_\-\/\+=]+$/', $token);
        }

        // APNs tokens: hex format, 64 chars
        if ($platform === 'ios') {
            return strlen($token) === 64 && ctype_xdigit($token);
        }

        return false;
    }
}
