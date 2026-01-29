<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Capability;

class Scenario extends Model
{
    use HasFactory;

    // Canonical table: `scenarios`. Note: DB view `workforce_planning_scenarios` provides backwards compatibility.
    protected $table = 'scenarios';

    protected $fillable = ['organization_id', 'name', 'code', 'description', 'kpis', 'start_date', 'end_date', 'horizon_months', 'fiscal_year', 'scope_type', 'scope_notes', 'status', 'approved_at', 'approved_by', 'assumptions', 'owner_user_id', 'sponsor_user_id', 'created_by', 'updated_by', 'template_id'];

    protected $casts = [
        'kpis' => 'array',
        'assumptions' => 'array',
        'custom_config' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function capabilities()
    {
        return $this->belongsToMany(Capability::class, 'scenario_capabilities', 'scenario_id', 'capability_id')
            ->withPivot(['required_level', 'is_critical', 'strategic_role', 'strategic_weight', 'priority', 'rationale'])
            ->withTimestamps();
    }

    // Backwards-compatible alias used by some controllers
    public function scenarioCapabilities()
    {
        return $this->hasMany(ScenarioCapability::class, 'scenario_id');
    }

    public function capabilityCompetencies()
    {
        return $this->hasMany(\App\Models\CapabilityCompetency::class, 'scenario_id');
    }

    public function skills()
    {
        return $this->hasMany(ScenarioSkill::class, 'scenario_id');
    }

    public function skillDemands()
    {
        return $this->hasMany(\App\Models\ScenarioSkillDemand::class, 'scenario_id');
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

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = 'SCN-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)) . '-' . time();
            }
            // Ensure start/end dates exist to satisfy NOT NULL DB constraints
            if (empty($model->start_date)) {
                $model->start_date = now()->toDateString();
            }
            if (empty($model->end_date)) {
                $months = $model->horizon_months ?? 6;
                $model->end_date = now()->addMonths($months)->toDateString();
            }
            if (empty($model->scope_type)) {
                $model->scope_type = 'organization_wide';
            }
            if (empty($model->owner_user_id)) {
                $model->owner_user_id = $model->created_by ?? 1;
            }
        });
    }

}
