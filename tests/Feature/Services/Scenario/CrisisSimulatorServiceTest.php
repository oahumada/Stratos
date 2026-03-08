<?php

namespace Tests\Feature\Services\Scenario;

use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\Skill;
use App\Services\AiOrchestratorService;
use App\Services\Scenario\CrisisSimulatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CrisisSimulatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CrisisSimulatorService $service;
    protected $org;
    protected $scenario;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->org = Organization::factory()->create();
        
        // Mocking dependencies
        $orchestrator = Mockery::mock(AiOrchestratorService::class);
        $this->instance(AiOrchestratorService::class, $orchestrator);

        $this->service = app(CrisisSimulatorService::class);

        $user = \App\Models\User::factory()->create(['organization_id' => $this->org->id]);
        $this->scenario = Scenario::create([
            'organization_id' => $this->org->id,
            'name' => 'Radar Test Scenario',
            'code' => 'RADAR-001',
            'status' => 'active',
            'owner_user_id' => $user->id,
            'created_by' => $user->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'horizon_months' => 12,
            'fiscal_year' => 2026,
            'scope_type' => 'organization_wide'
        ]);
    }

    public function test_simulate_mass_attrition()
    {
        // 1. Arrange: Create workforce
        $role = Roles::factory()->create(['organization_id' => $this->org->id]);
        $people = People::factory()->count(10)->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
            'status' => 'active'
        ]);

        $params = [
            'attrition_rate' => 20, // 2 people should leave
            'timeframe_months' => 6
        ];

        // 2. Act
        $result = $this->service->simulateMassAttrition($this->scenario->id, $params);

        // 3. Assert
        $this->assertEquals('mass_attrition', $result['crisis_type']);
        $this->assertEquals(2, $result['impact']['people_at_risk']);
        $this->assertArrayHasKey('replacement_cost_usd', $result['impact']);
        $this->assertArrayHasKey('mitigation_strategies', $result);
    }

    public function test_simulate_skill_obsolescence()
    {
        // 1. Arrange: Create skills and people having them
        $skill = Skill::factory()->create(['organization_id' => $this->org->id]);
        $role = Roles::factory()->create(['organization_id' => $this->org->id]);
        
        $person = People::factory()->create(['organization_id' => $this->org->id, 'role_id' => $role->id]);
        PeopleRoleSkills::create([
            'people_id' => $person->id,
            'skill_id' => $skill->id,
            'role_id' => $role->id,
            'current_level' => 3,
            'is_active' => true
        ]);

        $params = [
            'obsolete_skill_ids' => [$skill->id],
            'emerging_skills' => ['AI Tools'],
            'horizon_months' => 12
        ];

        // 2. Act
        $result = $this->service->simulateSkillObsolescence($this->scenario->id, $params);

        // 3. Assert
        $this->assertEquals('skill_obsolescence', $result['crisis_type']);
        $this->assertEquals(1, $result['impact']['people_affected']);
        $this->assertEquals(3500, $result['impact']['total_reskill_cost_usd']); // 1 person * 3500
        $this->assertArrayHasKey('transition_plan', $result);
    }
}
