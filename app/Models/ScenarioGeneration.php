<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

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
        'raw_prompt',
        'compacted',
        'chunk_count',
        'compacted_at',
        'compacted_by',
        'last_validation_issue_id',
        'model_version',
        'redacted',
        'scenario_id',
        'embedding',
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
        'last_validation_issue_id' => 'integer',
        'scenario_id' => 'integer',
    ];

    /**
     * If a generation was imported/accepted into a Scenario, the
     * `scenarios.source_generation_id` column references this model.
     * This defines the inverse 1:1 relation so code can easily navigate
     * from a generation to its created Scenario (if any).
     */
    public function scenario()
    {
        return $this->hasOne(Scenario::class, 'source_generation_id');
    }

    // Accesor para obtener el "Índice de Sintetización" del escenario
    public function getSynthetizationIndexAttribute() {
        // Calcula qué tan "IA-Ready" es este plan estratégico
        return collect($this->llm_response['suggested_roles'])
                ->avg('talent_composition.synthetic_percentage');
    }

    public function getRawPromptDecryptedAttribute()
    {
        if (empty($this->raw_prompt)) {
            return null;
        }
        try {
            return Crypt::decryptString($this->raw_prompt);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
