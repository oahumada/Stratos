<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransformationPhase extends Model
{
    use BelongsToOrganization, HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'scenario_id',
        'phase_name',
        'phase_number',
        'start_month',
        'duration_months',
        'objectives',
        'headcount_targets',
        'key_milestones',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'phase_number' => 'integer',
            'start_month' => 'integer',
            'duration_months' => 'integer',
            'objectives' => 'array',
            'headcount_targets' => 'array',
            'key_milestones' => 'array',
            'metadata' => 'array',
        ];
    }

    // ── Relationships ───────────────────────────────────────────────────────

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(TransformationTask::class, 'phase_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function getEndMonth(): int
    {
        return $this->start_month + $this->duration_months;
    }

    public function taskCount(): int
    {
        return $this->tasks()->count();
    }

    public function completedTaskCount(): int
    {
        return $this->tasks()->where('status', 'completed')->count();
    }

    public function getCompletionPercentage(): float
    {
        $total = $this->taskCount();
        if ($total === 0) {
            return 0;
        }

        return round(($this->completedTaskCount() / $total) * 100, 2);
    }
}
