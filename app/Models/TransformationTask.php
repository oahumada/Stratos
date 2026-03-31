<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransformationTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'phase_id',
        'task_name',
        'description',
        'owner_id',
        'status',
        'start_date',
        'due_date',
        'completion_date',
        'blockers',
        'dependencies',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'completion_date' => 'date',
            'blockers' => 'array',
            'dependencies' => 'array',
        ];
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('due_date', '<', now());
    }

    // ── Relationships ───────────────────────────────────────────────────────

    public function phase(): BelongsTo
    {
        return $this->belongsTo(TransformationPhase::class, 'phase_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return ! in_array($this->status, ['completed', 'cancelled'])
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completion_date' => now(),
        ]);
    }

    public function getDaysUntilDue(): ?int
    {
        if (! $this->due_date) {
            return null;
        }

        return $this->due_date->diffInDays(now());
    }
}
