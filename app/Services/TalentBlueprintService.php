<?php

// app/Services/TalentBlueprintService.php
namespace App\Services;

use App\Models\Scenario;
use App\Models\TalentBlueprint;

class TalentBlueprintService
{
    public function createFromLlmResponse(Scenario $scenario, array $suggestedRoles)
    {
        foreach ($suggestedRoles as $role) {
            TalentBlueprint::create([
                'scenario_id' => $scenario->id,
                'role_name' => $role['name'],
                'total_fte_required' => $role['estimated_fte'] ?? null,
                'human_leverage' => $role['talent_composition']['human_percentage'] ?? null,
                'synthetic_leverage' => $role['talent_composition']['synthetic_percentage'] ?? null,
                'recommended_strategy' => $role['talent_composition']['strategy_suggestion'] ?? null,
                'agent_specs' => [
                    'description' => $role['description'] ?? null,
                    'suggested_agent_type' => $role['suggested_agent_type'] ?? null,
                    'key_competencies' => $role['key_competencies'] ?? [],
                    'logic_justification' => $role['talent_composition']['logic_justification'] ?? null,
                ],
            ]);
        }
    }
}