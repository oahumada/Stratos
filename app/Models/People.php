<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class People extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'people';

    protected $fillable = [
        'user_id',
        'organization_id',
        'role_id',
        'first_name',
        'last_name',
        'email',
        'department_id',
        'hire_date',
        'photo_url',
    ];

    protected $appends = ['skills_count'];

    protected $casts = [
        'hire_date' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->where('people.organization_id', auth()->user()->organization_id);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Departments::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'people_role_skills', 'people_id', 'skill_id')
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

    public function developmentPaths(): HasMany
    {
        return $this->hasMany(DevelopmentPath::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Relación con skills históricas y activas (nueva tabla people_role_skills)
     */
    public function roleSkills(): HasMany
    {
        return $this->hasMany(PeopleRoleSkills::class, 'people_id');
    }

    /**
     * Solo skills activas del rol actual
     */
    public function activeSkills(): HasMany
    {
        return $this->hasMany(PeopleRoleSkills::class, 'people_id')
            ->where('is_active', true);
    }

    /**
     * Skills expiradas que requieren reevaluación
     */
    public function expiredSkills(): HasMany
    {
        return $this->hasMany(PeopleRoleSkills::class, 'people_id')
            ->where('is_active', true)
            ->where('expires_at', '<', now());
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getSkillsCountAttribute(): int
    {
        return $this->skills->count();
    }
}
