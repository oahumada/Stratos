<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\Competency;
use App\Models\ScenarioRoleCompetency;
use App\Models\Organizations;
use App\Models\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class Step2RoleCompetencyApiTest extends TestCase
{
    use RefreshDatabase;

    protected Organizations $organization;
    protected User $user;
    protected Scenario $scenario;
    protected Roles $baseRole;
    protected ScenarioRole $scenarioRole;
    protected Competency $competency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organizations::factory()->create();

        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        Sanctum::actingAs($this->user);

        $this->baseRole = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'Role Base',
            'level' => 'mid',
        ]);

        $this->scenario = Scenario::create([
            'organization_id' => $this->organization->id,
            'name' => 'Scenario Test',
            'horizon_months' => 12,
            'fiscal_year' => 2026,
            'scope_type' => 'organization_wide',
            'owner_user_id' => $this->user->id,
            'created_by' => $this->user->id,
        ]);

        $this->scenarioRole = ScenarioRole::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $this->baseRole->id,
            'fte' => 2,
            'role_change' => 'evolve',
            'impact_level' => 'medium',
            'evolution_type' => 'incremental',
        ]);

        $this->competency = Competency::create([
            'organization_id' => $this->organization->id,
            'name' => 'Competency Base',
        ]);
    }

    /** @test */
    public function test_can_get_matrix_data()
    {
        $response = $this->getJson("/api/scenarios/{$this->scenario->id}/step2/data");
        $response->assertOk()
            ->assertJsonStructure([
                'scenario' => ['id', 'name', 'horizon_months'],
                'roles' => ['*' => ['id', 'role_id', 'role_name', 'fte']],
                'competencies' => ['*' => ['id', 'name']],
                'mappings' => [],
            ]);
    }

    /** @test */
    public function test_can_save_mapping_for_new_role_competency()
    {
        $response = $this->postJson(
            "/api/scenarios/{$this->scenario->id}/step2/mappings",
            [
                'role_id' => $this->scenarioRole->id,
                'competency_id' => $this->competency->id,
                'required_level' => 4,
                'is_core' => true,
                'change_type' => 'transformation',
                'rationale' => 'Required for AI initiative',
            ]
        );
        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'mapping',
                'message',
            ]);
    }

    /** @test */
    public function test_validates_required_fields_for_mapping()
    {
        $response = $this->postJson(
            "/api/scenarios/{$this->scenario->id}/step2/mappings",
            ['role_id' => $this->scenarioRole->id]
        );
        $response->assertUnprocessable();
    }

    /** @test */
    public function test_validates_change_type_enum()
    {
        $response = $this->postJson(
            "/api/scenarios/{$this->scenario->id}/step2/mappings",
            [
                'role_id' => $this->scenarioRole->id,
                'competency_id' => $this->competency->id,
                'required_level' => 4,
                'change_type' => 'invalid_type',
                'is_core' => false,
                'rationale' => 'Test',
            ]
        );
        $response->assertUnprocessable();
    }

    /** @test */
    public function test_can_delete_mapping()
    {
        $mapping = ScenarioRoleCompetency::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $this->scenarioRole->id,
            'competency_id' => $this->competency->id,
            'required_level' => 3,
            'is_core' => true,
            'change_type' => 'maintenance',
        ]);
        $response = $this->deleteJson(
            "/api/scenarios/{$this->scenario->id}/step2/mappings/{$mapping->id}"
        );
        $response->assertOk();
    }

    /** @test */
    public function test_cannot_delete_nonexistent_mapping()
    {
        $response = $this->deleteJson(
            "/api/scenarios/{$this->scenario->id}/step2/mappings/99999"
        );
        $response->assertNotFound();
    }

    /** @test */
    public function test_can_add_role_from_existing()
    {
        // Create a new role to add (not the one already in the scenario)
        $newRole = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'Data Scientist',
            'level' => 'senior',
        ]);

        $response = $this->postJson(
            "/api/scenarios/{$this->scenario->id}/step2/roles",
            [
                'role_id' => $newRole->id,
                'fte' => 2,
                'role_change' => 'create',
                'evolution_type' => 'new_role',
                'impact_level' => 'high',
            ]
        );
        $response->assertCreated();
    }

    /** @test */
    public function test_can_add_role_new_creation()
    {
        $response = $this->postJson(
            "/api/scenarios/{$this->scenario->id}/step2/roles",
            [
                'role_name' => 'AI Architect',
                'fte' => 3,
                'role_change' => 'create',
                'evolution_type' => 'new_role',
                'impact_level' => 'high',
            ]
        );
        $response->assertCreated();
    }

    /** @test */
    public function test_can_get_role_forecasts()
    {
        $response = $this->getJson(
            "/api/scenarios/{$this->scenario->id}/step2/role-forecasts"
        );
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'role_id',
                        'role_name',
                        'fte_future',
                        'fte_current',
                        'fte_delta',
                        'evolution_type',
                        'impact_level',
                    ],
                ],
            ]);
    }

    /** @test */
    public function test_can_get_skill_gaps_matrix()
    {
        $response = $this->getJson(
            "/api/scenarios/{$this->scenario->id}/step2/skill-gaps-matrix"
        );
        $response->assertOk()
            ->assertJsonStructure([
                'roles',
                'skills',
                'gaps',
            ]);
    }

    /** @test */
    public function test_can_get_matching_results()
    {
        $response = $this->getJson(
            "/api/scenarios/{$this->scenario->id}/step2/matching-results"
        );
        $response->assertOk();
    }

    /** @test */
    public function test_can_get_succession_plans()
    {
        $response = $this->getJson(
            "/api/scenarios/{$this->scenario->id}/step2/succession-plans"
        );
        $response->assertOk();
    }

    /** @test */
    public function test_respects_organization_isolation()
    {
        $otherOrg = Organizations::factory()->create();
        $otherUser = User::factory()->create([
            'organization_id' => $otherOrg->id,
        ]);

        $otherScenario = Scenario::create([
            'organization_id' => $otherOrg->id,
            'name' => 'Scenario Other',
            'horizon_months' => 12,
            'fiscal_year' => 2026,
            'scope_type' => 'organization_wide',
            'owner_user_id' => $otherUser->id,
            'created_by' => $otherUser->id,
        ]);
        $response = $this->getJson(
            "/api/scenarios/{$otherScenario->id}/step2/data"
        );
        // Expect 404 because the scenario doesn't exist for this tenant
        // (firstOrFail() throws 404 when org_id doesn't match)
        $response->assertNotFound();
    }
}
