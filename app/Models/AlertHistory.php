<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertHistory extends Model
{
    protected $fillable = [
        'organization_id',
        'alert_threshold_id',
        'triggered_at',
        'acknowledged_at',
        'acknowledged_by',
        'severity',
        'status',
        'metric_value',
        'notes',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'metric_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function alertThreshold(): BelongsTo
    {
        return $this->belongsTo(AlertThreshold::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    // Scope helpers
    public function scopeTriggered($query)
    {
        return $query->where('status', 'triggered');
    }

    public function scopeAcknowledged($query)
    {
        return $query->where('status', 'acknowledged');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('triggered_at', '>=', now()->subMinutes($minutes))
            ->latest('triggered_at');
    }

    // Helper methods
    public function isUnacknowledged(): bool
    {
        return $this->status === 'triggered';
    }

    public function acknowledge(User $user, ?string $notes = null): void
    {
        $this->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => $user->id,
            'status' => 'acknowledged',
            'notes' => $notes,
        ]);
    }

    public function resolve(): void
    {
        $this->update(['status' => 'resolved']);
    }

    public function mute(): void
    {
        $this->update(['status' => 'muted']);
    }
}
