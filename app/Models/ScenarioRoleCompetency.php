<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioRoleCompetency extends Model
{
    use HasFactory;
    protected $fillable = [
        'scenario_id',
        'role_id',
        'competency_id',
        'required_level',
        'is_core',
        'is_referent',
        'change_type',
        'rationale',
        'competency_version_id',
        'suggested_strategy',
        'strategy_rationale',
        'ia_confidence_score',
        'ia_action_plan',
    ];

    protected $casts = [
        'is_core' => 'boolean',
        'is_referent' => 'boolean',
        'ia_action_plan' => 'array',
        'ia_confidence_score' => 'float',
    ];

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class);
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }
}
