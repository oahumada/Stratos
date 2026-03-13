<?php

namespace App\Services;

use App\Models\EmployeePulse;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\PulseResponse;
use App\Models\Scenario;
use Illuminate\Support\Facades\DB;

class TalentRoiService
{
    /**
     * ROI Estimations (Industry Averages - can be customized per org)
     */
    const AVG_HIRING_COST = 4500; // USD per hire

    const AVG_REPLACEMENT_COST_MULTIPLIER = 0.5; // 50% of annual salary

    const AVG_ANNUAL_SALARY = 45000; // USD

    protected ScenarioAnalyticsService $scenarioAnalytics;
    protected \App\Services\Intelligence\ImpactEngineService $impactEngine;

    public function __construct(
        ScenarioAnalyticsService $scenarioAnalytics,
        \App\Services\Intelligence\ImpactEngineService $impactEngine
    ) {
        $this->scenarioAnalytics = $scenarioAnalytics;
        $this->impactEngine = $impactEngine;
    }

    /**
     * Get consolidated high-level metrics for investors.
     */
    public function getExecutiveSummary(?int $organizationId = null): array
    {
        $organizationId = $this->resolveOrganizationId($organizationId);

        $totalHeadcount = People::where('organization_id', $organizationId)->count();
        $totalScenarios = Scenario::where('organization_id', $organizationId)->count();

        $avgReadiness = $this->calculateGlobalReadiness($organizationId);
        $talentRoi = $this->calculateTotalTalentRoi($organizationId);
        
        // Impact Engine Metrics
        $impactKpis = $this->impactEngine->calculateFinancialKPIs($organizationId);

        // High-level risks
        $criticalGapRate = $this->calculateCriticalGapRate($organizationId);

        $cultureHealthScore = $this->calculateCultureHealthScore($organizationId);
        $avgTurnoverRisk = $this->getAverageTurnoverRisk($organizationId);
        $aiAugmentationIndex = $this->calculateAiAugmentationIndex($organizationId);

        $stratosIq = $this->calculateStratosIq([
            'org_readiness' => round($avgReadiness * 100, 1),
            'critical_gap_rate' => round($criticalGapRate * 100, 1),
            'ai_augmentation_index' => $aiAugmentationIndex,
        ], $cultureHealthScore, $avgTurnoverRisk);

        return [
            'headcount' => $totalHeadcount,
            'active_scenarios' => $totalScenarios,
            'org_readiness' => round($avgReadiness * 100, 1),
            'talent_roi_usd' => $talentRoi,
            'hcva_usd' => $impactKpis['hcva_average'],
            'replacement_risk_usd' => $impactKpis['total_replacement_risk_usd'],
            'critical_gap_rate' => round($criticalGapRate * 100, 1),
            'ai_augmentation_index' => $aiAugmentationIndex,
            'culture_health_score' => $cultureHealthScore,
            'avg_turnover_risk' => round($avgTurnoverRisk, 1),
            'stratos_iq' => $stratosIq,
        ];
    }

    /**
     * CEO view: Top 5 KPIs de lectura en menos de 10 segundos.
     */
    public function getCeoTopKpis(array $summary): array
    {
        $stratosIq = (float) ($summary['stratos_iq']['score'] ?? 0);
        $orgReadiness = (float) ($summary['org_readiness'] ?? 0);
        $criticalGapRate = (float) ($summary['critical_gap_rate'] ?? 0);
        $talentRoi = (float) ($summary['talent_roi_usd'] ?? 0);
        $avgTurnoverRisk = (float) ($summary['avg_turnover_risk'] ?? 0);
        $hcva = (float) ($summary['hcva_usd'] ?? 0);

        return [
            [
                'key' => 'hcva_usd',
                'label' => 'HCVA',
                'value' => round($hcva, 2),
                'unit' => 'usd',
                'status' => $this->resolveStatus($hcva, 100000, true),
                'driver' => 'Valor Agregado por Capital Humano (Human Capital Value Added)',
                'action' => 'Analizar desviaciones por departamento para optimizar HCVA',
            ],
            [
                'key' => 'stratos_iq',
                'label' => 'Stratos IQ',
                'value' => round($stratosIq, 1),
                'unit' => '/100',
                'status' => $this->resolveStatus($stratosIq, 60, true),
                'driver' => 'Síntesis de readiness, brechas, IA, cultura y fuga de talento',
                'action' => 'Priorizar 1 iniciativa que eleve el componente más débil',
            ],
            [
                'key' => 'org_readiness',
                'label' => 'Org Readiness',
                'value' => round($orgReadiness, 1),
                'unit' => '%',
                'status' => $this->resolveStatus($orgReadiness, 65, true),
                'driver' => 'Cobertura de capacidades críticas para ejecutar estrategia',
                'action' => 'Acelerar estrategias Build/Borrow en capacidades bajo 50%',
            ],
            [
                'key' => 'critical_gap_rate',
                'label' => 'Critical Gap Rate',
                'value' => round($criticalGapRate, 1),
                'unit' => '%',
                'status' => $this->resolveStatus($criticalGapRate, 25, false),
                'driver' => 'Skills críticas por debajo de 50% del nivel requerido',
                'action' => 'Mitigar gaps top-10 por impacto en ingresos/operación',
            ],
            [
                'key' => 'talent_roi_usd',
                'label' => 'Talent ROI',
                'value' => round($talentRoi, 2),
                'unit' => 'usd',
                'status' => $this->resolveStatus($talentRoi, 150000, true),
                'driver' => 'Ahorros por movilidad interna + productividad asistida por IA',
                'action' => 'Escalar prácticas con mayor retorno por unidad de costo',
            ],
            [
                'key' => 'avg_turnover_risk',
                'label' => 'Turnover Risk',
                'value' => round($avgTurnoverRisk, 1),
                'unit' => '/100',
                'status' => $this->resolveStatus($avgTurnoverRisk, 45, false),
                'driver' => 'Riesgo promedio de salida en talento activo',
                'action' => 'Activar plan de retención para segmentos con riesgo alto',
            ],
        ];
    }

    /**
     * Calculate global Readiness across all people/roles.
     */
    private function calculateGlobalReadiness(int $organizationId): float
    {
        $total = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
            ->where('people.organization_id', $organizationId)
            ->count();

        if ($total === 0) {
            return 0;
        }

        $avgReadiness = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
            ->where('people.organization_id', $organizationId)
            ->selectRaw('AVG(LEAST(1.0, current_level / NULLIF(required_level, 0))) as avg')
            ->value('avg') ?: 0;

        return (float) $avgReadiness;
    }

    /**
     * Estimated ROI = (Internal Growth Savings) + (Attrition Prevention Value)
     */
    private function calculateTotalTalentRoi(int $organizationId): float
    {
        // 1. Savings from internal upskilling (people who reached required level)
        $upskilledCount = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
            ->where('people.organization_id', $organizationId)
            ->whereColumn('current_level', '>=', 'required_level')
            ->where('required_level', '>', 0)
            ->distinct('people_id')
            ->count();

        $upskillingSavings = $upskilledCount * self::AVG_HIRING_COST;

        // 2. Value from AI-Assisted productivity (mock/aggregated from bot strategies)
        $botStrategies = DB::table('scenario_closure_strategies')
            ->join('scenarios', 'scenario_closure_strategies.scenario_id', '=', 'scenarios.id')
            ->where('scenarios.organization_id', $organizationId)
            ->where('strategy', 'bot')
            ->count();
        $aiProductivityValue = $botStrategies * (self::AVG_ANNUAL_SALARY * 0.2); // Assuming 20% efficiency boost

        return round($upskillingSavings + $aiProductivityValue, 2);
    }

    private function calculateCriticalGapRate(int $organizationId): float
    {
        $totalPivotRows = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
            ->where('people.organization_id', $organizationId)
            ->count();

        if ($totalPivotRows === 0) {
            return 0;
        }

        $criticalGaps = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
            ->where('people.organization_id', $organizationId)
            ->whereRaw('current_level < (required_level * 0.5)')
            ->where('required_level', '>', 0)
            ->count();

        return $criticalGaps / $totalPivotRows;
    }

    private function calculateAiAugmentationIndex(int $organizationId): float
    {
        // Percentage of roles that have at least one 'bot' strategy mapped
        $totalRoles = DB::table('roles')
            ->where('organization_id', $organizationId)
            ->count();

        if ($totalRoles === 0) {
            return 0;
        }

        $augmentedRoles = DB::table('scenario_closure_strategies')
            ->join('scenarios', 'scenario_closure_strategies.scenario_id', '=', 'scenarios.id')
            ->join('roles', 'scenario_closure_strategies.role_id', '=', 'roles.id')
            ->where('scenarios.organization_id', $organizationId)
            ->where('roles.organization_id', $organizationId)
            ->where('strategy', 'bot')
            ->distinct('role_id')
            ->count('role_id');

        return round(($augmentedRoles / $totalRoles) * 100, 1);
    }

    /**
     * Get data for distribution charts.
     */
    public function getDistributionData(?int $organizationId = null): array
    {
        $organizationId = $this->resolveOrganizationId($organizationId);

        return [
            'skill_levels' => DB::table('people_role_skills')
                ->join('people', 'people_role_skills.people_id', '=', 'people.id')
                ->where('people.organization_id', $organizationId)
                ->select('current_level', DB::raw('count(*) as count'))
                ->groupBy('current_level')
                ->orderBy('current_level')
                ->get(),
            'department_readiness' => PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
                ->join('departments', 'people.department_id', '=', 'departments.id')
                ->where('people.organization_id', $organizationId)
                ->select('departments.name', DB::raw('AVG(LEAST(1.0, current_level / NULLIF(required_level, 0))) * 100 as readiness'))
                ->groupBy('departments.name')
                ->get(),
        ];
    }

    private function calculateCultureHealthScore(int $organizationId): int
    {
        $avgSentiment = (float) (PulseResponse::whereHas('people', function ($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->where('created_at', '>=', now()->subDays(30))->avg('sentiment_score') ?? 0);

        $pulseCount = PulseResponse::whereHas('people', function ($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->where('created_at', '>=', now()->subDays(30))->count();

        $score = 50;
        $score += ($avgSentiment - 50) * 0.4;

        $trend = $this->calculateSentimentTrend($organizationId);
        if ($trend === 'improving') {
            $score += 10;
        } elseif ($trend === 'declining') {
            $score -= 10;
        }

        if ($pulseCount >= 20) {
            $score += 10;
        } elseif ($pulseCount < 5) {
            $score -= 5;
        }

        return max(0, min(100, (int) round($score)));
    }

    private function calculateSentimentTrend(int $organizationId): string
    {
        $recent = (float) (PulseResponse::whereHas('people', function ($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->where('created_at', '>=', now()->subDays(15))->avg('sentiment_score') ?? 0);

        $previous = (float) (PulseResponse::whereHas('people', function ($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->whereBetween('created_at', [now()->subDays(30), now()->subDays(15)])->avg('sentiment_score') ?? 0);

        if ($recent > ($previous + 5)) {
            return 'improving';
        }

        if ($recent < ($previous - 5)) {
            return 'declining';
        }

        return 'stable';
    }

    private function getAverageTurnoverRisk(int $organizationId): float
    {
        $riskRows = EmployeePulse::query()
            ->join('people', 'employee_pulses.people_id', '=', 'people.id')
            ->where('people.organization_id', $organizationId)
            ->whereNotNull('employee_pulses.ai_turnover_risk')
            ->select('employee_pulses.ai_turnover_risk')
            ->latest('employee_pulses.id')
            ->limit(200)
            ->pluck('employee_pulses.ai_turnover_risk');

        if ($riskRows->isEmpty()) {
            return 50.0;
        }

        $score = $riskRows->avg(function ($risk) {
            return $this->mapTurnoverRiskToScore($risk);
        });

        return (float) $score;
    }

    private function mapTurnoverRiskToScore(?string $risk): int
    {
        return match (strtolower((string) $risk)) {
            'low' => 25,
            'medium' => 60,
            'high' => 85,
            default => 50,
        };
    }

    private function calculateStratosIq(array $summary, int $cultureHealthScore, float $avgTurnoverRisk): array
    {
        $orgReadiness = (float) ($summary['org_readiness'] ?? 0);
        $criticalGapRate = (float) ($summary['critical_gap_rate'] ?? 0);
        $aiAugmentationIndex = (float) ($summary['ai_augmentation_index'] ?? 0);

        $score =
            (0.30 * $orgReadiness) +
            (0.20 * (100 - $criticalGapRate)) +
            (0.15 * $aiAugmentationIndex) +
            (0.20 * $cultureHealthScore) +
            (0.15 * (100 - $avgTurnoverRisk));

        $roundedScore = max(0, min(100, round($score, 1)));

        return [
            'score' => $roundedScore,
            'components' => [
                'org_readiness' => round($orgReadiness, 1),
                'critical_gap_inverse' => round(100 - $criticalGapRate, 1),
                'ai_augmentation' => round($aiAugmentationIndex, 1),
                'culture_health' => round($cultureHealthScore, 1),
                'turnover_risk_inverse' => round(100 - $avgTurnoverRisk, 1),
            ],
            'weights' => [
                'org_readiness' => 0.30,
                'critical_gap_inverse' => 0.20,
                'ai_augmentation' => 0.15,
                'culture_health' => 0.20,
                'turnover_risk_inverse' => 0.15,
            ],
        ];
    }

    private function resolveStatus(float $value, float $warnThreshold, bool $higherIsBetter = true): string
    {
        if ($higherIsBetter) {
            if ($value >= ($warnThreshold + 10)) {
                return 'green';
            }

            if ($value >= $warnThreshold) {
                return 'yellow';
            }

            return 'red';
        }

        if ($value <= ($warnThreshold - 10)) {
            return 'green';
        }

        if ($value <= $warnThreshold) {
            return 'yellow';
        }

        return 'red';
    }

    private function resolveOrganizationId(?int $organizationId = null): int
    {
        if ($organizationId !== null) {
            return $organizationId;
        }

        return (int) (auth()->user()->organization_id ?? 0);
    }
}
