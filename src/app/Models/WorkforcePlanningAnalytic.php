<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanningAnalytic extends Model
{
    protected $table = 'workforce_planning_analytics';

    protected $fillable = [
        'scenario_id',
        'total_headcount_current',
        'total_headcount_projected',
        'net_growth',
        'internal_coverage_percentage',
        'external_gap_percentage',
        'total_skills_required',
        'skills_with_gaps',
        'critical_skills_at_risk',
        'critical_roles',
        'critical_roles_with_successor',
        'succession_risk_percentage',
        'estimated_recruitment_cost',
        'estimated_training_cost',
        'estimated_external_hiring_months',
        'high_risk_positions',
        'medium_risk_positions',
        'calculated_at',
    ];

    protected $casts = [
        'total_headcount_current' => 'integer',
        'total_headcount_projected' => 'integer',
        'net_growth' => 'integer',
        'internal_coverage_percentage' => 'decimal:2',
        'external_gap_percentage' => 'decimal:2',
        'total_skills_required' => 'integer',
        'skills_with_gaps' => 'integer',
        'critical_skills_at_risk' => 'integer',
        'critical_roles' => 'integer',
        'critical_roles_with_successor' => 'integer',
        'succession_risk_percentage' => 'decimal:2',
        'estimated_recruitment_cost' => 'decimal:2',
        'estimated_training_cost' => 'decimal:2',
        'estimated_external_hiring_months' => 'decimal:1',
        'high_risk_positions' => 'integer',
        'medium_risk_positions' => 'integer',
        'calculated_at' => 'datetime',
    ];

    // Relationships
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlanningScenario::class);
    }
}
