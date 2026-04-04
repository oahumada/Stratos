<?php

namespace App\Services;

use App\Models\Scenario;
use App\Models\ScenarioClosureStrategy;
use App\Models\WorkforceActionPlan;
use App\Models\WorkforceDemandLine;
use Illuminate\Support\Collection;

/**
 * ScenarioComparatorService — Fase 4
 *
 * Compara N escenarios de workforce side-by-side en las dimensiones:
 * cobertura, costo, riesgo, brechas, distribución de palancas, progreso de acciones.
 *
 * También ejecuta sensitivity sweeps: simula una variable en un rango y
 * calcula cómo cambian los KPIs en cada paso.
 */
class ScenarioComparatorService
{
    /** Horas efectivas por FTE al mes */
    private const HH_PER_FTE = 165;

    /**
     * Compara múltiples escenarios del mismo tenant side-by-side.
     *
     * @param  Scenario[]|Collection<int,Scenario>  $scenarios
     * @return array<string, mixed>
     */
    public function compareMulti(Collection $scenarios): array
    {
        $snapshots = $scenarios->map(fn (Scenario $s) => $this->buildSnapshot($s))->values()->all();

        // Ranking por cobertura y costo combinado
        $ranked = $this->rankScenarios($snapshots);

        return [
            'scenarios_count' => count($snapshots),
            'scenarios'       => $snapshots,
            'ranking'         => $ranked,
            'dimensions'      => $this->buildDimensionMatrix($snapshots),
        ];
    }

    /**
     * Ejecuta un sweep de sensibilidad para un escenario, variando una variable
     * en un rango y calculando los KPIs en cada paso.
     *
     * @param  array<string, mixed>  $sweepConfig  {variable, min, max, steps, cost_per_gap_hh}
     * @return array<string, mixed>
     */
    public function sensitivitySweep(Scenario $scenario, array $sweepConfig): array
    {
        $variable    = (string) ($sweepConfig['variable'] ?? 'productivity_factor');
        $min         = (float) ($sweepConfig['min'] ?? 0.5);
        $max         = (float) ($sweepConfig['max'] ?? 2.0);
        $steps       = max(2, min(20, (int) ($sweepConfig['steps'] ?? 10)));
        $costPerGapHh = (float) ($sweepConfig['cost_per_gap_hh'] ?? 50.0);

        $stepSize = ($max - $min) / ($steps - 1);

        $lines = WorkforceDemandLine::query()
            ->where('scenario_id', $scenario->id)
            ->where('organization_id', $scenario->organization_id)
            ->get();

        $results = [];

        for ($i = 0; $i < $steps; $i++) {
            $value = round($min + $i * $stepSize, 4);
            $kpis  = $this->computeKpisForValue($lines, $variable, $value, $costPerGapHh);

            $results[] = [
                'step'     => $i + 1,
                'variable' => $variable,
                'value'    => $value,
                'kpis'     => $kpis,
            ];
        }

        // Identify optimal point (highest coverage, lowest cost)
        $optimal = $this->findOptimalStep($results);

        return [
            'scenario_id'  => $scenario->id,
            'variable'     => $variable,
            'range'        => ['min' => $min, 'max' => $max, 'steps' => $steps],
            'sweep'        => $results,
            'optimal_step' => $optimal,
            'interpretation' => $this->interpretSweep($variable, $optimal, $results),
        ];
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    /**
     * Construye el snapshot de métricas de un escenario.
     *
     * @return array<string, mixed>
     */
    private function buildSnapshot(Scenario $scenario): array
    {
        $lines = WorkforceDemandLine::query()
            ->where('scenario_id', $scenario->id)
            ->where('organization_id', $scenario->organization_id)
            ->get();

        [$requiredHh, $effectiveHh] = $this->sumDemandLines($lines);

        $gapHh      = max(0.0, $requiredHh - $effectiveHh);
        $coveragePct = $requiredHh > 0 ? round(($effectiveHh / $requiredHh) * 100, 2) : 0.0;
        $gapFte     = round($gapHh / self::HH_PER_FTE, 2);

        // Budget from action plans
        $actionBudget = (float) WorkforceActionPlan::query()
            ->where('scenario_id', $scenario->id)
            ->sum('budget');

        $actionActual = (float) WorkforceActionPlan::query()
            ->where('scenario_id', $scenario->id)
            ->sum('actual_cost');

        // Lever distribution from closure strategies
        $leverDist = ScenarioClosureStrategy::query()
            ->where('scenario_id', $scenario->id)
            ->selectRaw('strategy, count(*) as total')
            ->groupBy('strategy')
            ->pluck('total', 'strategy')
            ->all();

        // Action plan progress
        $actions = WorkforceActionPlan::query()
            ->where('scenario_id', $scenario->id)
            ->selectRaw('status, count(*) as total, avg(progress_pct) as avg_progress')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Risk score: simple heuristic (gap_pct + blocked_pct)
        $totalActions  = WorkforceActionPlan::query()->where('scenario_id', $scenario->id)->count();
        $blockedActions = (int) ($actions->get('blocked')?->total ?? 0);
        $blockedPct    = $totalActions > 0 ? ($blockedActions / $totalActions) * 100 : 0;
        $gapRiskPct    = $requiredHh > 0 ? ($gapHh / $requiredHh) * 100 : 0;
        $riskScore     = round(($gapRiskPct * 0.6) + ($blockedPct * 0.4), 2);

        return [
            'scenario_id'    => $scenario->id,
            'scenario_name'  => $scenario->name,
            'status'         => $scenario->status,
            'coverage_pct'   => $coveragePct,
            'gap_hh'         => round($gapHh, 2),
            'gap_fte'        => $gapFte,
            'budget'         => $actionBudget,
            'actual_cost'    => $actionActual,
            'risk_score'     => $riskScore,
            'lever_distribution' => $leverDist,
            'action_summary' => [
                'total'       => $totalActions,
                'by_status'   => $actions->map(fn ($r) => (int) $r->total)->all(),
                'avg_progress' => round((float) ($actions->sum('avg_progress') / max(1, $actions->count())), 1),
            ],
        ];
    }

    /**
     * Rankea escenarios por composite score (coverage 50% + costo-eficiencia 30% + bajo riesgo 20%).
     *
     * @param  array<int, array<string, mixed>>  $snapshots
     * @return array<int, array<string, mixed>>
     */
    private function rankScenarios(array $snapshots): array
    {
        $maxBudget = max(1.0, max(array_column($snapshots, 'budget')));

        $scored = array_map(function (array $s) use ($maxBudget) {
            $coverageScore = $s['coverage_pct'];
            $costScore     = 30 * (1 - ($s['budget'] / $maxBudget));
            $riskScore     = 20 * (1 - min(1.0, $s['risk_score'] / 100));
            $composite     = round(($coverageScore * 0.5) + $costScore + $riskScore, 2);

            return [
                'scenario_id'     => $s['scenario_id'],
                'scenario_name'   => $s['scenario_name'],
                'composite_score' => $composite,
                'coverage_pct'    => $s['coverage_pct'],
                'budget'          => $s['budget'],
                'risk_score'      => $s['risk_score'],
            ];
        }, $snapshots);

        usort($scored, fn ($a, $b) => $b['composite_score'] <=> $a['composite_score']);

        foreach ($scored as $i => &$item) {
            $item['rank'] = $i + 1;
        }

        return $scored;
    }

    /**
     * Construye la matriz de dimensiones para visualización comparativa.
     *
     * @param  array<int, array<string, mixed>>  $snapshots
     * @return array<string, array<int, mixed>>
     */
    private function buildDimensionMatrix(array $snapshots): array
    {
        return [
            'coverage_pct' => array_map(fn ($s) => ['id' => $s['scenario_id'], 'value' => $s['coverage_pct']], $snapshots),
            'gap_fte'      => array_map(fn ($s) => ['id' => $s['scenario_id'], 'value' => $s['gap_fte']], $snapshots),
            'budget'       => array_map(fn ($s) => ['id' => $s['scenario_id'], 'value' => $s['budget']], $snapshots),
            'risk_score'   => array_map(fn ($s) => ['id' => $s['scenario_id'], 'value' => $s['risk_score']], $snapshots),
        ];
    }

    /**
     * Suma required_hh y effective_hh de las líneas de demanda.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int,WorkforceDemandLine>  $lines
     * @return array{0: float, 1: float}
     */
    private function sumDemandLines($lines): array
    {
        $requiredHh = 0.0;
        $effectiveHh = 0.0;

        foreach ($lines as $line) {
            $requiredHh  += (float) $line->required_hh;
            $effectiveHh += (float) $line->effective_hh;
        }

        return [$requiredHh, $effectiveHh];
    }

    /**
     * Calcula los KPIs de cobertura/gap/costo para un valor específico de una variable.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int,WorkforceDemandLine>  $lines
     * @return array<string, float>
     */
    private function computeKpisForValue($lines, string $variable, float $value, float $costPerGapHh): array
    {
        $simulatedRequired = 0.0;
        $simulatedEffective = 0.0;

        foreach ($lines as $line) {
            $requiredHh = ($line->volume_expected * $line->time_standard_minutes) / 60;
            $simulatedRequired += $requiredHh;

            $prod     = $variable === 'productivity_factor' ? $value : (float) $line->productivity_factor;
            $coverage = $variable === 'coverage_target_pct' ? ($value / 100) : ((float) $line->coverage_target_pct / 100);
            $ramp     = $variable === 'ramp_factor' ? $value : (float) $line->ramp_factor;

            $simulatedEffective += $prod > 0 && $ramp > 0
                ? ($requiredHh * $coverage) / $prod / $ramp
                : 0.0;
        }

        $gapHh      = max(0.0, $simulatedRequired - $simulatedEffective);
        $coveragePct = $simulatedRequired > 0
            ? round(($simulatedEffective / $simulatedRequired) * 100, 2)
            : 0.0;
        $gapCost    = round($gapHh * $costPerGapHh, 2);
        $gapFte     = round($gapHh / self::HH_PER_FTE, 2);

        return [
            'coverage_pct'    => $coveragePct,
            'gap_hh'          => round($gapHh, 2),
            'gap_fte'         => $gapFte,
            'gap_cost_estimate' => $gapCost,
        ];
    }

    /**
     * Encuentra el paso óptimo del sweep (mayor cobertura con menor costo).
     *
     * @param  array<int, array<string, mixed>>  $results
     * @return array<string, mixed>|null
     */
    private function findOptimalStep(array $results): ?array
    {
        if (empty($results)) {
            return null;
        }

        $maxCoverage = max(array_column(array_column($results, 'kpis'), 'coverage_pct'));

        // De los pasos con cobertura máxima, elegir el de menor costo
        $candidates = array_filter($results, fn ($r) => $r['kpis']['coverage_pct'] >= $maxCoverage - 1.0);
        usort($candidates, fn ($a, $b) => $a['kpis']['gap_cost_estimate'] <=> $b['kpis']['gap_cost_estimate']);

        return reset($candidates) ?: $results[0];
    }

    /**
     * Genera una interpretación textual del sweep.
     *
     * @param  array<string, mixed>|null  $optimal
     * @param  array<int, array<string, mixed>>  $allResults
     * @return string
     */
    private function interpretSweep(string $variable, ?array $optimal, array $allResults): string
    {
        if (! $optimal) {
            return 'Sin datos suficientes para interpretar el sweep.';
        }

        $varLabel = match ($variable) {
            'productivity_factor'  => 'productividad',
            'coverage_target_pct'  => 'cobertura objetivo (%)',
            'ramp_factor'          => 'factor de ramp-up',
            default                => $variable,
        };

        $optValue    = $optimal['value'];
        $optCoverage = $optimal['kpis']['coverage_pct'];

        return "El valor óptimo de {$varLabel} es {$optValue}, "
            . "que logra una cobertura de {$optCoverage}% al menor costo estimado. "
            . 'Se evaluaron ' . count($allResults) . ' puntos en el rango de sensibilidad.';
    }
}
