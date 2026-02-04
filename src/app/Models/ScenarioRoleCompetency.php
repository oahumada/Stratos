<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioRoleCompetency extends Model
{
    protected $fillable = [
        'scenario_id',
        'role_id',
        'competency_id',
        'required_level',
        'is_core',
        'change_type',
        'rationale'
    ];

    protected $casts = [
        'is_core' => 'boolean'
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