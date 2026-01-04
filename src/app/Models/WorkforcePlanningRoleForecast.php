<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkforcePlanningRoleForecast extends Model
{
    protected $table = 'workforce_planning_role_forecasts';

    protected $fillable = [
        'scenario_id',
        'role_id',
        'department_id',
        'headcount_current',
        'headcount_projected',
        'growth_rate',
        'variance_reason',
        'critical_skills',
        'emerging_skills',
        'declining_skills',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'headcount_current' => 'integer',
        'headcount_projected' => 'integer',
        'growth_rate' => 'decimal:2',
        'critical_skills' => 'array',
        'emerging_skills' => 'array',
        'declining_skills' => 'array',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(WorkforcePlanningMatch::class, 'forecast_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
