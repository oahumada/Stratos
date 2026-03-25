<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\HasDigitalSeal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Competency extends Model
{
    use BelongsToOrganization, HasDigitalSeal, HasFactory;

    protected $table = 'competencies';

    protected $fillable = ['organization_id', 'llm_id', 'name', 'description', 'status', 'discovered_in_scenario_id', 'embedding', 'agent_id', 'cube_dimensions', 'digital_signature', 'signed_at', 'signature_version'];

    protected $casts = [
        'llm_id' => 'string',
        'cube_dimensions' => 'array',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

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
            ->withPivot($this->competencySkillPivotColumns())
            ->withTimestamps();
    }

    private function competencySkillPivotColumns(): array
    {
        $cols = ['weight', 'priority', 'required_level', 'rationale', 'is_required'];

        return array_values(array_filter($cols, function ($c) {
            return \Illuminate\Support\Facades\Schema::hasColumn('competency_skills', $c);
        }));
    }

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'role_competencies', 'competency_id', 'role_id')
            ->withPivot($this->roleCompetencyPivotColumns())
            ->withTimestamps();
    }

    private function roleCompetencyPivotColumns(): array
    {
        $cols = ['required_level', 'criticity', 'change_type', 'strategy', 'notes', 'is_core', 'rationale'];

        return array_values(array_filter($cols, function ($c) {
            return Schema::hasColumn('role_competencies', $c);
        }));
    }

    public function roleCompetencies()
    {
        return $this->hasMany(RoleCompetency::class, 'competency_id');
    }

    public function competencySkills()
    {
        return $this->hasMany(CompetencySkill::class, 'competency_id');
    }

    public function capabilityCompetencies()
    {
        return $this->hasMany(\App\Models\CapabilityCompetency::class, 'competency_id');
    }

    public function versions()
    {
        return $this->hasMany(\App\Models\CompetencyVersion::class, 'competency_id');
    }
}
