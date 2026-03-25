<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * VerificationAudit - Metrics and audit snapshots for the verification system.
 */
class VerificationAudit extends Model
{
    protected $fillable = [
        'organization_id',
        'audit_type',
        'current_phase',
        'status',
        'confidence_score',
        'error_rate',
        'retry_rate',
        'sample_size',
        'throughput',
        'latency',
        'data',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
        'confidence_score' => 'float',
        'error_rate' => 'float',
        'retry_rate' => 'float',
        'throughput' => 'float',
        'latency' => 'float',
        'sample_size' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
