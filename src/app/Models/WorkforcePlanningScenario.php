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
        'workforce_plan_id',
        'organization_id',
        'parent_id',
        'template_id',
        'name',
        'description',
        'scenario_type',
        'scope_type',
        'scope_id',
        'target_date',
        'time_horizon_weeks',
        'horizon_months',
        'status',
        'decision_status',
        'execution_status',
        'current_step',
        'assumptions',
        'custom_config',
        'estimated_budget',
        'fiscal_year',
        'owner',
        'owner_id',
        'created_by',
        'approved_by',
        'approved_at',
        'last_simulated_at',
        'version_group_id',
        'version_number',
        'is_current_version',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'horizon_months' => 'integer',
        'time_horizon_weeks' => 'integer',
        'current_step' => 'integer',
        'version_number' => 'integer',
        'is_current_version' => 'boolean',
        'target_date' => 'date',
        'assumptions' => 'array',
        'custom_config' => 'array',
        'estimated_budget' => 'decimal:2',
        'approved_at' => 'datetime',
        'last_simulated_at' => 'datetime',
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

    public function workforcePlan(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlan::class, 'workforce_plan_id');
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

    // Nuevas relaciones para jerarquía y auditoría
    public function parent(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlanningScenario::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(WorkforcePlanningScenario::class, 'parent_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function statusEvents(): HasMany
    {
        return $this->hasMany(ScenarioStatusEvent::class, 'scenario_id');
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

    public function scopeCurrentVersion($query)
    {
        return $query->where('is_current_version', true);
    }

    public function scopeByVersionGroup($query, $versionGroupId)
    {
        return $query->where('version_group_id', $versionGroupId);
    }

    public function scopeByScope($query, $scopeType, $scopeId = null)
    {
        $query->where('scope_type', $scopeType);
        if ($scopeId !== null) {
            $query->where('scope_id', $scopeId);
        }
        return $query;
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeByDecisionStatus($query, $status)
    {
        return $query->where('decision_status', $status);
    }

    public function scopeByExecutionStatus($query, $status)
    {
        return $query->where('execution_status', $status);
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

    // Accessors para estados
    public function getIsApprovedAttribute(): bool
    {
        return $this->decision_status === 'approved';
    }

    public function getCanEditAttribute(): bool
    {
        return !$this->is_approved;
    }

    public function getIsParentAttribute(): bool
    {
        return $this->parent_id === null;
    }

    public function getIsChildAttribute(): bool
    {
        return $this->parent_id !== null;
    }

    public function getIsExecutingAttribute(): bool
    {
        return in_array($this->execution_status, ['in_progress', 'paused']);
    }

    // Helper para verificar transiciones permitidas
    public function canTransitionTo(string $newDecisionStatus): bool
    {
        $validTransitions = [
            'draft' => ['simulated', 'archived'],
            'simulated' => ['proposed', 'draft', 'archived'],
            'proposed' => ['approved', 'simulated', 'rejected', 'archived'],
            'approved' => ['archived'],
            'rejected' => ['archived'],
            'archived' => [],
        ];

        return in_array($newDecisionStatus, $validTransitions[$this->decision_status] ?? []);
    }
}
