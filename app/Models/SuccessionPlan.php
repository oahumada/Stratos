<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToOrganization;

class SuccessionPlan extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'person_id',
        'target_role_id',
        'readiness_score',
        'readiness_level',
        'estimated_months',
        'status',
        'metrics',
        'gaps',
        'created_by',
    ];

    protected $casts = [
        'metrics' => 'array',
        'gaps' => 'array',
        'readiness_score' => 'float',
        'estimated_months' => 'integer',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'person_id');
    }

    public function targetRole(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'target_role_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
