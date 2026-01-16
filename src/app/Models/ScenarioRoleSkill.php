<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScenarioRoleSkill extends Model
{
    use HasFactory;

    protected $table = 'scenario_role_skills';

    protected $fillable = ['scenario_id', 'role_id', 'skill_id', 'required_level', 'change_type', 'is_critical'];

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
        return $this->belongsTo(Skills::class, 'skill_id');
    }
}
