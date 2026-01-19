<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkforcePlan extends Model
{
    use HasFactory;

    protected $table = 'workforce_plans';

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'description',
        'start_date',
        'end_date',
        'planning_horizon_months',
        'scope_type',
        'scope_notes',
        'strategic_context',
        'budget_constraints',
        'legal_constraints',
        'labor_relations_constraints',
        'status',
        'owner_user_id',
        'sponsor_user_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function scenarios()
    {
        return $this->hasMany(Scenario::class, 'workforce_plan_id');
    }
}
