<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScenarioGeneration extends Model
{
    use HasFactory;

    protected $table = 'scenario_generations';

    protected $fillable = [
        'organization_id',
        'created_by',
        'prompt',
        'llm_response',
        'generated_at',
        'confidence_score',
        'status',
        'metadata',
        'compacted',
        'chunk_count',
        'compacted_at',
        'compacted_by',
        'model_version',
        'redacted',
    ];

    protected $casts = [
        'llm_response' => 'array',
        'metadata' => 'array',
        'generated_at' => 'datetime',
        'confidence_score' => 'float',
        'redacted' => 'boolean',
        'compacted_at' => 'datetime',
        'chunk_count' => 'integer',
        'compacted_by' => 'integer',
    ];
}
