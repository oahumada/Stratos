<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlertThreshold extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'metric',
        'threshold',
        'severity',
        'is_active',
        'description',
    ];

    protected $casts = [
        'threshold' => 'float',
        'is_active' => 'boolean',
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

    public function alertHistories(): HasMany
    {
        return $this->hasMany(AlertHistory::class);
    }

    // Scope to get active thresholds
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to get thresholds for a specific metric
    public function scopeForMetric($query, string $metric)
    {
        return $query->where('metric', $metric);
    }

    // Scope for organization
    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    // Check if current metric value triggers alert
    public function shouldTrigger(float $currentValue): bool
    {
        return $currentValue >= $this->threshold;
    }

    // Get recent alert history
    public function recentAlerts($minutes = 60): \Illuminate\Database\Eloquent\Collection
    {
        return $this->alertHistories()
            ->where('triggered_at', '>=', now()->subMinutes($minutes))
            ->latest('triggered_at')
            ->get();
    }
}
