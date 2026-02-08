<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScenarioSkill extends Model
{
    use HasFactory;

    protected $table = 'scenario_skills';

    protected $fillable = ['scenario_id', 'skill_id', 'strategic_role', 'priority', 'rationale'];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
