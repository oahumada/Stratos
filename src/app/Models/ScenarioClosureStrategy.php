<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioClosureStrategy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'scenario_id',
        'skill_id',
        'strategy',
        'strategy_name',
        'description',
        'estimated_cost',
        'estimated_time_weeks',
        'success_probability',
        'risk_level',
        'status',
        'action_items',
        'assigned_to',
        'target_completion_date',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'estimated_time_weeks' => 'integer',
        'success_probability' => 'decimal:2',
        'action_items' => 'array',
        'target_completion_date' => 'date',
    ];

    // Relaciones
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlanningScenario::class, 'scenario_id');
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByStrategy($query, $strategy)
    {
        return $query->where('strategy', $strategy);
    }

    // Métodos auxiliares
    public function getStrategyLabel()
    {
        return strtoupper($this->strategy);
    }
}
