<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanningSuccessionPlan extends Model
{
    protected $table = 'workforce_planning_succession_plans';

    protected $fillable = [
        'scenario_id',
        'role_id',
        'department_id',
        'criticality_level',
        'impact_if_vacant',
        'primary_successor_id',
        'secondary_successor_id',
        'tertiary_successor_id',
        'primary_readiness_level',
        'primary_readiness_percentage',
        'primary_gap',
        'development_plan_id',
        'succession_risk',
        'mitigation_actions',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'primary_readiness_percentage' => 'integer',
        'primary_gap' => 'array',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlanningScenario::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Departments::class);
    }

    public function primarySuccessor(): BelongsTo
    {
        return $this->belongsTo(People::class, 'primary_successor_id');
    }

    public function secondarySuccessor(): BelongsTo
    {
        return $this->belongsTo(People::class, 'secondary_successor_id');
    }

    public function tertiarySuccessor(): BelongsTo
    {
        return $this->belongsTo(People::class, 'tertiary_successor_id');
    }

    public function developmentPlan(): BelongsTo
    {
        return $this->belongsTo(DevelopmentPath::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeCritical($query)
    {
        return $query->where('criticality_level', 'critical');
    }

    public function scopeWithoutSuccessor($query)
    {
        return $query->whereNull('primary_successor_id');
    }

    public function scopeAtRisk($query)
    {
        return $query->where('primary_readiness_percentage', '<', 50);
    }
}
