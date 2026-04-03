<?php

namespace App\Services;

use App\Models\ChangeSet;
use App\Models\IntelligenceMetricAggregate;
use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceActionPlan;
use App\Models\WorkforceDemandLine;
use Illuminate\Support\Collection;

class WorkforcePlanningService
{
    /** @return array<string, mixed> */
    public function getEnterprisePlanningSummary(int $organizationId, int $periodDays = 30): array
    {
        $windowStart = now()->subDays(max(1, min($periodDays, 90)));

        $scenarioQuery = Scenario::query()->where('organization_id', $organizationId);
        $changeSetQuery = ChangeSet::query()->where('organization_id', $organizationId);
        $actionPlanQuery = WorkforceActionPlan::query()->where('organization_id', $organizationId);
        $demandLineQuery = WorkforceDemandLine::query()->where('organization_id', $organizationId);

        $demandLines = (clone $demandLineQuery)->get();
        $requiredHh = (float) $demandLines->sum(function (WorkforceDemandLine $line): float {
            return (float) $line->required_hh;
        });
        $effectiveHh = (float) $demandLines->sum(function (WorkforceDemandLine $line): float {
            return (float) $line->effective_hh;
        });
        $coveragePct = $requiredHh > 0 ? round(($effectiveHh / $requiredHh) * 100, 2) : 0.0;

        $totalActions = (clone $actionPlanQuery)->count();
        $completedActions = (clone $actionPlanQuery)->where('status', 'completed')->count();
        $completionPct = $totalActions > 0 ? round(($completedActions / $totalActions) * 100, 2) : 0.0;

        $telemetryQuery = IntelligenceMetricAggregate::query()
            ->where('organization_id', $organizationId)
            ->where('date_key', '>=', $windowStart->toDateString())
            ->where('source_type', 'like', 'workforce%');

        $avgSuccessRate = (clone $telemetryQuery)->avg('success_rate');
        $avgLatencyMs = (clone $telemetryQuery)->avg('avg_duration_ms');

        return [
            'window_days' => max(1, min($periodDays, 90)),
            'window_start' => $windowStart->toDateString(),
            'window_end' => now()->toDateString(),
            'portfolio' => [
                'scenarios_total' => (clone $scenarioQuery)->count(),
                'scenarios_active_or_approved' => (clone $scenarioQuery)
                    ->whereIn('status', ['active', 'approved'])
                    ->count(),
                'scenarios_in_governance_flow' => (clone $scenarioQuery)
                    ->whereIn('status', ['in_review', 'approved'])
                    ->count(),
            ],
            'workforce_execution' => [
                'demand_lines_total' => (clone $demandLineQuery)->count(),
                'required_hh_total' => round($requiredHh, 2),
                'effective_hh_total' => round($effectiveHh, 2),
                'coverage_pct' => $coveragePct,
                'action_plans_total' => $totalActions,
                'action_plans_completed' => $completedActions,
                'action_completion_pct' => $completionPct,
            ],
            'governance' => [
                'changesets_total' => (clone $changeSetQuery)->count(),
                'changesets_pending' => (clone $changeSetQuery)
                    ->whereIn('status', ['draft', 'pending', 'in_review'])
                    ->count(),
                'changesets_applied' => (clone $changeSetQuery)
                    ->where('status', 'applied')
                    ->count(),
            ],
            'operational_health' => [
                'success_rate_pct' => $avgSuccessRate !== null ? round(((float) $avgSuccessRate) * 100, 2) : null,
                'error_rate_pct' => $avgSuccessRate !== null ? round((1 - (float) $avgSuccessRate) * 100, 2) : null,
                'avg_latency_ms' => $avgLatencyMs !== null ? round((float) $avgLatencyMs, 2) : null,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getThresholds(?int $organizationId = null): array
    {
        $defaults = [
            'coverage' => [
                'success_min' => (float) config('workforce_planning.thresholds.coverage.success_min', 100),
                'warning_min' => (float) config('workforce_planning.thresholds.coverage.warning_min', 90),
            ],
            'gap' => [
                'warning_max_pct' => (float) config('workforce_planning.thresholds.gap.warning_max_pct', 10),
            ],
        ];

        if (! $organizationId) {
            return $defaults;
        }

        $organization = Organization::query()->find($organizationId);
        $overrides = is_array($organization?->workforce_thresholds) ? $organization->workforce_thresholds : [];

        return array_replace_recursive($defaults, $overrides);
    }

    public function getRecommendations(Scenario $scenario): Collection
    {
        $scenarioId = (int) $scenario->id;

        // Dummy implementation for the sake of MVP (as guided in the documentation)
        return collect([
            [
                'scenario_id' => $scenarioId,
                'role' => 'Analista de Datos',
                'demand' => 5,
                'internal_supply' => 3,
                'strategy_type' => 'BUILD',
                'action' => 'Movilidad interna (2 FTE)',
            ],
            [
                'scenario_id' => $scenarioId,
                'role' => 'Analista de Datos',
                'demand' => 5,
                'internal_supply' => 3,
                'strategy_type' => 'BORROW',
                'action' => 'Contratación externa (1 FTE)',
            ],
            [
                'scenario_id' => $scenarioId,
                'role' => 'Analista de Datos',
                'demand' => 5,
                'internal_supply' => 3,
                'strategy_type' => 'BUY',
                'action' => 'Reclutamiento directo (2 FTE)',
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getBaselineSummary(int $organizationId): array
    {
        $baselineScenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->orderByRaw("CASE WHEN status IN ('active', 'approved') THEN 0 ELSE 1 END")
            ->latest('id')
            ->first();

        if (! $baselineScenario) {
            return [
                'planning_context' => 'baseline',
                'scenario_id' => null,
                'required_hh' => 0.0,
                'effective_hh' => 0.0,
                'coverage_pct' => 0.0,
                'gap_hh' => 0.0,
                'gap_fte' => 0.0,
            ];
        }

        return $this->buildMetricsSnapshot($baselineScenario, 'baseline');
    }

    /**
     * @return array<string, mixed>
     */
    public function compareScenarioWithBaseline(Scenario $scenario, int $organizationId): array
    {
        $scenarioSnapshot = $this->buildMetricsSnapshot($scenario, 'scenario');

        $baselineScenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->where('id', '!=', $scenario->id)
            ->orderByRaw("CASE WHEN status IN ('active', 'approved') THEN 0 ELSE 1 END")
            ->latest('id')
            ->first();

        $baselineSnapshot = $baselineScenario
            ? $this->buildMetricsSnapshot($baselineScenario, 'baseline')
            : [
                'planning_context' => 'baseline',
                'scenario_id' => null,
                'required_hh' => 0.0,
                'effective_hh' => 0.0,
                'coverage_pct' => 0.0,
                'gap_hh' => 0.0,
                'gap_fte' => 0.0,
            ];

        return [
            'scenario' => $scenarioSnapshot,
            'baseline' => $baselineSnapshot,
            'delta' => [
                'delta_gap_hh' => round($scenarioSnapshot['gap_hh'] - $baselineSnapshot['gap_hh'], 2),
                'delta_gap_fte' => round($scenarioSnapshot['gap_fte'] - $baselineSnapshot['gap_fte'], 2),
                'delta_coverage_pct' => round($scenarioSnapshot['coverage_pct'] - $baselineSnapshot['coverage_pct'], 2),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function analyzeWithContext(Scenario $scenario, int $organizationId, string $planningContext): array
    {
        $analyzedScenario = $planningContext === 'baseline'
            ? $this->resolveBaselineScenario($organizationId) ?? $scenario
            : $scenario;

        $metrics = $this->buildMetricsSnapshot($analyzedScenario, $planningContext);

        return [
            'planning_context' => $planningContext,
            'requested_scenario_id' => $scenario->id,
            'analyzed_scenario_id' => $analyzedScenario->id,
            'summary' => $metrics,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function compareScenarioImpactWithBaseline(Scenario $scenario, int $organizationId, array $impactParameters = []): array
    {
        $baselineScenario = $this->resolveBaselineScenario($organizationId, $scenario->id);

        $scenarioRisk = $this->modeledRiskFromScenario($scenario, $impactParameters);
        $scenarioCost = $this->modeledCostFromScenario($scenario, $scenarioRisk, $impactParameters);

        $baselineRisk = $baselineScenario
            ? $this->modeledRiskFromScenario($baselineScenario, $impactParameters)
            : 0.0;
        $baselineCost = $baselineScenario
            ? $this->modeledCostFromScenario($baselineScenario, $baselineRisk, $impactParameters)
            : 0.0;

        return [
            'scenario_id' => $scenario->id,
            'baseline_scenario_id' => $baselineScenario?->id,
            'impact_parameters' => [
                'cost_per_gap_hh' => (float) ($impactParameters['cost_per_gap_hh'] ?? 0.0),
                'cost_risk_multiplier' => (float) ($impactParameters['cost_risk_multiplier'] ?? 0.0),
                'risk_base_offset' => (float) ($impactParameters['risk_base_offset'] ?? 0.0),
                'risk_weight_gap_pct' => (float) ($impactParameters['risk_weight_gap_pct'] ?? 0.0),
                'risk_weight_attrition_pct' => (float) ($impactParameters['risk_weight_attrition_pct'] ?? 0.0),
                'risk_weight_ramp_gap' => (float) ($impactParameters['risk_weight_ramp_gap'] ?? 0.0),
            ],
            'scenario' => [
                'cost_estimate' => $scenarioCost,
                'risk_score' => $scenarioRisk,
                'risk_level' => $this->riskLevelFromScore($scenarioRisk),
            ],
            'baseline' => [
                'cost_estimate' => $baselineCost,
                'risk_score' => $baselineRisk,
                'risk_level' => $this->riskLevelFromScore($baselineRisk),
            ],
            'delta' => [
                'delta_cost_estimate' => round($scenarioCost - $baselineCost, 2),
                'delta_risk_score' => round($scenarioRisk - $baselineRisk, 2),
                'delta_risk_level' => $this->deltaRiskLevel($scenarioRisk, $baselineRisk),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $adjustments
     * @return array<string, mixed>
     */
    public function analyzeOperationalSensitivity(Scenario $scenario, array $adjustments): array
    {
        $lines = WorkforceDemandLine::query()
            ->where('scenario_id', $scenario->id)
            ->where('organization_id', $scenario->organization_id)
            ->get();

        $baselineRequiredHh = 0.0;
        $baselineEffectiveHh = 0.0;
        $simulatedRequiredHh = 0.0;
        $simulatedEffectiveHh = 0.0;

        $adjustedProductivity = array_key_exists('productivity_factor', $adjustments)
            ? (float) $adjustments['productivity_factor']
            : null;
        $adjustedCoverage = array_key_exists('coverage_target_pct', $adjustments)
            ? (float) $adjustments['coverage_target_pct']
            : null;
        $adjustedRamp = array_key_exists('ramp_factor', $adjustments)
            ? (float) $adjustments['ramp_factor']
            : null;
        $costPerGapHh = (float) ($adjustments['cost_per_gap_hh'] ?? 0.0);

        foreach ($lines as $line) {
            $requiredHh = ($line->volume_expected * $line->time_standard_minutes) / 60;
            $baselineCoverage = ((float) $line->coverage_target_pct) / 100;
            $baselineProd = (float) $line->productivity_factor;
            $baselineRamp = (float) $line->ramp_factor;

            $baselineRequiredHh += $requiredHh;
            $baselineEffectiveHh += ($requiredHh * $baselineCoverage) / $baselineProd / $baselineRamp;

            $effectiveCoverage = ($adjustedCoverage ?? (float) $line->coverage_target_pct) / 100;
            $effectiveProd = $adjustedProductivity ?? (float) $line->productivity_factor;
            $effectiveRamp = $adjustedRamp ?? (float) $line->ramp_factor;

            $simulatedRequiredHh += $requiredHh;
            $simulatedEffectiveHh += ($requiredHh * $effectiveCoverage) / $effectiveProd / $effectiveRamp;
        }

        $baseline = $this->buildSensitivitySnapshot($scenario->id, $baselineRequiredHh, $baselineEffectiveHh, $costPerGapHh);
        $simulated = $this->buildSensitivitySnapshot($scenario->id, $simulatedRequiredHh, $simulatedEffectiveHh, $costPerGapHh);

        return [
            'scenario_id' => $scenario->id,
            'adjustments' => [
                'productivity_factor' => $adjustedProductivity,
                'coverage_target_pct' => $adjustedCoverage,
                'ramp_factor' => $adjustedRamp,
                'cost_per_gap_hh' => $costPerGapHh,
            ],
            'baseline' => $baseline,
            'simulated' => $simulated,
            'delta' => [
                'delta_gap_hh' => round($simulated['gap_hh'] - $baseline['gap_hh'], 2),
                'delta_gap_fte' => round($simulated['gap_fte'] - $baseline['gap_fte'], 2),
                'delta_coverage_pct' => round($simulated['coverage_pct'] - $baseline['coverage_pct'], 2),
                'delta_gap_cost_estimate' => round($simulated['gap_cost_estimate'] - $baseline['gap_cost_estimate'], 2),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildMetricsSnapshot(Scenario $scenario, string $context): array
    {
        $metricsFromDemandLines = $this->metricsFromDemandLines($scenario);

        $requiredHh = $metricsFromDemandLines['required_hh']
            ?? $this->metricFromScenario($scenario, 'required_hh', 0.0);
        $effectiveHh = $metricsFromDemandLines['effective_hh']
            ?? $this->metricFromScenario($scenario, 'effective_hh', 0.0);
        $gapHh = round($requiredHh - $effectiveHh, 2);
        $coveragePct = $requiredHh > 0
            ? round(($effectiveHh / $requiredHh) * 100, 2)
            : 0.0;
        $gapFte = round($gapHh / 165, 2);

        return [
            'planning_context' => $context,
            'scenario_id' => $scenario->id,
            'required_hh' => round($requiredHh, 2),
            'effective_hh' => round($effectiveHh, 2),
            'coverage_pct' => $coveragePct,
            'gap_hh' => $gapHh,
            'gap_fte' => $gapFte,
        ];
    }

    /**
     * @return array{required_hh: float, effective_hh: float}|array{}
     */
    private function metricsFromDemandLines(Scenario $scenario): array
    {
        $lines = WorkforceDemandLine::query()
            ->where('scenario_id', $scenario->id)
            ->where('organization_id', $scenario->organization_id)
            ->get();

        if ($lines->isEmpty()) {
            return [];
        }

        $requiredHh = (float) $lines->sum(function (WorkforceDemandLine $line): float {
            return $line->required_hh;
        });

        $effectiveHh = (float) $lines->sum(function (WorkforceDemandLine $line): float {
            return $line->effective_hh;
        });

        return [
            'required_hh' => round($requiredHh, 2),
            'effective_hh' => round($effectiveHh, 2),
        ];
    }

    private function metricFromScenario(Scenario $scenario, string $metricKey, float $default): float
    {
        $kpis = is_array($scenario->kpis) ? $scenario->kpis : [];
        if (array_key_exists($metricKey, $kpis) && is_numeric($kpis[$metricKey])) {
            return (float) $kpis[$metricKey];
        }

        $assumptions = is_array($scenario->assumptions) ? $scenario->assumptions : [];
        if (array_key_exists($metricKey, $assumptions) && is_numeric($assumptions[$metricKey])) {
            return (float) $assumptions[$metricKey];
        }

        return $default;
    }

    private function resolveBaselineScenario(int $organizationId, ?int $excludeScenarioId = null): ?Scenario
    {
        return Scenario::query()
            ->where('organization_id', $organizationId)
            ->when($excludeScenarioId !== null, fn ($query) => $query->where('id', '!=', $excludeScenarioId))
            ->orderByRaw("CASE WHEN status IN ('active', 'approved') THEN 0 ELSE 1 END")
            ->latest('id')
            ->first();
    }

    private function costFromScenario(Scenario $scenario): float
    {
        if (is_numeric($scenario->estimated_budget ?? null)) {
            return (float) $scenario->estimated_budget;
        }

        if (is_numeric($scenario->budget ?? null)) {
            return (float) $scenario->budget;
        }

        return 0.0;
    }

    private function riskFromScenario(Scenario $scenario): float
    {
        if (is_numeric($scenario->risk_score ?? null)) {
            return (float) $scenario->risk_score;
        }

        $kpis = is_array($scenario->kpis) ? $scenario->kpis : [];

        return (array_key_exists('risk_score', $kpis) && is_numeric($kpis['risk_score']))
            ? (float) $kpis['risk_score']
            : 0.0;
    }

    private function riskLevelFromScore(float $score): string
    {
        return match (true) {
            $score >= 8 => 'critical',
            $score >= 6 => 'high',
            $score >= 4 => 'medium',
            $score >= 2 => 'low',
            default => 'minimal',
        };
    }

    private function deltaRiskLevel(float $scenarioRisk, float $baselineRisk): string
    {
        return match (true) {
            $scenarioRisk > $baselineRisk => 'higher',
            $scenarioRisk < $baselineRisk => 'lower',
            default => 'stable',
        };
    }

    private function modeledRiskFromScenario(Scenario $scenario, array $impactParameters): float
    {
        $baseRisk = $this->riskFromScenario($scenario);
        $weights = $this->impactWeights($impactParameters);
        $operationalInputs = $this->operationalRiskInputs($scenario);

        $riskScore = $baseRisk
            + $weights['risk_base_offset']
            + (($operationalInputs['gap_pct'] / 10) * $weights['risk_weight_gap_pct'])
            + (($operationalInputs['avg_attrition_pct'] / 10) * $weights['risk_weight_attrition_pct'])
            + ((max(1 - $operationalInputs['avg_ramp_factor'], 0) * 10) * $weights['risk_weight_ramp_gap']);

        return round(max(0, min(10, $riskScore)), 2);
    }

    private function modeledCostFromScenario(Scenario $scenario, float $riskScore, array $impactParameters): float
    {
        $baseCost = $this->costFromScenario($scenario);
        $weights = $this->impactWeights($impactParameters);
        $snapshot = $this->buildMetricsSnapshot($scenario, 'scenario');
        $gapHh = max((float) $snapshot['gap_hh'], 0.0);

        $gapCost = $gapHh * $weights['cost_per_gap_hh'];
        $riskCost = $riskScore * $weights['cost_risk_multiplier'];

        return round($baseCost + $gapCost + $riskCost, 2);
    }

    /** @return array<string, float> */
    private function impactWeights(array $impactParameters): array
    {
        return [
            'cost_per_gap_hh' => (float) ($impactParameters['cost_per_gap_hh'] ?? 0.0),
            'cost_risk_multiplier' => (float) ($impactParameters['cost_risk_multiplier'] ?? 0.0),
            'risk_base_offset' => (float) ($impactParameters['risk_base_offset'] ?? 0.0),
            'risk_weight_gap_pct' => (float) ($impactParameters['risk_weight_gap_pct'] ?? 0.0),
            'risk_weight_attrition_pct' => (float) ($impactParameters['risk_weight_attrition_pct'] ?? 0.0),
            'risk_weight_ramp_gap' => (float) ($impactParameters['risk_weight_ramp_gap'] ?? 0.0),
        ];
    }

    /** @return array{gap_pct: float, avg_attrition_pct: float, avg_ramp_factor: float} */
    private function operationalRiskInputs(Scenario $scenario): array
    {
        $snapshot = $this->buildMetricsSnapshot($scenario, 'scenario');
        $requiredHh = (float) $snapshot['required_hh'];
        $gapHh = max((float) $snapshot['gap_hh'], 0.0);
        $gapPct = $requiredHh > 0 ? ($gapHh / $requiredHh) * 100 : 0.0;

        $lines = WorkforceDemandLine::query()
            ->where('scenario_id', $scenario->id)
            ->where('organization_id', $scenario->organization_id);

        $avgAttritionPct = (float) ($lines->avg('attrition_pct') ?? 0.0);
        $avgRampFactor = (float) ((clone $lines)->avg('ramp_factor') ?? 1.0);

        return [
            'gap_pct' => round($gapPct, 2),
            'avg_attrition_pct' => round($avgAttritionPct, 2),
            'avg_ramp_factor' => round($avgRampFactor, 4),
        ];
    }

    /** @return array<string, mixed> */
    private function buildSensitivitySnapshot(int $scenarioId, float $requiredHh, float $effectiveHh, float $costPerGapHh): array
    {
        $roundedRequiredHh = round($requiredHh, 2);
        $roundedEffectiveHh = round($effectiveHh, 2);
        $gapHh = round($roundedRequiredHh - $roundedEffectiveHh, 2);
        $coveragePct = $roundedRequiredHh > 0
            ? round(($roundedEffectiveHh / $roundedRequiredHh) * 100, 2)
            : 0.0;

        return [
            'scenario_id' => $scenarioId,
            'required_hh' => $roundedRequiredHh,
            'effective_hh' => $roundedEffectiveHh,
            'coverage_pct' => $coveragePct,
            'gap_hh' => $gapHh,
            'gap_fte' => round($gapHh / 165, 2),
            'gap_cost_estimate' => round(max($gapHh, 0) * $costPerGapHh, 2),
        ];
    }
}
