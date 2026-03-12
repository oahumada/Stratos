<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDigitalSeal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\BelongsToOrganization;

class JobOpening extends Model
{
    use HasFactory, HasDigitalSeal, BelongsToOrganization;
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
        'meta_data',
        'digital_signature',
        'signed_at',
        'signature_version',
    ];

    protected $casts = [
        'status' => 'string',
        'is_external' => 'boolean',
        'deadline' => 'date',
        'meta_data' => 'array',
        'signed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($opening) {
            if (! $opening->slug) {
                $opening->slug = \Illuminate\Support\Str::slug($opening->title.'-'.\Illuminate\Support\Str::random(5));
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
