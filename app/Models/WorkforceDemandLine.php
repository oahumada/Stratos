<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforceDemandLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'scenario_id',
        'organization_id',
        'unit',
        'role_name',
        'period',
        'volume_expected',
        'time_standard_minutes',
        'productivity_factor',
        'coverage_target_pct',
        'attrition_pct',
        'ramp_factor',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'volume_expected' => 'integer',
            'time_standard_minutes' => 'integer',
            'productivity_factor' => 'float',
            'coverage_target_pct' => 'float',
            'attrition_pct' => 'float',
            'ramp_factor' => 'float',
        ];
    }

    /** HH requeridas brutas = volumen × tiempo estándar / 60. */
    public function getRequiredHhAttribute(): float
    {
        return round(($this->volume_expected * $this->time_standard_minutes) / 60, 2);
    }

    /** HH efectivas ajustadas por productividad, cobertura y rampa. */
    public function getEffectiveHhAttribute(): float
    {
        $raw = $this->required_hh;
        $cov = $this->coverage_target_pct / 100;
        $prod = $this->productivity_factor;
        $ramp = $this->ramp_factor;

        return round($raw * $cov / $prod / $ramp, 2);
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }
}
