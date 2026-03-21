<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToOrganization;
use App\Traits\HasDigitalSeal;

class Skill extends Model
{
    use HasFactory, BelongsToOrganization, HasDigitalSeal;

    protected $fillable = [
        'organization_id',
        'parent_id',
        'llm_id',
        'name',
        'category',
        'complexity_level',
        'description',
        'is_critical',
        'scope_type',
        'domain_tag',
        'status',
        'discovered_in_scenario_id',
        'embedding',
        'digital_signature',
        'signed_at',
        'signature_version',
        'cube_dimensions',
    ];

    // Defaults applied when creating via Eloquent to match DB defaults
    protected $attributes = [
        'complexity_level' => 'tactical',
        'lifecycle_status' => 'active',
        'is_critical' => false,
        'scope_type' => 'domain',
    ];

    protected $casts = [
        'category' => 'string',
        'is_critical' => 'boolean',
        'llm_id' => 'string',
        'cube_dimensions' => 'array',
    ];


    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Roles::class, 'role_skills', 'skill_id', 'role_id')
            ->withPivot('required_level', 'is_critical')
            ->withTimestamps();
    }

    public function People(): BelongsToMany
    {
        return $this->belongsToMany(People::class, 'people_role_skills', 'skill_id', 'people_id')
            ->wherePivot('is_active', true)
            ->withPivot(
                'role_id',
                'current_level',
                'required_level',
                'is_active',
                'evaluated_at',
                'expires_at',
                'evaluated_by',
                'notes'
            )
            ->withTimestamps();
    }

    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'competency_skills', 'skill_id', 'competency_id')
            ->withPivot('weight')
            ->withTimestamps();
    }

    public function roleSkills(): HasMany
    {
        return $this->hasMany(RoleSkill::class, 'skill_id');
    }

    public function peopleRoleSkills(): HasMany
    {
        return $this->hasMany(PeopleRoleSkills::class, 'skill_id');
    }

    public function barsLevels(): HasMany
    {
        return $this->hasMany(BarsLevel::class)->orderBy('level');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SkillQuestionBank::class);
    }

    /**
     * Get the count of roles that require this skill
     */
    public function getRolesCountAttribute(): int
    {
        return $this->roles()->count();
    }

    /**
     * Get the count of employees that require this skill
     */
    public function getEmployeesCountAttribute(): int
    {
        return $this->peopleRoleSkills()
            ->where('is_active', true)
            ->distinct('people_id')
            ->count();
    }

    // Scopes para skills por alcance
    public function scopeTransversal($query)
    {
        return $query->where('scope_type', 'transversal');
    }

    public function scopeDomain($query)
    {
        return $query->where('scope_type', 'domain');
    }

    public function scopeSpecific($query)
    {
        return $query->where('scope_type', 'specific');
    }

    public function scopeByDomain($query, $domainTag)
    {
        return $query->where('domain_tag', $domainTag);
    }

    // Helper methods
    public function isTransversal(): bool
    {
        return $this->scope_type === 'transversal';
    }

    public function isDomain(): bool
    {
        return $this->scope_type === 'domain';
    }

    public function isSpecific(): bool
    {
        return $this->scope_type === 'specific';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Skill::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Skill::class, 'parent_id');
    }
}
