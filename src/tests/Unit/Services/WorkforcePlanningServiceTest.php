<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ScenarioService;
use App\Repository\WorkforcePlanningRepository;
use App\Models\StrategicPlanningScenarios;
use App\Models\WorkforcePlanningRoleForecast;
use App\Models\People;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkforcePlanningServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WorkforcePlanningService $service;
    protected WorkforcePlanningRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new WorkforcePlanningRepository();
        $this->service = new ScenarioService($this->repository);
    }

    /** @test */
    public function it_can_create_a_scenario()
    {
        $scenarioData = [
            'organization_id' => 1,
            'name' => 'Q1 2026 Planning',
            'description' => 'Base scenario for Q1',
            'horizon_months' => 12,
            'fiscal_year' => 2026,
            'status' => 'draft',
            'created_by' => 1,
        ];

        $scenario = $this->repository->createScenario($scenarioData);

        $this->assertInstanceOf(StrategicPlanningScenarios::class, $scenario);
        $this->assertEquals('Q1 2026 Planning', $scenario->name);
        $this->assertEquals('draft', $scenario->status);
    }

    /** @test */
    public function it_can_list_scenarios_by_organization()
    {
        $organizationId = 1;

        // Create 3 scenarios
        for ($i = 0; $i < 3; $i++) {
            $this->repository->createScenario([
                'organization_id' => $organizationId,
                'name' => "Scenario $i",
                'horizon_months' => 12,
                'fiscal_year' => 2026,
                'status' => 'draft',
                'created_by' => 1,
            ]);
        }

        $scenarios = $this->repository->getScenariosByOrganization($organizationId);

        $this->assertEquals(3, $scenarios->total());
    }

    /** @test */
    public function it_can_filter_scenarios_by_status()
    {
        $organizationId = 1;

        $this->repository->createScenario([
            'organization_id' => $organizationId,
            'name' => 'Draft Scenario',
            'horizon_months' => 12,
            'fiscal_year' => 2026,
            'status' => 'draft',
            'created_by' => 1,
        ]);

        $this->repository->createScenario([
            'organization_id' => $organizationId,
            'name' => 'Approved Scenario',
            'horizon_months' => 12,
            'fiscal_year' => 2026,
            'status' => 'approved',
            'created_by' => 1,
        ]);

        $draftScenarios = $this->repository->getScenariosByOrganization($organizationId, ['status' => 'draft']);

        $this->assertEquals(1, $draftScenarios->total());
        $this->assertEquals('draft', $draftScenarios->first()->status);
    }

    /** @test */
    public function it_can_update_a_scenario()
    {
        $scenario = $this->repository->createScenario([
            'organization_id' => 1,
            'name' => 'Original Name',
            'horizon_months' => 12,
            'fiscal_year' => 2026,
            'status' => 'draft',
            'created_by' => 1,
        ]);

        $updated = $this->repository->updateScenario($scenario->id, [
            'name' => 'Updated Name',
            'status' => 'approved',
        ]);

        $this->assertEquals('Updated Name', $updated->name);
        $this->assertEquals('approved', $updated->status);
    }

    /** @test */
    public function it_can_delete_a_scenario()
    {
        $scenario = $this->repository->createScenario([
            'organization_id' => 1,
            'name' => 'To Delete',
            'horizon_months' => 12,
            'fiscal_year' => 2026,
            'status' => 'draft',
            'created_by' => 1,
        ]);

        $deleted = $this->repository->deleteScenario($scenario->id);

        $this->assertTrue($deleted > 0);
        $this->assertNull(StrategicPlanningScenarios::find($scenario->id));
    }

    /** @test */
    public function readiness_level_calculation_is_immediate_with_high_skill_match()
    {
        // Using reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('determineReadinessLevel');
        $method->setAccessible(true);

        $readiness = $method->invoke($this->service, 90, 0);

        $this->assertEquals('immediate', $readiness);
    }

    /** @test */
    public function readiness_level_calculation_is_short_term_with_moderate_match()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('determineReadinessLevel');
        $method->setAccessible(true);

        $readiness = $method->invoke($this->service, 75, 1);

        $this->assertEquals('short_term', $readiness);
    }

    /** @test */
    public function transition_months_calculation_respects_readiness_level()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('estimateTransitionMonths');
        $method->setAccessible(true);

        $immediateMonths = $method->invoke($this->service, 'immediate', 0);
        $shortTermMonths = $method->invoke($this->service, 'short_term', 2);
        $longTermMonths = $method->invoke($this->service, 'long_term', 3);

        $this->assertEquals(1, $immediateMonths);
        $this->assertGreaterThan(2, $shortTermMonths);
        $this->assertGreaterThan(6, $longTermMonths);
    }

    /** @test */
    public function transition_type_is_promotion_with_high_match()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('determineTransitionType');
        $method->setAccessible(true);

        $type = $method->invoke($this->service, 1, 2, 85);

        $this->assertEquals('promotion', $type);
    }

    /** @test */
    public function transition_type_is_lateral_with_moderate_match()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('determineTransitionType');
        $method->setAccessible(true);

        $type = $method->invoke($this->service, 1, 2, 65);

        $this->assertEquals('lateral', $type);
    }

    /** @test */
    public function risk_score_increases_with_gaps()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('calculateRiskScore');
        $method->setAccessible(true);

        $person = new People([
            'id' => 1,
            'name' => 'Test Person',
            'organization_id' => 1,
            'department_id' => 1,
        ]);

        $nGaps = $method->invoke($this->service, $person, [], 'immediate');
        $withGaps = $method->invoke($this->service, $person, [['skill_id' => 1, 'gap' => 2]], 'immediate');

        $this->assertLessThan($withGaps, $nGaps);
    }
}
