<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scenario extends Model
{
    use HasFactory;

    protected $table = 'scenarios';

    protected $fillable = ['organization_id', 'name', 'code', 'description', 'kpis', 'start_date', 'end_date', 'horizon_months', 'fiscal_year', 'scope_type', 'scope_notes', 'status', 'approved_at', 'approved_by', 'assumptions', 'owner_user_id', 'sponsor_user_id', 'created_by', 'updated_by'];

    protected $casts = [
        'kpis' => 'array',
        'assumptions' => 'array',
        'custom_config' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function capabilities()
    {
        return $this->hasMany(ScenarioCapability::class, 'scenario_id');
    }

    public function skills()
    {
        return $this->hasMany(ScenarioSkill::class, 'scenario_id');
    }

    public function roles()
    {
        return $this->hasMany(\App\Models\ScenarioRole::class, 'scenario_id');
    }
    
    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_user_id');
    }

    public function statusEvents()
    {
        return $this->hasMany(\App\Models\ScenarioStatusEvent::class, 'scenario_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
