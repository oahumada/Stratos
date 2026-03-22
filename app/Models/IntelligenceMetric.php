<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntelligenceMetric extends Model
{
    /** @use HasFactory<\Database\Factories\IntelligenceMetricFactory> */
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'metric_type',
        'source_type',
        'context_count',
        'confidence',
        'duration_ms',
        'success',
        'metadata',
    ];

    protected $casts = [
        'context_count' => 'integer',
        'confidence' => 'float',
        'duration_ms' => 'integer',
        'success' => 'boolean',
        'metadata' => 'array',
    ];
}
