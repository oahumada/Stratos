<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class TalentPass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ulid',
        'organization_id',
        'people_id',
        'title',
        'summary',
        'status',
        'visibility',
        'is_featured',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'view_count' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(TalentPassSkill::class);
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(TalentPassCredential::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(TalentPassExperience::class);
    }

    // Scopes
    public function scopeByOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    // Methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
    public function canBeEdited(): bool
    {
        return $this->isDraft();
    }
    public function publish()
    {
        return $this->update(['status' => 'published']);
    }

    public function archive()
    {
        return $this->update(['status' => 'archived']);
    }
}
