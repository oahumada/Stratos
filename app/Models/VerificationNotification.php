<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * VerificationNotification - Model for verification system notifications
 *
 * Stores notification history for auditing and user visibility
 */
class VerificationNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'type',
        'data',
        'severity',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Relationship: belongs to organization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope: unread notifications
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope: by type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: by severity
     */
    public function scopeBySeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): self
    {
        $this->update(['read_at' => now()]);

        return $this;
    }

    /**
     * Get human-readable type
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'phase_transition' => 'Phase Transition',
            'alert_threshold' => 'Alert Threshold',
            'violation_detected' => 'Violation Detected',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get summary from data
     */
    public function getSummary(): string
    {
        $data = $this->data;

        return match ($this->type) {
            'phase_transition' => "Phase changed: {$data['from_phase']} → {$data['to_phase']}",
            'alert_threshold' => "{$data['metric_name']} = {$data['current_value']}% (threshold: {$data['threshold']}%)",
            'violation_detected' => "{$data['violation_count']} violations detected by {$data['agent_name']}",
            default => '',
        };
    }
}
