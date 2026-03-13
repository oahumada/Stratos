<?php

namespace Tests\Feature\Intelligence;

use App\Models\FinancialIndicator;
use App\Models\Organizations;
use App\Models\People;
use App\Models\Scenario;
use App\Models\User;
use App\Services\Scenario\AgenticScenarioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpactScenarioIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected AgenticScenarioService $scenarioService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock de IA usando Mockery para evitar TypeErrors y llamadas reales
        $orchestrator = \Mockery::mock(\App\Services\AiOrchestratorService::class);
        $orchestrator->shouldReceive('agentThink')
            ->andReturn([
                'response' => json_encode([
                    'logic_narrative' => 'Mock logic',
                    'correlations' => [],
                    'insight_summary' => 'Mock insight',
                    'recommendations' => []
                ])
            ]);
        
        $this->app->instance(\App\Services\AiOrchestratorService::class, $orchestrator);
        
        $this->scenarioService = app(AgenticScenarioService::class);
    }

    /** @test */
    public function testCalculatesScenarioCostsBasedOnDynamicImpactBenchmarks()
    {
        // 1. Setup Data
        $org = Organizations::factory()->create(['size' => 'medium']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        
        $scenario = Scenario::factory()->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'owner_user_id' => $user->id,
            'status' => 'draft'
        ]);

        // Crear departamentos y empleados
        $deptA = \App\Models\Departments::create(['organization_id' => $org->id, 'name' => 'IT Dept ' . uniqid()]);
        $deptB = \App\Models\Departments::create(['organization_id' => $org->id, 'name' => 'Sales Dept ' . uniqid()]);
        
        People::factory()->count(5)->create(['organization_id' => $org->id, 'department_id' => $deptA->id]);
        People::factory()->count(5)->create(['organization_id' => $org->id, 'department_id' => $deptB->id]);

        // 2. Setup initial benchmarks in Impact Engine
        FinancialIndicator::create([
            'organization_id' => $org->id,
            'indicator_type' => 'avg_annual_salary',
            'value' => 50000,
            'reference_date' => now()
        ]);

        FinancialIndicator::create([
            'organization_id' => $org->id,
            'indicator_type' => 'avg_recruitment_cost',
            'value' => 2000,
            'reference_date' => now()
        ]);

        // 3. Run simulation (Expansion 20% -> 10 * 0.2 = 2 new positions)
        // Recruitment cost should be 2 * 2000 = 4000
        $result1 = $this->scenarioService->runAgenticSimulation($scenario->id, [
            'change_type' => 'expansion',
            'growth_percentage' => 20
        ]);

        // Verificamos que se calculó con el benchmark (2 * 2000)
        $this->assertEquals(4000, abs($result1['kpi_impact']['cost_impact_usd']));

        // 4. Update benchmarks (Costo de reclutamiento sube a 10k)
        FinancialIndicator::where('indicator_type', 'avg_recruitment_cost')
            ->update(['value' => 10000]);

        // 5. Run simulation again
        // Recruitment cost should be 2 * 10000 = 20000
        $result2 = $this->scenarioService->runAgenticSimulation($scenario->id, [
            'change_type' => 'expansion',
            'growth_percentage' => 20
        ]);

        $this->assertEquals(20000, abs($result2['kpi_impact']['cost_impact_usd']));

        // 6. Verify Team Merge impact (Savings)
        // With avg_annual_salary = 50k, merging deptA (5) and deptB (5) = 10 total.
        // 20% redundancy (2 people) should save 100k
        $resultMerge = $this->scenarioService->runAgenticSimulation($scenario->id, [
            'change_type' => 'team_merge',
            'team_a_department_id' => $deptA->id,
            'team_b_department_id' => $deptB->id,
            'expected_redundancy_rate' => 20
        ]);

        // En team_merge, 2 personas de redundancia * 50000 = 100000 de ahorro
        $this->assertEquals(100000, $resultMerge['kpi_impact']['cost_impact_usd']);
    }
}
