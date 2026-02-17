<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

// app/Models/TalentBlueprint.php
// Une el escenario con la estrategia de ingeniería
class TalentBlueprint extends Model
{
    use HasFactory;
    // Support both legacy service attribute names and the actual DB columns.
    protected $fillable = [
        'scenario_id', 'role_name', 'role_description', 'estimated_fte',
        'total_fte_required', 'human_percentage', 'synthetic_percentage',
        'human_leverage', 'synthetic_leverage', 'strategy_suggestion', 'recommended_strategy',
        'logic_justification', 'suggested_agent_type', 'agent_specs', 'key_competencies', 'status', 'embedding'
    ];

    protected $casts = [
        'estimated_fte' => 'decimal:2',
        'total_fte_required' => 'decimal:2',
        'key_competencies' => 'array',
        'agent_specs' => 'array',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }

    // Accessors / Mutators to keep compatibility with service layer expectations
    public function getHumanPercentageAttribute()
    {
        return $this->human_leverage ?? $this->attributes['human_leverage'] ?? null;
    }

    public function setHumanPercentageAttribute($value)
    {
        $this->attributes['human_leverage'] = (int) $value;
    }

    public function getSyntheticPercentageAttribute()
    {
        return $this->synthetic_leverage ?? $this->attributes['synthetic_leverage'] ?? null;
    }

    public function setSyntheticPercentageAttribute($value)
    {
        $this->attributes['synthetic_leverage'] = (int) $value;
    }

    public function getEstimatedFteAttribute()
    {
        return $this->total_fte_required ?? $this->attributes['total_fte_required'] ?? null;
    }

    public function setEstimatedFteAttribute($value)
    {
        $this->attributes['total_fte_required'] = $value;
    }


}