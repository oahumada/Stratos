<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScenarioRoleSkill extends Model
{
    use HasFactory;

    protected $table = 'scenario_role_skills';

    protected $fillable = ['scenario_id', 'role_id', 'skill_id', 'required_level', 'change_type', 'is_critical', 'source', 'competency_id', 'competency_version_id', 'metadata', 'created_by'];

    protected $casts = [
        'metadata' => 'array',
        'is_critical' => 'boolean',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
