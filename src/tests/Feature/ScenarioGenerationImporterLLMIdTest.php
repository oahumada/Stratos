<?php

namespace Tests\Feature;

use App\Models\ScenarioGeneration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScenarioGenerationImporterLLMIdTest extends TestCase
{
    use RefreshDatabase;

    public function test_importer_respects_llm_id_and_persists_mappings()
    {
        $org = \App\Models\Organizations::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        $this->actingAs($user);

        $llmResponse = [
            'scenario_metadata' => ['name' => 'LLM Id Scenario'],
            'capabilities' => [
                [
                    'id' => 'cap_llm_1',
                    'name' => 'Cap from LLM',
                    'description' => 'desc',
                    'competencies' => [
                        [
                            'id' => 'comp_llm_1',
                            'name' => 'Comp from LLM',
                            'skills' => [
                                ['id' => 'skill_llm_1', 'name' => 'Skill from LLM']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $generation = ScenarioGeneration::create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'prompt' => 'redacted prompt',
            'llm_response' => $llmResponse,
            'status' => 'complete',
            'redacted' => true,
        ]);

        $res = $this->postJson("/api/strategic-planning/scenarios/generate/{$generation->id}/accept", ['import' => true]);

        $res->assertStatus(201);
        $res->assertJson(['success' => true]);

        $this->assertDatabaseHas('capabilities', ['organization_id' => $org->id, 'llm_id' => 'cap_llm_1', 'name' => 'Cap from LLM']);
        $this->assertDatabaseHas('competencies', ['organization_id' => $org->id, 'llm_id' => 'comp_llm_1', 'name' => 'Comp from LLM']);
        $this->assertDatabaseHas('skills', ['organization_id' => $org->id, 'llm_id' => 'skill_llm_1', 'name' => 'Skill from LLM']);

        $genMeta = \DB::table('scenario_generations')->where('id', $generation->id)->value('metadata');
        $this->assertStringContainsString('llm_mappings', json_encode($genMeta));
    }
}
