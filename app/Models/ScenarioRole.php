<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScenarioRole extends Model
{
    use HasFactory;

    // Some migrations use 'scenario_roles' and some 'scenario_role'
    protected $table = 'scenario_roles';

    protected $fillable = [
        'scenario_id',
        'role_id',
        'fte',
        'role_change',
        'impact_level',
        'evolution_type',
        'rationale',
        'strategic_role',
        'embedding',
        'human_leverage',
        'archetype',
        'ai_suggestions'
    ];

    protected $casts = [
        'ai_suggestions' => 'array',
        'embedding' => 'array',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }
}
