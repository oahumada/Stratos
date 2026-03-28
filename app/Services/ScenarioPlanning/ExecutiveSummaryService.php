<?php

namespace App\Services\ScenarioPlanning;

use App\Models\Scenario;
use Illuminate\Support\Facades\Log;

/**
 * ExecutiveSummaryService — Generate executive summaries for scenarios
 *
 * Provides executive-level insights for scenario decisions:
 * - KPI aggregation (cost, risk, timeline, ROI, headcount, capability)
 * - Decision recommendations (proceed, revise, reject)
 * - Comparison to company baseline
 * - Risk heatmap visualization data
 * - Executive readiness assessment
 */
class ExecutiveSummaryService
{
    public function __construct(
        private WhatIfAnalysisService $whatIfService,
        private ScenarioTemplateService $templateService,
    ) {
    }

    /**
     * Generate comprehensive executive summary for a scenario
     *
     * @param  int  $scenarioId
     * @param  array<string, mixed>  $options  Options: {baseline_scenario_id?, include_recommendations?}
     * @return array<string, mixed>
     */
    public function generateExecutiveSummary(int $scenarioId, array $options = []): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        // Collect all analyses
        $headcountAnalysis = $this->whatIfService->analyzeHeadcountImpact($scenarioId, [
            'headcount_delta' => $scenario->headcount_delta ?? 0,
            'turnover_rate' => $scenario->turnover_rate ?? 0.1,
            'timeline_weeks' => $scenario->timeline_weeks ?? 12,
        ]);

        $financialAnalysis = $this->whatIfService->analyzeFinancialImpact($scenarioId, [
            'headcount_delta' => $scenario->headcount_delta ?? 0,
            'cost_per_hire' => $scenario->cost_per_hire ?? 80000,
            'training_hours' => $scenario->training_hours ?? 40,
            'timeline_weeks' => $scenario->timeline_weeks ?? 12,
        ]);

        $riskAnalysis = $this->whatIfService->analyzeRiskImpact($scenarioId, [
            'timeline_weeks' => $scenario->timeline_weeks ?? 12,
            'headcount_delta' => $scenario->headcount_delta ?? 0,
            'turnover_rate' => $scenario->turnover_rate ?? 0.1,
            'complexity' => $scenario->complexity_factor ?? 1,
            'technology_changes' => $scenario->technology_changes ?? 0,
            'total_cost' => $financialAnalysis['total_cost'] ?? 0,
        ]);

        // Build KPI cards
        $kpis = $this->buildKPICards($scenario, $headcountAnalysis, $financialAnalysis, $riskAnalysis);

        // Generate decision recommendation
        $recommendation = $this->generateDecisionRecommendation($scenario, $kpis, $riskAnalysis);

        // Baseline comparison
        $baselineComparison = null;
        if ($options['baseline_scenario_id'] ?? false) {
            $baselineComparison = $this->whatIfService->compareScenariosWithBaseline(
                $scenarioId,
                $options['baseline_scenario_id']
            );
        }

        // Risk heatmap data
        $riskHeatmap = $this->buildRiskHeatmap($riskAnalysis);

        // Readiness assessment
        $readinessAssessment = $this->assessExecutiveReadiness($scenario, $kpis, $riskAnalysis);

        return [
            'scenario_id' => $scenarioId,
            'scenario_name' => $scenario->name,
            'kpis' => $kpis,
            'decision_recommendation' => $recommendation,
            'baseline_comparison' => $baselineComparison,
            'risk_heatmap' => $riskHeatmap,
            'readiness_assessment' => $readinessAssessment,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Build KPI cards for executive dashboard (6-8 cards)
     *
     * @param  Scenario  $scenario
     * @param  array<string, mixed>  $headcountAnalysis
     * @param  array<string, mixed>  $financialAnalysis
     * @param  array<string, mixed>  $riskAnalysis
     * @return array<int, array<string, mixed>>
     */
    private function buildKPICards(
        Scenario $scenario,
        array $headcountAnalysis,
        array $financialAnalysis,
        array $riskAnalysis,
    ): array {
        $cards = [];

        // Card 1: Cost
        $cards[] = [
            'title' => 'Total Cost',
            'value' => number_format($financialAnalysis['total_cost'] / 1000000, 2), // In millions
            'unit' => '$M',
            'status' => $financialAnalysis['budget_variance_pct'] > 20 ? 'warning' : 'success',
            'trend' => $financialAnalysis['budget_variance'] > 0 ? '+' : '',
            'comparison' => $financialAnalysis['budget_variance_pct'] . '%',
            'icon' => '💰',
        ];

        // Card 2: ROI
        $cards[] = [
            'title' => 'Estimated ROI',
            'value' => round($financialAnalysis['roi'], 1),
            'unit' => '%',
            'status' => $financialAnalysis['roi'] > 20 ? 'success' : ($financialAnalysis['roi'] > 0 ? 'caution' : 'danger'),
            'trend' => $financialAnalysis['roi'] > 0 ? '↑' : '↓',
            'comparison' => 'vs baseline',
            'icon' => '📈',
        ];

        // Card 3: Headcount Change
        $cards[] = [
            'title' => 'Headcount Impact',
            'value' => abs($headcountAnalysis['headcount_delta']),
            'unit' => $headcountAnalysis['headcount_delta'] > 0 ? 'Hires' : 'Reductions',
            'status' => abs($headcountAnalysis['headcount_delta']) < 20 ? 'success' : 'warning',
            'trend' => $headcountAnalysis['headcount_delta'] > 0 ? '+' : '-',
            'comparison' => $headcountAnalysis['expected_coverage'] . '% coverage',
            'icon' => '👥',
        ];

        // Card 4: Timeline
        $cards[] = [
            'title' => 'Execution Timeline',
            'value' => $scenario->timeline_weeks ?? 12,
            'unit' => 'weeks',
            'status' => ($scenario->timeline_weeks ?? 12) < 8 ? 'warning' : 'success',
            'trend' => $headcountAnalysis['weeks_to_full_capacity'] . 'w to full',
            'comparison' => 'until mature',
            'icon' => '⏱️',
        ];

        // Card 5: Risk Level
        $cards[] = [
            'title' => 'Overall Risk',
            'value' => $riskAnalysis['overall_risk_score'],
            'unit' => '/10',
            'status' => match ($riskAnalysis['risk_level']) {
                'Critical' => 'danger',
                'High' => 'warning',
                'Medium' => 'caution',
                'Low', 'Minimal' => 'success',
                default => 'info',
            },
            'trend' => $riskAnalysis['risk_level'],
            'comparison' => count($riskAnalysis['individual_risks']) . ' risks',
            'icon' => '⚠️',
        ];

        // Card 6: Capability Improvement
        $capabilityImprovement = ($scenario->capability_gap_closure ?? 0) * 100;
        $cards[] = [
            'title' => 'Capability Gain',
            'value' => round($capabilityImprovement),
            'unit' => '%',
            'status' => $capabilityImprovement > 30 ? 'success' : 'caution',
            'trend' => $capabilityImprovement > 0 ? '↑' : '↓',
            'comparison' => 'skill coverage',
            'icon' => '🎓',
        ];

        // Card 7: Payback Period
        $paybackMonths = $financialAnalysis['payback_period_months'] ?? 0;
        $cards[] = [
            'title' => 'Payback Period',
            'value' => round($paybackMonths, 1),
            'unit' => 'months',
            'status' => $paybackMonths < 12 ? 'success' : ($paybackMonths < 24 ? 'caution' : 'warning'),
            'trend' => $paybackMonths > 0 ? '~' : 'n/a',
            'comparison' => 'to breakeven',
            'icon' => '⏰',
        ];

        // Card 8: Success Probability
        $successProbability = $this->calculateSuccessProbability($scenario, $headcountAnalysis, $riskAnalysis);
        $cards[] = [
            'title' => 'Success Probability',
            'value' => round($successProbability),
            'unit' => '%',
            'status' => $successProbability > 80 ? 'success' : ($successProbability > 60 ? 'caution' : 'warning'),
            'trend' => $successProbability > 70 ? '✓' : '?',
            'comparison' => 'likelihood',
            'icon' => '🎯',
        ];

        return $cards;
    }

    /**
     * Generate executive decision recommendation
     *
     * @param  Scenario  $scenario
     * @param  array<int, array<string, mixed>>  $kpis
     * @param  array<string, mixed>  $riskAnalysis
     * @return array<string, mixed>
     */
    private function generateDecisionRecommendation(
        Scenario $scenario,
        array $kpis,
        array $riskAnalysis,
    ): array {
        $decision = 'proceed'; // default
        $reasoning = [];
        $confidence = 90;

        // Extract metric statuses
        $roiCard = $kpis[1];
        $riskCard = $kpis[4];
        $successCard = $kpis[7];

        // Decision logic
        if ($roiCard['status'] === 'danger' || $successCard['status'] === 'warning') {
            $decision = 'revise';
            $reasoning[] = 'Financial viability concerns - ROI is negative or success probability is low';
            $confidence = 50;
        } elseif ($riskCard['status'] === 'danger') {
            $decision = 'revise';
            $reasoning[] = 'Critical risks identified - recommend risk mitigation planning';
            $confidence = 40;
        } elseif ($riskCard['status'] === 'warning') {
            $decision = 'proceed';
            $reasoning[] = 'Proceed with caution - monitor high-priority risks closely';
            $confidence = 70;
        } else {
            $decision = 'proceed';
            $reasoning[] = 'Strong business case with acceptable risk profile';
            $confidence = 90;
        }

        // Add additional context
        if ($successCard['value'] < 60) {
            $reasoning[] = 'Consider phased implementation to reduce execution risk';
        }

        if ($kpis[2]['status'] === 'warning') {
            $reasoning[] = 'Large headcount changes may require extended change management';
        }

        return [
            'recommendation' => $decision,
            'confidence' => $confidence,
            'reasoning' => $reasoning,
            'ready_to_activate' => $decision === 'proceed' && $confidence >= 80,
            'next_steps' => $this->generateNextSteps($decision, $kpis, $riskAnalysis),
        ];
    }

    /**
     * Build risk heatmap data (2x2 matrix: likelihood vs impact)
     *
     * @param  array<string, mixed>  $riskAnalysis
     * @return array<string, mixed>
     */
    private function buildRiskHeatmap(array $riskAnalysis): array
    {
        $risks = $riskAnalysis['individual_risks'] ?? [];
        $heatmap = [];

        foreach ($risks as $risk) {
            $riskType = $risk['type'] ?? 'unknown';
            $score = $risk['score'] ?? 0;

            // Map score (0-10) to likelihood (low/medium/high)
            $likelihood = $score < 3.5 ? 'low' : ($score < 6.5 ? 'medium' : 'high');

            // Estimate impact (could be enhanced with actual data)
            $impact = match ($riskType) {
                'execution_risk' => 'high',
                'financial_risk' => 'high',
                'talent_risk' => 'medium',
                'technology_risk' => 'medium',
                default => 'low',
            };

            $heatmap[] = [
                'type' => $riskType,
                'likelihood' => $likelihood,
                'impact' => $impact,
                'score' => $score,
                'description' => $risk['description'] ?? 'No description',
            ];
        }

        return $heatmap;
    }

    /**
     * Assess executive readiness (activation readiness)
     *
     * @param  Scenario  $scenario
     * @param  array<int, array<string, mixed>>  $kpis
     * @param  array<string, mixed>  $riskAnalysis
     * @return array<string, mixed>
     */
    private function assessExecutiveReadiness(
        Scenario $scenario,
        array $kpis,
        array $riskAnalysis,
    ): array {
        $checks = [];
        $passCount = 0;
        $totalChecks = 6;

        // Check 1: Budget approved
        $checks[] = [
            'name' => 'Budget Allocated',
            'status' => ($scenario->budget_approved_at ?? false) ? 'pass' : 'pending',
            'description' => 'CFO sign-off on budget',
        ];
        if ($scenario->budget_approved_at) {
            $passCount++;
        }

        // Check 2: Stakeholder alignment
        $checks[] = [
            'name' => 'Stakeholder Alignment',
            'status' => ($scenario->stakeholder_signoff_count ?? 0) > 2 ? 'pass' : 'pending',
            'description' => 'Required approvers signed off',
        ];
        if (($scenario->stakeholder_signoff_count ?? 0) > 2) {
            $passCount++;
        }

        // Check 3: Risk mitigation plan
        $checks[] = [
            'name' => 'Risk Mitigation Plan',
            'status' => count($riskAnalysis['mitigation_strategies'] ?? []) > 0 ? 'pass' : 'pending',
            'description' => 'Mitigation strategies defined for critical risks',
        ];
        if (count($riskAnalysis['mitigation_strategies'] ?? []) > 0) {
            $passCount++;
        }

        // Check 4: Resource allocation
        $checks[] = [
            'name' => 'Resources Allocated',
            'status' => ($scenario->resources_allocated_at ?? false) ? 'pass' : 'pending',
            'description' => 'Team assigned and committed',
        ];
        if ($scenario->resources_allocated_at) {
            $passCount++;
        }

        // Check 5: Executive sponsorship
        $checks[] = [
            'name' => 'Executive Sponsorship',
            'status' => ($scenario->sponsor_id ?? false) ? 'pass' : 'pending',
            'description' => 'Executive sponsor assigned',
        ];
        if ($scenario->sponsor_id) {
            $passCount++;
        }

        // Check 6: Communication plan
        $checks[] = [
            'name' => 'Communication Plan',
            'status' => ($scenario->communication_plan_created_at ?? false) ? 'pass' : 'pending',
            'description' => 'Internal & external comms planned',
        ];
        if ($scenario->communication_plan_created_at) {
            $passCount++;
        }

        $readinessPercentage = ($passCount / $totalChecks) * 100;

        return [
            'checks' => $checks,
            'ready_percentage' => round($readinessPercentage),
            'activation_ready' => $readinessPercentage >= 80,
            'missing_items' => array_filter($checks, fn ($c) => $c['status'] === 'pending'),
        ];
    }

    /**
     * Calculate success probability based on various factors
     *
     * @param  Scenario  $scenario
     * @param  array<string, mixed>  $headcountAnalysis
     * @param  array<string, mixed>  $riskAnalysis
     * @return float
     */
    private function calculateSuccessProbability(
        Scenario $scenario,
        array $headcountAnalysis,
        array $riskAnalysis,
    ): float {
        $baselineProbability = 75; // Start at 75%

        // Factor 1: Risk score (lower is better)
        $riskFactor = 100 - min($riskAnalysis['overall_risk_score'] * 10, 40); // Max 40% reduction
        $baselineProbability = $baselineProbability * ($riskFactor / 100);

        // Factor 2: Timeline pressure (tighter = lower probability)
        $timelineFactor = match (true) {
            ($scenario->timeline_weeks ?? 12) < 4 => 0.7,
            ($scenario->timeline_weeks ?? 12) < 8 => 0.85,
            ($scenario->timeline_weeks ?? 12) < 12 => 0.95,
            default => 1.0,
        };
        $baselineProbability = $baselineProbability * $timelineFactor;

        // Factor 3: Headcount volatility (large changes = lower probability)
        $headcountVolatility = match (true) {
            abs($headcountAnalysis['headcount_delta']) > 50 => 0.8,
            abs($headcountAnalysis['headcount_delta']) > 30 => 0.9,
            default => 1.0,
        };
        $baselineProbability = $baselineProbability * $headcountVolatility;

        // Factor 4: Confidence score from analysis (if available)
        $analysisConfidence = $headcountAnalysis['confidence_score'] ?? 0.85;
        $baselineProbability = $baselineProbability * $analysisConfidence;

        return min(max($baselineProbability, 10), 99); // Clamp 10-99%
    }

    /**
     * Generate next steps based on recommendation
     *
     * @param  string  $decision
     * @param  array<int, array<string, mixed>>  $kpis
     * @param  array<string, mixed>  $riskAnalysis
     * @return array<int, string>
     */
    private function generateNextSteps(
        string $decision,
        array $kpis,
        array $riskAnalysis,
    ): array {
        $steps = [];

        if ($decision === 'proceed') {
            $steps[] = '1. Schedule executive steering committee review';
            $steps[] = '2. Finalize detailed implementation roadmap';
            $steps[] = '3. Confirm budget and resource allocation';
            $steps[] = '4. Initiate stakeholder communication plan';
            $steps[] = '5. Establish governance and escalation procedures';
        } elseif ($decision === 'revise') {
            $steps[] = '1. Schedule scenario review session with finance and strategy teams';
            $steps[] = '2. Identify scenario variables for optimization';
            $steps[] = '3. Explore phased implementation approach';
            $steps[] = '4. Re-run what-if analysis with revised parameters';
            $steps[] = '5. Resubmit with updated recommendations';
        }

        return $steps;
    }
}
