<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;


class Departments extends Model
{
    
    protected $fillable = [
        'organization_id',
        'name',
        'description',
    ];
    
    public function People(): HasMany
    {
        return $this->hasMany(People::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->where('departments.organization_id', auth()->user()->organization_id);
            }
        });
    }
}
