<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departments extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->where('departments.organization_id', auth()->user()->organization_id);
            }
        });
    }

    protected $fillable = [
        'organization_id',
        'parent_id',
        'manager_id',
        'name',
        'description',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function People(): HasMany
    {
        return $this->hasMany(People::class, 'department_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Departments::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Departments::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(People::class, 'manager_id');
    }
}
