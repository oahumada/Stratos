<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanningSkillGap extends Model
{
    protected $table = 'workforce_planning_skill_gaps';

    protected $fillable = [
        'scenario_id',
        'department_id',
        'role_id',
        'skill_id',
        'current_proficiency',
        'required_proficiency',
        'gap',
        'people_with_skill',
        'coverage_percentage',
        'priority',
        'remediation_strategy',
        'estimated_cost',
        'timeline_months',
    ];

    protected $casts = [
        'current_proficiency' => 'decimal:1',
        'required_proficiency' => 'decimal:1',
        'gap' => 'decimal:1',
        'people_with_skill' => 'integer',
        'coverage_percentage' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'timeline_months' => 'integer',
    ];

    // Relationships
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlanningScenario::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skills::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Departments::class);
    }

    // Scopes
    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['critical', 'high']);
    }

    public function scopeByStrategy($query, $strategy)
    {
        return $query->where('remediation_strategy', $strategy);
    }
}
