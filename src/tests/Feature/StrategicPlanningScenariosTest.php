<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\ScenarioTemplate;
use App\Models\User;
use App\Models\StrategicPlanningScenarios;
use Tests\TestCase;

class StrategicPlanningScenariosTest extends TestCase
{
    protected User $user;
    protected Organizations $organization;
    protected ScenarioTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organizations::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
        $this->template = ScenarioTemplate::factory()->create();
    }

    public function test_create_workforce_scenario(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('//api/workforce-planning/workforce-scenarios', [
                'name' => 'Test Scenario',
                'description' => 'Test Description',
                'scenario_type' => 'growth',
                'horizon_months' => 12,
                'fiscal_year' => 2026,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'scenario_type',
                    'organization_id',
                    'created_by',
                ]
            ]);

        $this->assertDatabaseHas('workforce_planning_scenarios', [
            'organization_id' => $this->organization->id,
            'name' => 'Test Scenario',
        ]);
    }

    public function test_tenant_isolation_prevents_cross_org_access(): void
    {
        $otherOrg = Organizations::factory()->create();
        $scenario = StrategicPlanningScenarios::factory()->create([
            'organization_id' => $otherOrg->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("//api/workforce-planning/workforce-scenarios/{$scenario->id}");

        $response->assertForbidden();
    }

    public function test_list_scenarios_filtered_by_organization(): void
    {
        StrategicPlanningScenarios::factory(3)->create([
            'organization_id' => $this->organization->id,
        ]);

        StrategicPlanningScenarios::factory(2)->create([
            'organization_id' => Organizations::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('//api/workforce-planning/workforce-scenarios');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_instantiate_scenario_from_template(): void
    {
        $template = ScenarioTemplate::factory()->create([
            'config' => [
                'predefined_skills' => [
                    [
                        'skill_id' => 1,
                        'required_headcount' => 5,
                        'required_level' => 4,
                        'priority' => 'critical',
                    ]
                ]
            ]
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("//api/workforce-planning/workforce-scenarios/{$template->id}/instantiate-from-template", [
                'name' => 'Custom Implementation',
                'horizon_months' => 18,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'template_id',
                    'skill_demands' => [
                        '*' => ['skill_id', 'required_headcount', 'required_level']
                    ]
                ]
            ]);

        $this->assertDatabaseHas('workforce_planning_scenarios', [
            'template_id' => $template->id,
            'name' => 'Custom Implementation',
        ]);
    }

    public function test_calculate_scenario_gaps(): void
    {
        $scenario = StrategicPlanningScenarios::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("//api/workforce-planning/workforce-scenarios/{$scenario->id}/calculate-gaps");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'scenario_id',
                    'generated_at',
                    'summary' => [
                        'total_skills',
                        'critical_skills',
                        'avg_coverage_pct',
                        'risk_score',
                    ],
                    'gaps' => []
                ]
            ]);
    }

    public function test_unauthorized_user_cannot_update_scenario(): void
    {
        $otherOrg = Organizations::factory()->create();
        $otherUser = User::factory()->create(['organization_id' => $otherOrg->id]);
        $scenario = StrategicPlanningScenarios::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->patchJson("//api/workforce-planning/workforce-scenarios/{$scenario->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertForbidden();
    }

    public function test_filter_scenarios_by_status(): void
    {
        StrategicPlanningScenarios::factory(2)->create([
            'organization_id' => $this->organization->id,
            'status' => 'draft',
        ]);

        StrategicPlanningScenarios::factory(1)->create([
            'organization_id' => $this->organization->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('//api/workforce-planning/workforce-scenarios?status=draft');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
