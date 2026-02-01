<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class Competency extends Model
{
    use HasFactory;

    protected $table = 'competencies';

    protected $fillable = ['organization_id', 'name', 'description'];

    /**
     * Relación N:N con Capabilities vía tabla pivote capability_competencies
     */
    public function capabilities()
    {
        return $this->belongsToMany(
            Capability::class,
            'capability_competencies',
            'competency_id',
            'capability_id'
        )
            ->withPivot($this->capabilityCompetencyPivotColumns())
            ->withTimestamps();
    }

    private function capabilityCompetencyPivotColumns(): array
    {
        $cols = ['scenario_id', 'required_level', 'weight', 'strategic_weight', 'priority', 'rationale', 'is_required', 'created_at', 'updated_at'];
        return array_values(array_filter($cols, function ($c) {
            return Schema::hasColumn('capability_competencies', $c);
        }));
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'competency_skills', 'competency_id', 'skill_id')
            ->withPivot('weight', 'priority', 'required_level', 'rationale', 'is_required')
            ->withTimestamps();
    }

    public function competencySkills()
    {
        return $this->hasMany(CompetencySkill::class, 'competency_id');
    }

    public function capabilityCompetencies()
    {
        return $this->hasMany(\App\Models\CapabilityCompetency::class, 'competency_id');
    }
}

