<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkforcePlanningScenario extends Model
{
    use HasFactory;

    protected $table = 'workforce_planning_scenarios';

    protected $fillable = [
        'organization_id',
        'template_id',
        'name',
        'description',
        'scenario_type',
        'target_date',
        'time_horizon_weeks',
        'horizon_months',
        'status',
        'assumptions',
        'custom_config',
        'estimated_budget',
        'fiscal_year',
        'owner',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'horizon_months' => 'integer',
        'time_horizon_weeks' => 'integer',
        'target_date' => 'date',
        'assumptions' => 'array',
        'custom_config' => 'array',
        'estimated_budget' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function roleForecasts(): HasMany
    {
        return $this->hasMany(WorkforcePlanningRoleForecast::class, 'scenario_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(WorkforcePlanningMatch::class, 'scenario_id');
    }

    public function skillGaps(): HasMany
    {
        return $this->hasMany(WorkforcePlanningSkillGap::class, 'scenario_id');
    }

    public function successionPlans(): HasMany
    {
        return $this->hasMany(WorkforcePlanningSuccessionPlan::class, 'scenario_id');
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(WorkforcePlanningAnalytic::class, 'scenario_id');
    }

    // Nuevas relaciones para Scenario Modeling
    public function template(): BelongsTo
    {
        return $this->belongsTo(ScenarioTemplate::class, 'template_id');
    }

    public function skillDemands(): HasMany
    {
        return $this->hasMany(ScenarioSkillDemand::class, 'scenario_id');
    }

    public function closureStrategies(): HasMany
    {
        return $this->hasMany(ScenarioClosureStrategy::class, 'scenario_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ScenarioMilestone::class, 'scenario_id');
    }

    // Scopes
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('scenario_type', $type);
    }

    // Métodos auxiliares de negocio
    public function getTotalEstimatedCost()
    {
        return $this->closureStrategies()
            ->whereNotNull('estimated_cost')
            ->sum('estimated_cost');
    }

    public function getAverageCompletionTime()
    {
        return $this->closureStrategies()
            ->whereNotNull('estimated_time_weeks')
            ->avg('estimated_time_weeks');
    }

    public function getCompletionPercentage()
    {
        $total = $this->milestones()->count();
        if ($total === 0) return 0;
        
        $completed = $this->milestones()->where('status', 'completed')->count();
        return round(($completed / $total) * 100);
    }

    public function getCriticalSkillsCount()
    {
        return $this->skillDemands()->where('priority', 'critical')->count();
    }
}
