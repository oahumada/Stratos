<?php

namespace Tests\Feature;

use App\Models\ScenarioGeneration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScenarioGenerationImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_importer_creates_capabilities_competencies_and_skills_on_accept()
    {
        $org = \App\Models\Organizations::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        $this->actingAs($user);

        $llmResponse = [
            'scenario_metadata' => ['name' => 'Importer Scenario', 'description' => 'desc'],
            'capabilities' => [
                [
                    'name' => 'Data & Analytics',
                    'description' => 'Analytics capability',
                    'competencies' => [
                        [
                            'name' => 'Data Engineering',
                            'description' => 'Build pipelines',
                            'skills' => ['SQL', 'ETL'],
                        ],
                    ],
                ],
            ],
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

        // Scenario created
        $this->assertDatabaseHas('scenarios', ['source_generation_id' => $generation->id, 'name' => 'Importer Scenario']);

        $scenarioId = \DB::table('scenarios')->where('source_generation_id', $generation->id)->value('id');

        // Capability created and linked to scenario
        $capId = \DB::table('capabilities')->where('organization_id', $org->id)->where('name', 'Data & Analytics')->value('id');
        $this->assertNotNull($capId);
        $this->assertDatabaseHas('scenario_capabilities', ['scenario_id' => $scenarioId, 'capability_id' => $capId]);

        // Competency created and linked via capability_competencies
        $compId = \DB::table('competencies')->where('organization_id', $org->id)->where('name', 'Data Engineering')->value('id');
        $this->assertNotNull($compId);
        $this->assertDatabaseHas('capability_competencies', ['scenario_id' => $scenarioId, 'capability_id' => $capId, 'competency_id' => $compId]);

        // Skills created and linked via competency_skills
        $sqlId = \DB::table('skills')->where('organization_id', $org->id)->where('name', 'SQL')->value('id');
        $etlId = \DB::table('skills')->where('organization_id', $org->id)->where('name', 'ETL')->value('id');
        $this->assertNotNull($sqlId);
        $this->assertNotNull($etlId);
        $this->assertDatabaseHas('competency_skills', ['competency_id' => $compId, 'skill_id' => $sqlId]);
        $this->assertDatabaseHas('competency_skills', ['competency_id' => $compId, 'skill_id' => $etlId]);
    }
}
