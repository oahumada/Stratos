<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DeviceToken - Mobile device push notification tokens
 *
 * Stores FCM (Android) and APNs (iOS) tokens for push notifications.
 *
 * Attributes:
 * - id: Primary key
 * - organization_id: Multi-tenant scope
 * - user_id: Device owner
 * - token: FCM or APNs token
 * - platform: 'android' or 'ios'
 * - is_active: Token is still valid
 * - last_used_at: Last time device received notification
 * - metadata: JSON - app_version, os_version, device_model, push_enabled, etc
 * - created_at, updated_at: Timestamps
 *
 * Relationships:
 * - user: User who owns device
 * - organization: Organization scope
 */
class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_id',
        'token',
        'platform',
        'is_active',
        'last_used_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'token', // Don't expose the actual token in API responses
    ];

    /**
     * Relationship: Device belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Device belongs to Organization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope: Active devices only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Devices by platform
     */
    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope: Recently used (last 24 hours)
     */
    public function scopeRecentlyUsed($query)
    {
        return $query->where('last_used_at', '>', now()->subHours(24));
    }

    /**
     * Get masked token for display
     */
    public function getMaskedTokenAttribute(): string
    {
        return substr($this->token, 0, 20).'...'.substr($this->token, -20);
    }

    /**
     * Check if token is stale (not used in 30 days)
     */
    public function isStale(): bool
    {
        return optional($this->last_used_at)?->diffInDays(now()) > 30;
    }
}
