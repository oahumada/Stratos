<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScenarioSkillDemand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'scenario_id',
        'skill_id',
        'role_id',
        'department',
        'required_headcount',
        'required_level',
        'current_headcount',
        'current_avg_level',
        'priority',
        'rationale',
        'target_date',
        'is_mandatory_from_parent',
    ];

    protected $casts = [
        'required_headcount' => 'integer',
        'required_level' => 'decimal:1',
        'current_headcount' => 'integer',
        'current_avg_level' => 'decimal:1',
        'target_date' => 'date',
        'is_mandatory_from_parent' => 'boolean',
    ];

    // Relaciones
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(StrategicPlanningScenarios::class, 'scenario_id');
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class);
    }

    // ── Gap methods ─────────────────────────────────────────────
    // This model bridges TWO gap domains:
    //  • Headcount gap (dotacional): required_headcount - current_headcount
    //  • Proficiency gap (competencias): required_level - current_avg_level
    // See: docs/STRATOS_DOMINIOS_WFP_VS_TALENT.md

    /** Headcount gap (dotacional): personas faltantes para cubrir la demanda. */
    public function getHeadcountGap(): int
    {
        return max(0, $this->required_headcount - $this->current_headcount);
    }

    /** @deprecated Use getHeadcountGap() — renamed for domain clarity. */
    public function getGapHeadcount(): int
    {
        return $this->getHeadcountGap();
    }

    /** Proficiency gap (competencias): niveles faltantes para alcanzar el requerido. */
    public function getProficiencyGap(): float
    {
        return max(0, $this->required_level - ($this->current_avg_level ?? 0));
    }

    /** @deprecated Use getProficiencyGap() — renamed for domain clarity. */
    public function getGapLevel(): float
    {
        return $this->getProficiencyGap();
    }

    /** Headcount coverage percentage (dotacional). */
    public function getCoveragePercentage()
    {
        if ($this->required_headcount === 0) {
            return 100;
        }

        return round(($this->current_headcount / $this->required_headcount) * 100);
    }

    // Scopes
    public function scopeMandatoryFromParent($query)
    {
        return $query->where('is_mandatory_from_parent', true);
    }

    public function scopeEditable($query)
    {
        return $query->where('is_mandatory_from_parent', false);
    }

    // Helpers
    public function isMandatory(): bool
    {
        return $this->is_mandatory_from_parent === true;
    }

    public function canDelete(): bool
    {
        return ! $this->is_mandatory_from_parent;
    }
}
