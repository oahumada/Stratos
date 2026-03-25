<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departments extends Model
{
    use BelongsToOrganization, HasFactory;

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

    protected $appends = ['headcount', 'payroll_total'];

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

    public function getHeadcountAttribute(): int
    {
        return $this->People()->count();
    }

    public function getPayrollTotalAttribute(): float
    {
        return (float) $this->People()->sum('salary');
    }
}
