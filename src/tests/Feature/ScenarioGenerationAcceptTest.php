<?php

namespace Tests\Feature;

use App\Models\ScenarioGeneration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScenarioGenerationAcceptTest extends TestCase
{
    use RefreshDatabase;

    public function test_accept_generation_creates_scenario_and_persists_prompt()
    {
        $org = \App\Models\Organizations::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        $this->actingAs($user);

        $llmResponse = [
            'scenario_metadata' => ['name' => 'ACME Generated', 'description' => 'Generated desc', 'horizon_months' => 6],
            'capabilities' => [],
            'competencies' => [],
            'skills' => [],
            'suggested_roles' => [],
            'impact_analysis' => [],
        ];

        $generation = ScenarioGeneration::create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'prompt' => 'redacted prompt',
            'llm_response' => $llmResponse,
            'status' => 'complete',
            'redacted' => true,
        ]);

        $res = $this->postJson("/api/strategic-planning/scenarios/generate/{$generation->id}/accept");

        $res->assertStatus(201);
        $res->assertJson(['success' => true]);

        $this->assertDatabaseHas('scenarios', ['source_generation_id' => $generation->id, 'name' => 'ACME Generated']);

        $generation->refresh();
        $this->assertArrayHasKey('accepted_by', $generation->metadata);
        $this->assertArrayHasKey('created_scenario_id', $generation->metadata);
    }
}
