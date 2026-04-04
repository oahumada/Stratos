<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsMarketplaceListing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'course_id',
        'title',
        'description',
        'price',
        'currency',
        'listing_type',
        'is_published',
        'downloads_count',
        'category',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_published' => 'boolean',
            'downloads_count' => 'integer',
            'tags' => 'array',
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

    public function purchases(): HasMany
    {
        return $this->hasMany(LmsMarketplacePurchase::class, 'listing_id');
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
