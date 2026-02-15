<?php

// app/Services/TalentBlueprintService.php
namespace App\Services;

use App\Models\Scenario;
use App\Models\TalentBlueprint;

class TalentBlueprintService
{
    protected $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }

    public function createFromLlmResponse(Scenario $scenario, array $suggestedRoles)
    {
        foreach ($suggestedRoles as $role) {
            $blueprint = TalentBlueprint::create([
                'scenario_id' => $scenario->id,
                'role_name' => $role['name'] ?? $role['role_name'],
                'role_description' => $role['description'] ?? ($role['role_description'] ?? null),
                'total_fte_required' => $role['estimated_fte'] ?? null,
                'human_leverage' => $role['talent_composition']['human_percentage'] ?? ($role['human_leverage'] ?? null),
                'synthetic_leverage' => $role['talent_composition']['synthetic_percentage'] ?? ($role['synthetic_leverage'] ?? null),
                'recommended_strategy' => $role['talent_composition']['strategy_suggestion'] ?? ($role['recommended_strategy'] ?? null),
                'status' => 'in_incubation',
                'key_competencies' => $role['key_competencies'] ?? [],
                'agent_specs' => [
                    'description' => $role['description'] ?? ($role['role_description'] ?? null),
                    'suggested_agent_type' => $role['suggested_agent_type'] ?? null,
                    'logic_justification' => $role['talent_composition']['logic_justification'] ?? null,
                ],
            ]);

            // Generate embedding for similarity check later
            if (config('features.generate_embeddings', false)) {
                try {
                    // Create a text representation for the role
                    $text = ($role['name'] ?? $role['role_name']) . " | " . ($role['description'] ?? ($role['role_description'] ?? ''));
                    $embedding = $this->embeddingService->generate($text);
                    
                    if ($embedding) {
                        $vectorStr = $this->embeddingService->toVectorString($embedding);
                        \DB::update(
                            "UPDATE talent_blueprints SET embedding = ?::vector WHERE id = ?",
                            [$vectorStr, $blueprint->id]
                        );
                    }
                } catch (\Exception $e) {
                    \Log::warning("Embedding generation failed for blueprint {$blueprint->id}: " . $e->getMessage());
                }
            }
        }
    }
}
