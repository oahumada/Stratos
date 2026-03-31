<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevelopmentPlan extends Model
{
    use BelongsToOrganization, HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'succession_candidate_id',
        'goal_description',
        'target_completion_date',
        'activities',
        'status',
        'progress_pct',
    ];

    protected function casts(): array
    {
        return [
            'target_completion_date' => 'date',
            'activities' => 'array', // [{activity: string, duration_hours: int, status: string}]
            'progress_pct' => 'integer',
        ];
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('target_completion_date', '<', now());
    }

    // ── Relationships ───────────────────────────────────────────────────────

    public function successionCandidate(): BelongsTo
    {
        return $this->belongsTo(SuccessionCandidate::class, 'succession_candidate_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'completed'
            && $this->target_completion_date
            && $this->target_completion_date->isPast();
    }

    public function getActivityCount(): int
    {
        return count($this->activities ?? []);
    }

    public function getCompletedActivityCount(): int
    {
        return collect($this->activities ?? [])
            ->filter(fn ($a) => $a['status'] === 'completed')
            ->count();
    }

    public function getTotalDurationHours(): int
    {
        return collect($this->activities ?? [])
            ->sum('duration_hours');
    }
}
