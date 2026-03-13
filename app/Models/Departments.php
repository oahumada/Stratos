<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToOrganization;

class Departments extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'parent_id',
        'manager_id',
        'name',
        'description',
        'aliases',
    ];

    protected $casts = [
        'aliases' => 'array',
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
