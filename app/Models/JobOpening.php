<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    protected $fillable = [
        'organization_id',
        'title',
        'slug',
        'role_id',
        'department',
        'description',
        'requirements',
        'benefits',
        'status',
        'is_external',
        'deadline',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string',
        'is_external' => 'boolean',
        'deadline' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($opening) {
            if (!$opening->slug) {
                $opening->slug = \Illuminate\Support\Str::slug($opening->title . '-' . \Illuminate\Support\Str::random(5));
            }
        });

        static::addGlobalScope('organization', function (Builder $builder) {
            // Only apply organization scope if authenticated
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->where('job_openings.organization_id', auth()->user()->organization_id);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
