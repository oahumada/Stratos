<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformanceCycle extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'period',
        'status',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'status' => 'string',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class, 'cycle_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
