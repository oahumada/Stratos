<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TalentRiskIndicator extends Model
{
    use BelongsToOrganization, HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'scenario_id',
        'person_id',
        'risk_type',
        'risk_score',
        'last_assessed_at',
        'factors',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'risk_score' => 'float',
            'factors' => 'array',
            'metadata' => 'array',
            'last_assessed_at' => 'datetime',
        ];
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeHigh($query)
    {
        return $query->where('risk_score', '>=', 70);
    }

    public function scopeMedium($query)
    {
        return $query->whereBetween('risk_score', [40, 69]);
    }

    public function scopeLow($query)
    {
        return $query->where('risk_score', '<', 40);
    }

    // ── Relationships ───────────────────────────────────────────────────────

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'person_id');
    }

    public function mitigations(): HasMany
    {
        return $this->hasMany(RiskMitigation::class, 'risk_indicator_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isHighRisk(): bool
    {
        return $this->risk_score >= 70;
    }

    public function isMediumRisk(): bool
    {
        return $this->risk_score >= 40 && $this->risk_score < 70;
    }

    public function getRiskLevel(): string
    {
        return match (true) {
            $this->risk_score >= 70 => 'high',
            $this->risk_score >= 40 => 'medium',
            default => 'low',
        };
    }
}
