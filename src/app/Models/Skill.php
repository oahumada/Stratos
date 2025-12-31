<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    protected $fillable = ['organization_id', 'name', 'category', 'description', 'is_critical'];

    protected $casts = [
        'category' => 'string',
        'is_critical' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->where('skills.organization_id', auth()->user()->organization_id);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Roles::class, 'role_skills')
            ->withPivot('required_level', 'is_critical')
            ->withTimestamps();
    }

    public function People(): BelongsToMany
    {
        return $this->belongsToMany(People::class, 'people_skills')
            ->withPivot('level', 'last_evaluated_at', 'evaluated_by')
            ->withTimestamps();
    }
}
