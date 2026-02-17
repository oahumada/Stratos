<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScenarioRoleSkill extends Model
{
    use HasFactory;

    protected $table = 'scenario_role_skills';

    protected $fillable = ['scenario_id', 'role_id', 'skill_id', 'current_level', 'required_level', 'change_type', 'is_critical', 'source', 'competency_id', 'competency_version_id', 'metadata', 'created_by'];

    protected $casts = [
        'metadata' => 'array',
        'is_critical' => 'boolean',
        'current_level' => 'integer',
        'required_level' => 'integer',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function scenarioRole()
    {
        return $this->belongsTo(ScenarioRole::class, 'role_id');
    }

    public function role()
    {
        // This remains for backward compatibility if needed, 
        // but it actually points to scenario_roles table in Step 2 logic.
        return $this->belongsTo(ScenarioRole::class, 'role_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
