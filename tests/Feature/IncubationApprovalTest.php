<?php

namespace Tests\Feature;

use App\Models\Competency;
use App\Models\Organization;
use App\Models\Scenario;
use App\Models\TalentBlueprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncubationApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected $org;
    protected $user;
    protected $scenario;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->org = Organization::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->org->id]);

        $this->scenario = Scenario::create([
            'organization_id' => $this->org->id,
            'name' => 'AI Transformation',
            'code' => 'TEST-SCN-001',
            'status' => 'incubating',
            'owner_user_id' => $this->user->id,
            'created_by' => $this->user->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(6)->toDateString(),
            'horizon_months' => 6,
            'fiscal_year' => 2026,
            'scope_type' => 'organization_wide'
        ]);
    }

    public function test_can_approve_incubated_competency()
    {
        $competency = Competency::create([
            'organization_id' => $this->org->id,
            'name' => 'AI Prompting',
            'status' => 'in_incubation',
            'discovered_in_scenario_id' => $this->scenario->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/incubated-items/approve", [
                'items' => [
                    ['type' => 'competency', 'id' => $competency->id]
                ]
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('competencies', [
            'id' => $competency->id,
            'status' => 'active'
        ]);
    }

    public function test_can_approve_incubated_role_blueprint()
    {
        // satisfying all NOT NULL columns discovered via introspection
        $blueprint = TalentBlueprint::create([
            'scenario_id' => $this->scenario->id,
            'role_name' => 'AI Engineer',
            'role_description' => 'Develops AI apps',
            'status' => 'in_incubation',
            'total_fte_required' => 2.0,
            'human_leverage' => 80,
            'synthetic_leverage' => 20,
            'recommended_strategy' => 'augment',
            'agent_specs' => ['type' => 'analyst']
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/incubated-items/approve", [
                'items' => [
                    ['type' => 'role', 'id' => $blueprint->id]
                ]
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('talent_blueprints', [
            'id' => $blueprint->id,
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('roles', [
            'organization_id' => $this->org->id,
            'name' => 'AI Engineer',
            'status' => 'active'
        ]);

        $role = \App\Models\Roles::where('name', 'AI Engineer')->first();
        $this->assertNotNull($role);

        $this->assertDatabaseHas('scenario_roles', [
            'scenario_id' => $this->scenario->id,
            'role_id' => $role->id,
            'archetype' => 'E'
        ]);
    }
}
