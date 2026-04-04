<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'cycle_id',
        'people_id',
        'reviewer_id',
        'self_score',
        'manager_score',
        'peer_score',
        'final_score',
        'calibration_score',
        'strengths',
        'development_areas',
        'status',
    ];

    protected $casts = [
        'self_score' => 'decimal:2',
        'manager_score' => 'decimal:2',
        'peer_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'calibration_score' => 'decimal:2',
        'strengths' => 'array',
        'development_areas' => 'array',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(PerformanceCycle::class, 'cycle_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }
}
