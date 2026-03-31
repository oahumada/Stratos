<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuccessionCandidate extends Model
{
    use BelongsToOrganization, HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'scenario_id',
        'person_id',
        'target_role_id',
        'skill_match_score',
        'readiness_level',
        'estimated_months_to_ready',
        'gaps',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'skill_match_score' => 'float',
            'estimated_months_to_ready' => 'integer',
            'gaps' => 'array',
            'metadata' => 'array',
        ];
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeReady($query)
    {
        return $query->whereIn('readiness_level', ['senior', 'expert']);
    }

    public function scopeAtRisk($query)
    {
        return $query->where('skill_match_score', '<', 50)
            ->orWhereIn('readiness_level', ['junior']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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

    public function targetRole(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'target_role_id');
    }

    public function developmentPlan(): HasOne
    {
        return $this->hasOne(DevelopmentPlan::class, 'succession_candidate_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isReady(): bool
    {
        return in_array($this->readiness_level, ['senior', 'expert']);
    }

    public function isAtRisk(): bool
    {
        return $this->skill_match_score < 50 || $this->readiness_level === 'junior';
    }
}
