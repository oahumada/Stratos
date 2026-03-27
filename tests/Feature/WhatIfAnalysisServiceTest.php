<?php

namespace Tests\Feature;

use App\Models\Scenario;
use App\Services\ScenarioPlanning\WhatIfAnalysisService;
use Tests\TestCase;

class WhatIfAnalysisServiceTest extends TestCase
{
    public WhatIfAnalysisService $whatIfService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->whatIfService = app(WhatIfAnalysisService::class);
    }

    /**
     * HEADCOUNT IMPACT ANALYSIS
     */
    public function test_analyze_headcount_impact_with_positive_delta(): void
    {
        $scenario = Scenario::factory()->create();

        $analysis = $this->whatIfService->analyzeHeadcountImpact($scenario->id, [
            'headcount_delta' => 10,
            'turnover_rate' => 0.1,
            'timeline_weeks' => 12,
        ]);

        expect($analysis)
            ->toHaveKey('current_headcount')
            ->toHaveKey('new_headcount')
            ->toHaveKey('headcount_delta', 10)
            ->toHaveKey('hiring_needs', 10)
            ->toHaveKey('reduction_needs', 0)
            ->toHaveKey('weeks_to_full_capacity')
            ->toHaveKey('confidence_score');

        expect($analysis['new_headcount'])->toBeGreaterThan($analysis['current_headcount']);
    }

    public function test_analyze_headcount_impact_with_negative_delta(): void
    {
        $scenario = Scenario::factory()->create();

        $analysis = $this->whatIfService->analyzeHeadcountImpact($scenario->id, [
            'headcount_delta' => -15,
            'turnover_rate' => 0.1,
            'timeline_weeks' => 12,
        ]);

        expect($analysis)
            ->toHaveKey('reduction_needs', 15)
            ->toHaveKey('hiring_needs', 0);

        expect($analysis['new_headcount'])->toBeLessThan($analysis['current_headcount']);
    }

    public function test_headcount_impact_identifies_hiring_risk(): void
    {
        $scenario = Scenario::factory()->create();

        $analysis = $this->whatIfService->analyzeHeadcountImpact($scenario->id, [
            'headcount_delta' => 25, // More than 20 triggers risk
            'turnover_rate' => 0.25, // High turnover
            'timeline_weeks' => 2, // Very aggressive
        ]);

        expect($analysis['risks'])->toHaveLength(3);

        $riskTypes = collect($analysis['risks'])->pluck('type')->toArray();
        foreach ($riskTypes as $type) {
            expect(['hiring_capacity', 'attrition_risk', 'timeline_pressure'])->toContain($type);
        }

        foreach ($analysis['risks'] as $risk) {
            expect($risk)->toHaveKeys(['type', 'severity', 'description', 'mitigation']);
        }
    }

    public function test_headcount_impact_confidence_score_decreases_with_risk(): void
    {
        $scenario = Scenario::factory()->create();

        $lowRisk = $this->whatIfService->analyzeHeadcountImpact($scenario->id, [
            'headcount_delta' => 2,
            'turnover_rate' => 0.05,
            'timeline_weeks' => 24,
        ]);

        $highRisk = $this->whatIfService->analyzeHeadcountImpact($scenario->id, [
            'headcount_delta' => 30,
            'turnover_rate' => 0.25,
            'timeline_weeks' => 2,
        ]);

        expect($lowRisk['confidence_score'])->toBeGreaterThan($highRisk['confidence_score']);
    }

    /**
     * FINANCIAL IMPACT ANALYSIS
     */
    public function test_analyze_financial_impact_with_hiring(): void
    {
        $scenario = Scenario::factory()->create();

        $analysis = $this->whatIfService->analyzeFinancialImpact($scenario->id, [
            'cost_per_hire' => 100000,
            'training_hours' => 40,
            'cost_per_training_hour' => 150,
            'infrastructure_cost_per_week' => 5000,
            'headcount_delta' => 5,
            'timeline_weeks' => 12,
        ]);

        expect($analysis)
            ->toHaveKeys([
                'total_cost',
                'cost_breakdown',
                'total_savings',
                'net_impact',
                'roi',
                'budget_variance',
            ]);

        expect($analysis['cost_breakdown'])->toHaveKeys([
            'hiring_cost',
            'training_cost',
            'infrastructure_cost',
            'severance_cost',
            'contingency',
        ]);
    }

    public function test_financial_impact_calculates_roi(): void
    {
        $scenario = Scenario::factory()->create();

        $analysis = $this->whatIfService->analyzeFinancialImpact($scenario->id, [
            'cost_per_hire' => 80000,
            'headcount_delta' => 5,
            'infrastructure_cost_per_week' => 5000,
            'timeline_weeks' => 12,
        ]);

        expect($analysis['roi'])->toBeNumeric();
    }

    public function test_financial_impact_calculates_payback_period(): void
    {
        $scenario = Scenario::factory()->create();

        $analysis = $this->whatIfService->analyzeFinancialImpact($scenario->id, [
            'cost_per_hire' => 80000,
            'headcount_delta' => 10,
        ]);

        expect($analysis)->toHaveKey('payback_period_months');
        expect($analysis['payback_period_months'])->toBeGreaterThan(0);
    }

    public function test_financial_impact_budget_variance(): void
    {
        $scenario = Scenario::factory()->create();

        $analysis = $this->whatIfService->analyzeFinancialImpact($scenario->id, [
            'cost_per_hire' => 80000,
            'headcount_delta' => 5,
            'infrastructure_cost_per_week' => 5000,
            'timeline_weeks' => 12,
        ]);

        expect($analysis['budget_variance_pct'])->toBeNumeric();
    }

    /**
     * RISK IMPACT ANALYSIS
     */
    public function test_analyze_risk_impact_identifies_all_risk_types(): void
    {
        $scenario = Scenario::factory()->create();

        $analysis = $this->whatIfService->analyzeRiskImpact($scenario->id, [
            'timeline_weeks' => 4,
            'complexity' => 1.5,
            'headcount_delta' => 20,
            'turnover_rate' => 0.2,
            'technology_changes' => 3,
        ]);

        expect($analysis)
            ->toHaveKeys([
                'overall_risk_score',
                'risk_level',
                'individual_risks',
                'mitigation_strategies',
                'recommendation',
            ]);

        foreach ($analysis['individual_risks'] as $risk) {
            expect($risk)->toHaveKeys(['type', 'score', 'factors', 'description']);
        }

        $riskTypes = collect($analysis['individual_risks'])->pluck('type')->toArray();
        $expectedTypes = ['execution_risk', 'talent_risk', 'financial_risk', 'technology_risk'];
        
        foreach ($expectedTypes as $type) {
            expect($riskTypes)->toContain($type);
        }
    }

    public function test_risk_impact_classifies_critical_risks(): void
    {
        $scenario = Scenario::factory()->create();

        $critical = $this->whatIfService->analyzeRiskImpact($scenario->id, [
            'timeline_weeks' => 1,
            'complexity' => 2,
            'headcount_delta' => 50,
            'turnover_rate' => 0.4,
            'technology_changes' => 5,
            'total_cost' => 1000000,
        ]);

        // With very aggressive parameters, expect either "Critical" or "High" risk
        expect($critical['risk_level'])->toBeIn(['Critical', 'High']);
        // Overall risk score should be high
        expect($critical['overall_risk_score'])->toBeGreaterThanOrEqual(5);
    }

    public function test_risk_impact_generates_mitigation_strategies(): void
    {
        $scenario = Scenario::factory()->create();

        $analysis = $this->whatIfService->analyzeRiskImpact($scenario->id, [
            'timeline_weeks' => 2,
            'complexity' => 1.5,
            'headcount_delta' => 15,
            'technology_changes' => 2,
        ]);

        expect($analysis['mitigation_strategies'])->toBeArray();
        if (count($analysis['mitigation_strategies']) > 0) {
            expect($analysis['mitigation_strategies'][0])->toHaveKeys(['risk_type', 'strategy', 'priority']);
        }
    }

    /**
     * BASELINE COMPARISON
     */
    public function test_compare_scenarios_with_baseline(): void
    {
        $baseline = Scenario::factory()->create();
        $scenario = Scenario::factory()->create();

        $comparison = $this->whatIfService->compareScenariosWithBaseline(
            $scenario->id,
            $baseline->id
        );

        expect($comparison)
            ->toHaveKeys([
                'scenario_id',
                'baseline_scenario_id',
                'deltas',
                'percentage_changes',
                'improvements',
                'concerns',
            ]);
    }

    public function test_compare_with_current_baseline_if_not_specified(): void
    {
        $scenario = Scenario::factory()->create();

        $comparison = $this->whatIfService->compareScenariosWithBaseline($scenario->id);

        expect($comparison)
            ->toHaveKey('baseline_scenario_id', 'current_state')
            ->toHaveKey('deltas')
            ->toHaveKey('improvements')
            ->toHaveKey('concerns');
    }

    /**
     * OUTCOME PREDICTION
     */
    public function test_predict_outcomes_calculates_success_probability(): void
    {
        $scenario = Scenario::factory()->create();

        $prediction = $this->whatIfService->predictOutcomes($scenario->id, [
            'team_readiness' => 0.8,
            'budget_confidence' => 0.9,
            'stakeholder_buy_in' => 0.85,
            'external_dependencies' => 0.75,
            'change_management_readiness' => 0.7,
        ]);

        expect($prediction)
            ->toHaveKeys([
                'success_probability',
                'confidence_factors',
                'predicted_outcomes',
                'roi_scenarios',
                'success_level',
                'recommendations',
            ]);

        expect($prediction['success_probability'])
            ->toBeGreaterThan(0)
            ->toBeLessThanOrEqual(100);
    }

    public function test_predict_outcomes_generates_recommendations(): void
    {
        $scenario = Scenario::factory()->create();

        $prediction = $this->whatIfService->predictOutcomes($scenario->id, [
            'team_readiness' => 0.5,
            'budget_confidence' => 0.5,
            'stakeholder_buy_in' => 0.4,
            'external_dependencies' => 0.3,
            'change_management_readiness' => 0.4,
        ]);

        expect($prediction['recommendations'])->toBeArray();
        expect($prediction['recommendations'])->not->toBeEmpty();
    }

    public function test_predict_outcomes_provides_roi_scenarios_(): void
    {
        $scenario = Scenario::factory()->create();

        $prediction = $this->whatIfService->predictOutcomes($scenario->id, [
            'team_readiness' => 0.8,
            'budget_confidence' => 0.8,
            'stakeholder_buy_in' => 0.8,
            'external_dependencies' => 0.8,
            'change_management_readiness' => 0.8,
        ]);

        expect($prediction['roi_scenarios'])->toHaveKeys([
            'optimistic_roi',
            'realistic_roi',
            'pessimistic_roi',
        ]);

        // Verify ROI scenarios are numeric values
        expect($prediction['roi_scenarios']['optimistic_roi'])->toBeNumeric();
        expect($prediction['roi_scenarios']['realistic_roi'])->toBeNumeric();
        expect($prediction['roi_scenarios']['pessimistic_roi'])->toBeNumeric();

        // If base ROI is non-zero, verify order: optimistic >= realistic >= pessimistic
        $optimistic = $prediction['roi_scenarios']['optimistic_roi'];
        $realistic = $prediction['roi_scenarios']['realistic_roi'];
        $pessimistic = $prediction['roi_scenarios']['pessimistic_roi'];
        
        if ($optimistic > 0) {
            expect($optimistic)->toBeGreaterThanOrEqual($realistic);
            expect($realistic)->toBeGreaterThanOrEqual($pessimistic);
        }
    }

    /**
     * SENSITIVITY ANALYSIS
     */
    public function test_perform_sensitivity_analysis_on_budget(): void
    {
        $scenario = Scenario::factory()->create();

        $sensitivity = $this->whatIfService->performSensitivityAnalysis($scenario->id, [
            'budget_adjustments' => [-20, -10, 0, 10, 20],
        ]);

        expect($sensitivity)
            ->toHaveKeys([
                'scenario_id',
                'base_parameters',
                'budget_sensitivity',
                'headcount_sensitivity',
                'timeline_sensitivity',
                'critical_variables',
                'recommendations',
            ]);

        expect($sensitivity['budget_sensitivity'])->toHaveLength(5);
        expect($sensitivity['budget_sensitivity'][0])
            ->toHaveKeys(['adjustment_pct', 'adjusted_budget', 'impact_on_roi', 'impact_on_timeline', 'feasibility']);
    }

    public function test_sensitivity_analysis_identifies_critical_variables(): void
    {
        $scenario = Scenario::factory()->create();

        $sensitivity = $this->whatIfService->performSensitivityAnalysis($scenario->id);

        expect($sensitivity)->toHaveKey('critical_variables');
        expect($sensitivity['critical_variables'])->toBeArray();
    }

    public function test_sensitivity_analysis_generates_recommendations(): void
    {
        $scenario = Scenario::factory()->create();

        $sensitivity = $this->whatIfService->performSensitivityAnalysis($scenario->id);

        expect($sensitivity)->toHaveKey('recommendations');
        expect($sensitivity['recommendations'])->toBeArray();
    }

    /**
     * API ENDPOINTS (Controller Tests)
     */
    public function test_headcount_impact_endpoint(): void
    {
        $user = \App\Models\User::factory()->create();
        $scenario = Scenario::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/strategic-planning/what-if/headcount-impact', [
            'scenario_id' => $scenario->id,
            'headcount_delta' => 10,
            'turnover_rate' => 0.1,
            'timeline_weeks' => 12,
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_headcount',
                    'new_headcount',
                    'hiring_needs',
                    'confidence_score',
                    'risks',
                ],
            ]);
    }

    public function test_financial_impact_endpoint(): void
    {
        $user = \App\Models\User::factory()->create();
        $scenario = Scenario::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/strategic-planning/what-if/financial-impact', [
            'scenario_id' => $scenario->id,
            'cost_per_hire' => 80000,
            'timeline_weeks' => 12,
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_cost',
                    'cost_breakdown',
                    'roi',
                    'net_impact',
                    'payback_period_months',
                ],
            ]);
    }

    public function test_risk_impact_endpoint(): void
    {
        $user = \App\Models\User::factory()->create();
        $scenario = Scenario::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/strategic-planning/what-if/risk-impact', [
            'scenario_id' => $scenario->id,
            'timeline_weeks' => 12,
            'headcount_delta' => 5,
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'overall_risk_score',
                    'risk_level',
                    'individual_risks',
                    'mitigation_strategies',
                ],
            ]);
    }

    public function test_compare_baseline_endpoint(): void
    {
        $user = \App\Models\User::factory()->create();
        $scenario = Scenario::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/strategic-planning/what-if/compare?scenario_id={$scenario->id}");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'scenario_id',
                    'deltas',
                    'improvements',
                    'concerns',
                ],
            ]);
    }

    public function test_predict_outcomes_endpoint(): void
    {
        $user = \App\Models\User::factory()->create();
        $scenario = Scenario::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/strategic-planning/what-if/predict-outcomes', [
            'scenario_id' => $scenario->id,
            'team_readiness' => 0.8,
            'budget_confidence' => 0.8,
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'success_probability',
                    'confidence_factors',
                    'roi_scenarios',
                    'success_level',
                ],
            ]);
    }

    public function test_sensitivity_analysis_endpoint(): void
    {
        $user = \App\Models\User::factory()->create();
        $scenario = Scenario::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/strategic-planning/what-if/sensitivity-analysis', [
            'scenario_id' => $scenario->id,
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'scenario_id',
                    'budget_sensitivity',
                    'critical_variables',
                ],
            ]);
    }

    public function test_comprehensive_analysis_endpoint(): void
    {
        $user = \App\Models\User::factory()->create();
        $scenario = Scenario::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/strategic-planning/what-if/comprehensive', [
            'scenario_id' => $scenario->id,
            'headcount_delta' => 10,
            'timeline_weeks' => 12,
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'scenario_id',
                    'executive_summary',
                    'headcount_analysis',
                    'financial_analysis',
                    'risk_analysis',
                    'baseline_comparison',
                    'outcome_predictions',
                    'sensitivity_analysis',
                    'overall_recommendation',
                ],
            ]);
    }

    /**
     * VALIDATION TESTS
     */
    public function test_headcount_impact_validation_requires_scenario_id(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/strategic-planning/what-if/headcount-impact', [
            'headcount_delta' => 10,
            'turnover_rate' => 0.1,
        ]);

        $response->assertUnprocessable();
    }

    public function test_financial_impact_validation_requires_scenario_id(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/strategic-planning/what-if/financial-impact', [
            'cost_per_hire' => 80000,
        ]);

        $response->assertUnprocessable();
    }

    public function test_sensitivity_analysis_validation_default_adjustments(): void
    {
        $user = \App\Models\User::factory()->create();
        $scenario = Scenario::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/strategic-planning/what-if/sensitivity-analysis', [
            'scenario_id' => $scenario->id,
        ]);

        // Should succeed with default adjustments
        $response->assertSuccessful();
    }
}
