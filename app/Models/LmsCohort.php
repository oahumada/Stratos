<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsCohort extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'course_id',
        'starts_at',
        'ends_at',
        'max_members',
        'is_active',
        'facilitator_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'max_members' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function facilitator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'facilitator_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(LmsCohortMember::class, 'cohort_id');
    }
}
