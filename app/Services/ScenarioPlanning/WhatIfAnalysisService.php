<?php

namespace App\Services\ScenarioPlanning;

use App\Models\Scenario;
use Illuminate\Support\Facades\Log;

/**
 * WhatIfAnalysisService — Analyze impact of scenario changes
 *
 * Provides comprehensive what-if analysis for scenario planning:
 * - Headcount impact analysis (hiring, reduction, mobility)
 * - Financial impact calculation (costs, savings, ROI)
 * - Risk impact assessment (identify risks from changes)
 * - Baseline comparison (deltas against current state)
 * - Outcome prediction (success probability, recommendations)
 * - Sensitivity analysis (impact by variable/parameter)
 */
class WhatIfAnalysisService
{
    /**
     * Analyze the impact of headcount changes on scenario
     *
     * @param  int  $scenarioId
     * @param  array<string, mixed>  $changes  Changes: {headcount_delta, role_changes, turnover_rate}
     * @return array<string, mixed>
     */
    public function analyzeHeadcountImpact(int $scenarioId, array $changes): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        $currentHeadcount = $scenario->expected_coverage ?? 100; // % of roles covered
        $headcountDelta = $changes['headcount_delta'] ?? 0;
        $turnoverRate = $changes['turnover_rate'] ?? 0.1; // 10% default
        $timelineWeeks = $changes['timeline_weeks'] ?? $scenario->timeline_weeks ?? 12;

        // Calculate new headcount after changes
        $newHeadcount = max(0, $currentHeadcount + $headcountDelta);
        $expectedCoverage = min(100, ($newHeadcount / (100 + abs($headcountDelta))) * 100);

        // Estimate hiring/reduction needs
        $hiringNeeds = $headcountDelta > 0 ? $headcountDelta : 0;
        $reductionNeeds = abs(min(0, $headcountDelta));

        // Calculate timeline impact (hiring/onboarding time)
        $weeks_to_full_capacity = $hiringNeeds > 0 ? ceil($hiringNeeds / 5) + 4 : 0; // 5 hires/week + 4 weeks onboarding
        $accelerated_timeline = max(0, $timelineWeeks - ($reductionNeeds > 0 ? 0 : 2)); // Reduction speeds up

        // Risk factors
        $risks = [];
        if ($hiringNeeds > 10) {
            $risks[] = [
                'type' => 'hiring_capacity',
                'severity' => 'high',
                'description' => "Need to hire {$hiringNeeds} people - may exceed market capacity",
                'mitigation' => 'Consider external agencies or training existing staff',
            ];
        }
        if ($turnoverRate > 0.2) {
            $turnoverPct = round($turnoverRate * 100, 1);
            $risks[] = [
                'type' => 'attrition_risk',
                'severity' => 'medium',
                'description' => "High turnover rate ({$turnoverPct}%) may impact execution",
                'mitigation' => 'Implement retention programs',
            ];
        }
        if ($accelerated_timeline < 4) {
            $risks[] = [
                'type' => 'timeline_pressure',
                'severity' => 'high',
                'description' => 'Timeline is very aggressive',
                'mitigation' => 'Reduce scope or extend timeline',
            ];
        }

        return [
            'current_headcount' => $currentHeadcount,
            'new_headcount' => $newHeadcount,
            'headcount_delta' => $headcountDelta,
            'expected_coverage' => round($expectedCoverage, 2),
            'hiring_needs' => $hiringNeeds,
            'reduction_needs' => $reductionNeeds,
            'weeks_to_full_capacity' => $weeks_to_full_capacity,
            'turnover_rate' => $turnoverRate,
            'impact_on_timeline_weeks' => round($accelerated_timeline),
            'risks' => $risks,
            'confidence_score' => $this->calculateConfidenceScore($hiringNeeds, $turnoverRate, $accelerated_timeline),
        ];
    }

    /**
     * Analyze financial impact of scenario changes
     *
     * @param  int  $scenarioId
     * @param  array<string, mixed>  $changes  Changes: {cost_per_hire, training_hours, timeline_weeks}
     * @return array<string, mixed>
     */
    public function analyzeFinancialImpact(int $scenarioId, array $changes): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        // Get current scenario financial baseline
        $currentBudget = $scenario->budget ?? 0;
        $currentTimeline = $scenario->timeline_weeks ?? 12;

        // Cost parameters
        $costPerHire = $changes['cost_per_hire'] ?? 80000; // Average salary
        $costPerTrainingHour = $changes['cost_per_training_hour'] ?? 150;
        $trainingHourPerPerson = $changes['training_hours'] ?? 40;
        $infrastructureCostPerWeek = $changes['infrastructure_cost_per_week'] ?? 5000;

        // Headcount impact (assuming from previous analysis)
        $headcountChanges = $changes['headcount_delta'] ?? 0;
        $trainingChanges = $changes['training_changes'] ?? 0;

        // Calculate costs
        $hiring_cost = max(0, $headcountChanges) * $costPerHire;
        $training_cost = $trainingChanges * $trainingHourPerPerson * $costPerTrainingHour;
        $infrastructure_cost = ($changes['timeline_weeks'] ?? $currentTimeline) * $infrastructureCostPerWeek;
        $severance_cost = max(0, -$headcountChanges) * ($costPerHire * 0.15); // 15% of salary
        $contingency = ($hiring_cost + $training_cost + $infrastructure_cost) * 0.1; // 10% buffer

        $total_cost = $hiring_cost + $training_cost + $infrastructure_cost + $severance_cost + $contingency;

        // Calculate savings
        $salary_savings = max(0, -$headcountChanges) * $costPerHire * 0.25; // Reduced salary burden after severance
        $automation_savings = 0; // Will be calculated based on automation_potential in changes
        $efficiency_savings = $changes['efficiency_improvement'] ?? 0;

        $total_savings = $salary_savings + $automation_savings + $efficiency_savings;

        // Calculate ROI
        $net_impact = $total_savings - $total_cost;
        $roi = $total_cost > 0 ? (($net_impact / $total_cost) * 100) : 0;

        // Budget variance
        $budget_variance = $currentBudget - $total_cost;
        $budget_variance_pct = $currentBudget > 0 ? (($budget_variance / $currentBudget) * 100) : 0;

        return [
            'current_budget' => $currentBudget,
            'total_cost' => round($total_cost, 2),
            'cost_breakdown' => [
                'hiring_cost' => round($hiring_cost, 2),
                'training_cost' => round($training_cost, 2),
                'infrastructure_cost' => round($infrastructure_cost, 2),
                'severance_cost' => round($severance_cost, 2),
                'contingency' => round($contingency, 2),
            ],
            'total_savings' => round($total_savings, 2),
            'savings_breakdown' => [
                'salary_savings' => round($salary_savings, 2),
                'automation_savings' => round($automation_savings, 2),
                'efficiency_savings' => round($efficiency_savings, 2),
            ],
            'net_impact' => round($net_impact, 2),
            'roi' => round($roi, 2),
            'budget_variance' => round($budget_variance, 2),
            'budget_variance_pct' => round($budget_variance_pct, 2),
            'payback_period_months' => $this->calculatePaybackPeriod($total_cost, $total_savings),
        ];
    }

    /**
     * Analyze risk impact of scenario changes
     *
     * @param  int  $scenarioId
     * @param  array<string, mixed>  $changes  Changes to analyze risks for
     * @return array<string, mixed>
     */
    public function analyzeRiskImpact(int $scenarioId, array $changes): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        $risks = [];
        $overall_risk_score = 0;

        // Risk 1: Execution Risk
        $timeline_weeks = $changes['timeline_weeks'] ?? $scenario->timeline_weeks ?? 12;
        $complexity_factor = $changes['complexity'] ?? 1; // 0-2 scale

        if ($timeline_weeks < 4) {
            $execution_risk = 8 + ($complexity_factor * 2); // 8-12 range
        } elseif ($timeline_weeks < 12) {
            $execution_risk = 5 + ($complexity_factor * 1.5); // 5-8.5 range
        } else {
            $execution_risk = 2 + ($complexity_factor * 1.5); // 2-5.5 range
        }
        $risks[] = [
            'type' => 'execution_risk',
            'score' => round(min(10, $execution_risk), 2),
            'factors' => ['timeline_pressure' => $timeline_weeks < 8, 'complexity' => $complexity_factor > 1],
            'description' => "Risk of not delivering scenario within {$timeline_weeks} weeks",
        ];

        // Risk 2: Talent/HR Risk
        $headcount_delta = $changes['headcount_delta'] ?? 0;
        $turnover_rate = $changes['turnover_rate'] ?? 0.1;

        $talent_risk = 3;
        if (abs($headcount_delta) > 20) {
            $talent_risk += 3; // Large reorganization
        }
        if ($turnover_rate > 0.15) {
            $talent_risk += 2; // High attrition
        }
        $risks[] = [
            'type' => 'talent_risk',
            'score' => round(min(10, $talent_risk), 2),
            'factors' => ['headcount_change' => abs($headcount_delta) > 20, 'high_turnover' => $turnover_rate > 0.15],
            'description' => "Risk of losing key talent or failing to hire needed skills",
        ];

        // Risk 3: Financial Risk
        $total_cost = $changes['total_cost'] ?? 0;
        $current_budget = $scenario->budget ?? 0;

        $financial_risk = 2;
        if ($total_cost > $current_budget * 1.2) {
            $financial_risk += 4; // Over budget
        }
        $risks[] = [
            'type' => 'financial_risk',
            'score' => round(min(10, $financial_risk), 2),
            'factors' => ['over_budget' => $total_cost > $current_budget * 1.2],
            'description' => "Risk of scenario exceeding approved budget",
        ];

        // Risk 4: Technology/Integration Risk
        $tech_changes = $changes['technology_changes'] ?? 0;
        $tech_risk = max(1, $tech_changes * 2); // 0-10 scale
        $risks[] = [
            'type' => 'technology_risk',
            'score' => round(min(10, $tech_risk), 2),
            'factors' => ['integration_points' => $tech_changes],
            'description' => 'Risk of technology integration or compatibility issues',
        ];

        // Calculate overall risk score as weighted average
        $weights = [
            'execution_risk' => 0.3,
            'talent_risk' => 0.25,
            'financial_risk' => 0.25,
            'technology_risk' => 0.2,
        ];

        foreach ($risks as $risk) {
            $overall_risk_score += $risk['score'] * ($weights[$risk['type']] ?? 0);
        }

        // Risk Level Classification
        $risk_level = match (true) {
            $overall_risk_score >= 8 => 'Critical',
            $overall_risk_score >= 6 => 'High',
            $overall_risk_score >= 4 => 'Medium',
            $overall_risk_score >= 2 => 'Low',
            default => 'Minimal',
        };

        // Mitigation strategies
        $mitigations = $this->generateMitigationStrategies($risks, $changes);

        return [
            'overall_risk_score' => round($overall_risk_score, 2),
            'risk_level' => $risk_level,
            'individual_risks' => $risks,
            'mitigation_strategies' => $mitigations,
            'recommendation' => $this->getRiskRecommendation($overall_risk_score),
        ];
    }

    /**
     * Compare scenario with baseline to show deltas
     *
     * @param  int  $scenarioId
     * @param  int  $baselineScenarioId
     * @return array<string, mixed>
     */
    public function compareScenariosWithBaseline(int $scenarioId, int $baselineScenarioId = null): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        // If no baseline specified, assume current state
        $baseline = $baselineScenarioId ? Scenario::findOrFail($baselineScenarioId) : $this->getCurrentBaseline($scenario);

        $deltas = [
            'headcount' => ($scenario->expected_coverage ?? 0) - ($baseline['headcount'] ?? 0),
            'budget' => ($scenario->budget ?? 0) - ($baseline['budget'] ?? 0),
            'timeline' => ($scenario->timeline_weeks ?? 0) - ($baseline['timeline'] ?? 0),
            'risk_score' => ($scenario->risk_score ?? 0) - ($baseline['risk_score'] ?? 0),
            'roi_improvement' => ($scenario->estimated_roi ?? 0) - ($baseline['roi'] ?? 0),
        ];

        return [
            'scenario_id' => $scenarioId,
            'baseline_scenario_id' => $baselineScenarioId ?? 'current_state',
            'deltas' => $deltas,
            'percentage_changes' => [
                'headcount_pct' => $baseline['headcount'] > 0 ? round(($deltas['headcount'] / $baseline['headcount']) * 100, 2) : 0,
                'budget_pct' => $baseline['budget'] > 0 ? round(($deltas['budget'] / $baseline['budget']) * 100, 2) : 0,
                'timeline_pct' => $baseline['timeline'] > 0 ? round(($deltas['timeline'] / $baseline['timeline']) * 100, 2) : 0,
            ],
            'improvements' => $this->identifyImprovements($deltas),
            'concerns' => $this->identifyConcerns($deltas),
        ];
    }

    /**
     * Predict outcomes and success probability
     *
     * @param  int  $scenarioId
     * @param  array<string, mixed>  $executionParams  Execution confidence factors
     * @return array<string, mixed>
     */
    public function predictOutcomes(int $scenarioId, array $executionParams = []): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        // Extract confidence factors
        $team_readiness = $executionParams['team_readiness'] ?? 0.7; // 0-1 scale
        $budget_confidence = $executionParams['budget_confidence'] ?? 0.8;
        $stakeholder_buy_in = $executionParams['stakeholder_buy_in'] ?? 0.75;
        $external_dependencies = $executionParams['external_dependencies'] ?? 0.6;
        $change_management_readiness = $executionParams['change_management_readiness'] ?? 0.65;

        // Calculate success probability (weighted average)
        $success_probability = (
            $team_readiness * 0.25 +
            $budget_confidence * 0.2 +
            $stakeholder_buy_in * 0.25 +
            $external_dependencies * 0.15 +
            $change_management_readiness * 0.15
        );

        // Predict key outcomes
        $predicted_headcount = $scenario->expected_coverage ?? 80;
        $predicted_cost = ($scenario->budget ?? 0) * (1 + (random_int(-10, 20) / 100)); // ±10-20% variance
        $predicted_timeline = intval(($scenario->timeline_weeks ?? 12) * (1 + (random_int(-15, 25) / 100))); // ±15-25% variance

        // Estimate ROI based on success probability
        $base_roi = $scenario->estimated_roi ?? 0;
        $realistic_roi = $base_roi * $success_probability;
        $optimistic_roi = $base_roi * ($success_probability + 0.1);
        $pessimistic_roi = $base_roi * max(0, $success_probability - 0.2);

        // Recommendations
        $recommendations = [];
        if ($team_readiness < 0.7) {
            $recommendations[] = 'Enhance team training and skill development';
        }
        if ($budget_confidence < 0.75) {
            $recommendations[] = 'Review budget allocation and contingencies';
        }
        if ($stakeholder_buy_in < 0.75) {
            $recommendations[] = 'Increase stakeholder engagement and communication';
        }
        if ($external_dependencies < 0.75) {
            $recommendations[] = 'Establish clear dependencies and contingency plans';
        }
        if ($change_management_readiness < 0.7) {
            $recommendations[] = 'Strengthen change management strategy';
        }

        return [
            'success_probability' => round($success_probability * 100, 2),
            'confidence_factors' => [
                'team_readiness' => round($team_readiness * 100, 2),
                'budget_confidence' => round($budget_confidence * 100, 2),
                'stakeholder_buy_in' => round($stakeholder_buy_in * 100, 2),
                'external_dependencies' => round($external_dependencies * 100, 2),
                'change_management_readiness' => round($change_management_readiness * 100, 2),
            ],
            'predicted_outcomes' => [
                'expected_headcount_coverage' => round($predicted_headcount, 2),
                'realistic_cost' => round($predicted_cost, 2),
                'realistic_timeline_weeks' => $predicted_timeline,
            ],
            'roi_scenarios' => [
                'optimistic_roi' => round($optimistic_roi, 2),
                'realistic_roi' => round($realistic_roi, 2),
                'pessimistic_roi' => round($pessimistic_roi, 2),
            ],
            'success_level' => match (true) {
                $success_probability >= 0.8 => 'Highly Likely',
                $success_probability >= 0.65 => 'Likely',
                $success_probability >= 0.5 => 'Possible',
                $success_probability >= 0.35 => 'Challenging',
                default => 'High Risk',
            },
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Perform sensitivity analysis - see how outcome changes with variable changes
     *
     * @param  int  $scenarioId
     * @param  array<string, mixed>  $variables  Variables to test: {budget_range, timeline_range, headcount_range}
     * @return array<string, mixed>
     */
    public function performSensitivityAnalysis(int $scenarioId, array $variables = []): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        // Define sensitivity ranges if not provided
        $budget_adjustments = $variables['budget_adjustments'] ?? [-20, -10, 0, 10, 20]; // percentages
        $headcount_adjustments = $variables['headcount_adjustments'] ?? [-15, -10, 0, 10, 15];
        $timeline_adjustments = $variables['timeline_adjustments'] ?? [-25, -15, 0, 15, 25];

        $base_budget = $scenario->budget ?? 100000;
        $base_headcount = $scenario->expected_coverage ?? 100;
        $base_timeline = $scenario->timeline_weeks ?? 12;

        // Sensitivity analysis on each variable
        $budget_sensitivity = [];
        foreach ($budget_adjustments as $adj) {
            $adjusted = $base_budget * (1 + ($adj / 100));
            $impact = $this->estimateImpactOfBudgetChange($adjusted, $scenario);
            $budget_sensitivity[] = [
                'adjustment_pct' => $adj,
                'adjusted_budget' => round($adjusted, 2),
                'impact_on_roi' => round($impact['roi_change'], 2),
                'impact_on_timeline' => $impact['timeline_change'],
                'feasibility' => $impact['feasible'] ? 'Feasible' : 'Not Feasible',
            ];
        }

        $headcount_sensitivity = [];
        foreach ($headcount_adjustments as $adj) {
            $adjusted = $base_headcount + $adj;
            $impact = $this->estimateImpactOfHeadcountChange($adjusted, $scenario);
            $headcount_sensitivity[] = [
                'adjustment' => $adj,
                'adjusted_headcount' => round($adjusted, 2),
                'impact_on_timeline' => round($impact['timeline_change'], 2),
                'impact_on_cost' => round($impact['cost_change'], 2),
                'risk_level' => $impact['risk_level'],
            ];
        }

        $timeline_sensitivity = [];
        foreach ($timeline_adjustments as $adj) {
            $adjusted = max(1, $base_timeline + ($base_timeline * ($adj / 100)));
            $impact = $this->estimateImpactOfTimelineChange($adjusted, $scenario);
            $timeline_sensitivity[] = [
                'adjustment_pct' => $adj,
                'adjusted_timeline_weeks' => round($adjusted, 2),
                'impact_on_cost' => round($impact['cost_change'], 2),
                'impact_on_quality' => $impact['quality_change'],
                'risk_level' => $impact['risk_level'],
            ];
        }

        // Identify critical variables (those with highest impact)
        $critical_variables = $this->identifyCriticalVariables([
            'budget' => $budget_sensitivity,
            'headcount' => $headcount_sensitivity,
            'timeline' => $timeline_sensitivity,
        ]);

        return [
            'scenario_id' => $scenarioId,
            'base_parameters' => [
                'budget' => round($base_budget, 2),
                'headcount_coverage' => round($base_headcount, 2),
                'timeline_weeks' => $base_timeline,
            ],
            'budget_sensitivity' => $budget_sensitivity,
            'headcount_sensitivity' => $headcount_sensitivity,
            'timeline_sensitivity' => $timeline_sensitivity,
            'critical_variables' => $critical_variables,
            'recommendations' => $this->generateSensitivityRecommendations($critical_variables),
        ];
    }

    // ================== HELPER METHODS ==================

    private function calculateConfidenceScore(int $hiringNeeds, float $turnoverRate, int $timeline): float
    {
        $score = 100;

        // Deduct for hiring challenges
        if ($hiringNeeds > 20) {
            $score -= 20;
        } elseif ($hiringNeeds > 10) {
            $score -= 10;
        }

        // Deduct for high turnover
        if ($turnoverRate > 0.2) {
            $score -= 15;
        } elseif ($turnoverRate > 0.15) {
            $score -= 10;
        }

        // Deduct for aggressive timeline
        if ($timeline < 4) {
            $score -= 25;
        } elseif ($timeline < 8) {
            $score -= 15;
        }

        return max(0, min(100, $score));
    }

    private function calculatePaybackPeriod(float $totalCost, float $totalSavings): float
    {
        if ($totalSavings <= 0) {
            return 999; // No payback
        }

        $monthlySavings = $totalSavings / 12;

        return $totalCost / $monthlySavings;
    }

    private function getCurrentBaseline(Scenario $scenario): array
    {
        return [
            'headcount' => 100,
            'budget' => $scenario->budget ?? 500000,
            'timeline' => 12,
            'risk_score' => 5,
            'roi' => 1.5,
        ];
    }

    private function identifyImprovements(array $deltas): array
    {
        $improvements = [];

        if ($deltas['headcount'] > 5) {
            $improvements[] = 'Improved headcount coverage';
        }
        if ($deltas['budget'] < -50000) {
            $improvements[] = 'Reduced budget requirement';
        }
        if ($deltas['timeline'] < 0) {
            $improvements[] = 'Accelerated timeline';
        }
        if ($deltas['roi_improvement'] > 0.5) {
            $improvements[] = 'Better return on investment';
        }

        return $improvements;
    }

    private function identifyConcerns(array $deltas): array
    {
        $concerns = [];

        if ($deltas['headcount'] < -10) {
            $concerns[] = 'Significant headcount reduction may impact execution';
        }
        if ($deltas['budget'] > 200000) {
            $concerns[] = 'Budget significantly increased';
        }
        if ($deltas['timeline'] > 8) {
            $concerns[] = 'Extended timeline may delay benefits';
        }

        return $concerns;
    }

    private function generateMitigationStrategies(array $risks, array $changes): array
    {
        $strategies = [];

        foreach ($risks as $risk) {
            if ($risk['type'] === 'execution_risk' && $risk['score'] > 6) {
                $strategies[] = [
                    'risk_type' => 'execution_risk',
                    'strategy' => 'Break scenario into smaller phases with milestones',
                    'priority' => 'High',
                ];
            }
            if ($risk['type'] === 'talent_risk' && $risk['score'] > 6) {
                $strategies[] = [
                    'risk_type' => 'talent_risk',
                    'strategy' => 'Hire external consultants or use agencies for staffing',
                    'priority' => 'High',
                ];
            }
            if ($risk['type'] === 'financial_risk' && $risk['score'] > 6) {
                $strategies[] = [
                    'risk_type' => 'financial_risk',
                    'strategy' => 'Negotiate phased payment terms or seek additional funding',
                    'priority' => 'High',
                ];
            }
        }

        return $strategies;
    }

    private function getRiskRecommendation(float $riskScore): string
    {
        return match (true) {
            $riskScore >= 8 => 'DO NOT PROCEED - Risks are critical. Major revisions required.',
            $riskScore >= 6 => 'PROCEED WITH CAUTION - Implement strong mitigation strategies.',
            $riskScore >= 4 => 'PROCEED - Monitor key risk indicators closely.',
            $riskScore >= 2 => 'PROCEED - Standard risk management sufficient.',
            default => 'PROCEED - Minimal risks identified.',
        };
    }

    private function estimateImpactOfBudgetChange(float $newBudget, Scenario $scenario): array
    {
        $originalBudget = $scenario->budget ?? 500000;
        $budgetChange = $newBudget - $originalBudget;

        return [
            'roi_change' => ($budgetChange / $originalBudget) * -2, // Negative relationship
            'timeline_change' => $budgetChange > 0 ? -1 : 1, // More budget = faster
            'feasible' => $newBudget > ($originalBudget * 0.5), // Must have 50%+ min budget
        ];
    }

    private function estimateImpactOfHeadcountChange(float $newHeadcount, Scenario $scenario): array
    {
        $originalHeadcount = $scenario->expected_coverage ?? 100;
        $change = $newHeadcount - $originalHeadcount;

        return [
            'timeline_change' => $change > 0 ? -2 : 1,
            'cost_change' => $change * 80000,
            'risk_level' => match (true) {
                abs($change) > 20 => 'High',
                abs($change) > 10 => 'Medium',
                default => 'Low',
            },
        ];
    }

    private function estimateImpactOfTimelineChange(float $newTimeline, Scenario $scenario): array
    {
        $originalTimeline = $scenario->timeline_weeks ?? 12;

        return [
            'cost_change' => ($newTimeline - $originalTimeline) * 5000, // Infrastructure costs
            'quality_change' => $newTimeline < $originalTimeline ? -0.2 : 0.1,
            'risk_level' => match (true) {
                $newTimeline < 4 => 'Critical',
                $newTimeline < 8 => 'High',
                $newTimeline < 16 => 'Medium',
                default => 'Low',
            },
        ];
    }

    private function identifyCriticalVariables(array $sensitivities): array
    {
        $critical = [];

        // Find variables with highest impact variance
        foreach ($sensitivities as $variable => $values) {
            $impacts = array_column($values, $variable === 'budget' ? 'impact_on_roi' : 'impact_on_cost');
            $variance = count($impacts) > 0 ? abs(max($impacts) - min($impacts)) : 0;

            if ($variance > 100000 || (isset($impacts[0]) && abs($impacts[0]) > 50000)) {
                $critical[] = $variable;
            }
        }

        return $critical;
    }

    private function generateSensitivityRecommendations(array $criticalVariables): array
    {
        $recommendations = [];

        foreach ($criticalVariables as $variable) {
            $recommendations[] = match ($variable) {
                'budget' => 'Budget is a critical variable - ensure firm budget commitments before proceeding',
                'headcount' => 'Headcount is critical - build flexibility into staffing plans',
                'timeline' => 'Timeline is critical - consider risk of aggressive schedule',
                default => 'Monitor this variable closely',
            };
        }

        return $recommendations;
    }
}
