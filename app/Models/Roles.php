<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

use App\Traits\BelongsToOrganization;
use App\Traits\HasDomainEvents;

class Roles extends Model
{
    use HasFactory, BelongsToOrganization, HasDomainEvents;

    protected $table = 'roles';

    protected $fillable = [
        'organization_id',
        'parent_id',
        'llm_id',
        'name',
        'level',
        'description',
        'status',
        'discovered_in_scenario_id',
        'embedding',
        'ai_archetype_config',
        'agent_id',
        'blueprint_id',
        'cube_dimensions',
        'base_salary',
    ];

    protected $casts = [
        'level' => 'string',
        'ai_archetype_config' => 'array',
        'embedding' => 'array',
        'cube_dimensions' => 'array',
    ];


    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(TalentBlueprint::class, 'blueprint_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'role_skills', 'role_id', 'skill_id')
            ->withPivot('required_level', 'is_critical')
            ->withTimestamps();
    }

    public function people(): HasMany
    {
        return $this->hasMany(People::class, 'role_id');
    }

    public function roleSkills(): HasMany
    {
        return $this->hasMany(RoleSkill::class, 'role_id');
    }

    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'role_competencies', 'role_id', 'competency_id')
            ->withPivot($this->roleCompetencyPivotColumns())
            ->withTimestamps();
    }

    private function roleCompetencyPivotColumns(): array
    {
        $cols = ['required_level', 'criticity', 'change_type', 'strategy', 'notes', 'is_core', 'rationale'];

        return array_values(array_filter($cols, function ($c) {
            return \Illuminate\Support\Facades\Schema::hasColumn('role_competencies', $c);
        }));
    }

    public function roleCompetencies(): HasMany
    {
        return $this->hasMany(RoleCompetency::class, 'role_id');
    }

    public function jobOpenings(): HasMany
    {
        return $this->hasMany(JobOpening::class);
    }

    public function developmentPaths(): HasMany
    {
        return $this->hasMany(DevelopmentPath::class, 'target_role_id');
    }

    public function peopleRoleSkills(): HasMany
    {
        return $this->hasMany(PeopleRoleSkills::class, 'role_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(\App\Models\RoleVersion::class, 'role_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Roles::class, 'parent_id');
    }
}
