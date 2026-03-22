<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntelligenceMetricAggregate extends Model
{
    /** @use HasFactory<\Database\Factories\IntelligenceMetricAggregateFactory> */
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'metric_type',
        'source_type',
        'date_key',
        'total_count',
        'success_count',
        'success_rate',
        'avg_duration_ms',
        'p50_duration_ms',
        'p95_duration_ms',
        'p99_duration_ms',
        'avg_confidence',
        'avg_context_count',
        'metadata',
    ];

    protected $casts = [
        'date_key' => 'date',
        'total_count' => 'integer',
        'success_count' => 'integer',
        'success_rate' => 'float',
        'avg_duration_ms' => 'integer',
        'p50_duration_ms' => 'integer',
        'p95_duration_ms' => 'integer',
        'p99_duration_ms' => 'integer',
        'avg_confidence' => 'float',
        'avg_context_count' => 'integer',
        'metadata' => 'array',
    ];
}
