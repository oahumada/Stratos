<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Roles extends Model
{
    protected $table = 'roles';

    protected $fillable = ['organization_id', 'name', 'level', 'description', 'embedding'];

    protected $casts = [
        'level' => 'string',
    ];

    protected static function booted()
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $user = Auth::user();
            if ($user && $user->organization_id) {
                $builder->where('roles.organization_id', $user->organization_id);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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
}
