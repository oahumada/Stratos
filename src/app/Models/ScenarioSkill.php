<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return $this->belongsTo(Skills::class, 'skill_id');
    }
}
