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
        'model_version',
        'redacted',
    ];

    protected $casts = [
        'llm_response' => 'array',
        'metadata' => 'array',
        'generated_at' => 'datetime',
        'confidence_score' => 'float',
        'redacted' => 'boolean',
    ];
}
