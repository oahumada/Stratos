<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsXapiStatement extends Model
{
    use HasUuids;

    protected $fillable = [
        'organization_id',
        'actor_id',
        'actor_name',
        'actor_email',
        'verb_id',
        'verb_display',
        'object_type',
        'object_id',
        'object_name',
        'result_score_raw',
        'result_score_min',
        'result_score_max',
        'result_score_scaled',
        'result_success',
        'result_completion',
        'result_duration',
        'context_data',
        'statement_timestamp',
        'stored_at',
    ];

    protected function casts(): array
    {
        return [
            'result_score_raw' => 'float',
            'result_score_min' => 'float',
            'result_score_max' => 'float',
            'result_score_scaled' => 'float',
            'result_success' => 'boolean',
            'result_completion' => 'boolean',
            'context_data' => 'array',
            'statement_timestamp' => 'datetime',
            'stored_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeByActor($query, int $userId)
    {
        return $query->where('actor_id', $userId);
    }

    public function scopeByVerb($query, string $verbId)
    {
        return $query->where('verb_id', $verbId);
    }

    public function scopeByObject($query, string $objectId)
    {
        return $query->where('object_id', $objectId);
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }
}
