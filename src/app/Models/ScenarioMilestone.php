<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioMilestone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'scenario_id',
        'name',
        'description',
        'target_date',
        'actual_date',
        'status',
        'completion_percentage',
        'deliverables',
        'dependencies',
        'owner_id',
        'notes',
    ];

    protected $casts = [
        'target_date' => 'date',
        'actual_date' => 'date',
        'completion_percentage' => 'integer',
        'deliverables' => 'array',
        'dependencies' => 'array',
    ];

    // Relaciones
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(StrategicPlanningScenarios::class, 'scenario_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    // Métodos auxiliares
    public function isOverdue()
    {
        return $this->target_date < now() && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function markCompleted()
    {
        $this->update([
            'status' => 'completed',
            'actual_date' => now(),
            'completion_percentage' => 100,
        ]);
    }
}
