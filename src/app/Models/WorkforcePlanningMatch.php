<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanningMatch extends Model
{
    protected $table = 'workforce_planning_matches';

    protected $fillable = [
        'scenario_id',
        'forecast_id',
        'person_id',
        'match_score',
        'skill_match',
        'readiness_level',
        'gaps',
        'transition_type',
        'transition_months',
        'development_path_id',
        'risk_score',
        'risk_factors',
        'recommendation',
    ];

    protected $casts = [
        'match_score' => 'decimal:2',
        'skill_match' => 'decimal:2',
        'risk_score' => 'decimal:2',
        'transition_months' => 'integer',
        'gaps' => 'array',
        'risk_factors' => 'array',
    ];

    // Relationships
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlanningScenario::class);
    }

    public function forecast(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlanningRoleForecast::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class);
    }

    public function developmentPath(): BelongsTo
    {
        return $this->belongsTo(DevelopmentPath::class);
    }

    // Scopes
    public function scopeByReadiness($query, $readiness)
    {
        return $query->where('readiness_level', $readiness);
    }

    public function scopeHighScore($query)
    {
        return $query->where('match_score', '>=', 75)->orderByDesc('match_score');
    }

    public function scopeByRisk($query, $maxRisk = 50)
    {
        return $query->where('risk_score', '<=', $maxRisk);
    }
}
