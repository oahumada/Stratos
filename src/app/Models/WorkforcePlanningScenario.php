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
        'name',
        'description',
        'horizon_months',
        'status',
        'fiscal_year',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'horizon_months' => 'integer',
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
}
