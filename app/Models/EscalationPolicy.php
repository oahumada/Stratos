<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EscalationPolicy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'severity',
        'recipients',
        'notification_type',
        'delay_minutes',
        'is_active',
        'escalation_level',
        'description',
    ];

    protected $casts = [
        'recipients' => 'array',
        'delay_minutes' => 'integer',
        'is_active' => 'boolean',
        'escalation_level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ─────────────────────────────────────────────────────── Observers ──────

    protected static function booted(): void
    {
        static::observe(\App\Observers\AuditObserver::class);
    }

    // ─────────────────────────────────────────────────────── Relationships ──

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // Scope helpers
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeForLevel($query, int $level)
    {
        return $query->where('escalation_level', $level);
    }

    // Helper methods
    public function recipientEmails(): array
    {
        $recipients = $this->recipients ?? [];
        return is_array($recipients) ? $recipients : [];
    }

    public function hasSlackNotification(): bool
    {
        return in_array($this->notification_type, ['slack', 'both']);
    }

    public function hasEmailNotification(): bool
    {
        return in_array($this->notification_type, ['email', 'both']);
    }

    public function shouldDelayNotification(): bool
    {
        return $this->delay_minutes > 0;
    }

    // Get escalation chain for a severity
    public static function getChainForSeverity(int $organizationId, string $severity): \Illuminate\Database\Eloquent\Collection
    {
        return self::forOrganization($organizationId)
            ->forSeverity($severity)
            ->active()
            ->orderBy('escalation_level')
            ->get();
    }
}
