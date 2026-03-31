<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiskMitigation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'risk_indicator_id',
        'action_type',
        'description',
        'priority',
        'status',
        'assigned_to',
        'due_date',
        'completion_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completion_date' => 'date',
        ];
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planned', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('due_date', '<', now());
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    // ── Relationships ───────────────────────────────────────────────────────

    public function riskIndicator(): BelongsTo
    {
        return $this->belongsTo(TalentRiskIndicator::class, 'risk_indicator_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'completed' && $this->due_date && $this->due_date->isPast();
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completion_date' => now(),
        ]);
    }
}
