<?php

namespace App\Services;

use App\Models\EmployeePulse;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\PulseResponse;
use App\Models\Scenario;
use Illuminate\Support\Facades\DB;
use App\Models\ExecutiveAggregate;

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

    /**
     * Cache for executive aggregates to avoid repeating heavy SQL in a single request.
     * keyed by organizationId
     * @var array<int, object>
     */
    protected array $executiveAggregatesCache = [];
    /**
     * Simple per-request memoization cache for heavy queries.
     * @var array<string,mixed>
     */
    protected array $memo = [];

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
    public function getExecutiveSummary(?int $organizationId = null, ?array $precomputedTalentPipeline = null): array
    {
        $organizationId = $this->resolveOrganizationId($organizationId);
        $aggs = $this->getExecutiveAggregates($organizationId);
        $totalHeadcount = (int) ($aggs->headcount ?? 0);
        $totalScenarios = (int) ($aggs->total_scenarios ?? 0);

        // Use aggregated values when available to avoid extra DB hits
        $avgReadiness = isset($aggs->avg_readiness) ? (float) $aggs->avg_readiness : $this->calculateGlobalReadiness($organizationId);

        // Pass precomputed upskilled & bot counts to avoid duplicate queries
        $talentRoi = $this->calculateTotalTalentRoi($organizationId, (int) ($aggs->upskilled_count ?? 0), (int) ($aggs->bot_strategies ?? 0));

        // Impact Engine Metrics
        $impactKpis = $this->impactEngine->calculateFinancialKPIs($organizationId);

        // High-level risks
        if (isset($aggs->critical_gaps) && isset($aggs->total_pivot_rows) && $aggs->total_pivot_rows > 0) {
            $criticalGapRate = $aggs->critical_gaps / $aggs->total_pivot_rows;
        } else {
            $criticalGapRate = $this->calculateCriticalGapRate($organizationId);
        }

        $cultureHealthScore = $this->calculateCultureHealthScore($organizationId);
        // Prefer precomputed aggregate when available to avoid extra queries
        if (isset($aggs->avg_turnover_risk) && $aggs->avg_turnover_risk !== null) {
            $avgTurnoverRisk = (float) $aggs->avg_turnover_risk;
        } else {
            // Default when no pulse data: 50.0 (matches previous fallback behavior)
            $avgTurnoverRisk = 50.0;
        }

        if (isset($aggs->total_roles) && isset($aggs->augmented_roles) && $aggs->total_roles > 0) {
            $aiAugmentationIndex = round(($aggs->augmented_roles / $aggs->total_roles) * 100, 1);
        } else {
            $aiAugmentationIndex = $this->calculateAiAugmentationIndex($organizationId);
        }

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
            // Expose some precomputed aggregates for callers that want to avoid duplicate queries
            'upskilled_count' => (int) ($aggs->upskilled_count ?? 0),
            'bot_strategies' => (int) ($aggs->bot_strategies ?? 0),
            'total_pivot_rows' => (int) ($aggs->total_pivot_rows ?? 0),
            'talent_pipeline' => $precomputedTalentPipeline ?? null,
        ];
    }

        /**
         * Ejecuta agregados clave en una sola consulta para reducir roundtrips.
         */
        private function getExecutiveAggregates(int $organizationId): object
        {
                $sql = <<<'SQL'
select
    (select count(*) from people where organization_id = ? and deleted_at is null) as headcount,
    (select count(*) from scenarios where organization_id = ?) as total_scenarios,
    (select count(distinct people_role_skills.people_id) from people_role_skills join people on people_role_skills.people_id = people.id where people.organization_id = ? and people_role_skills.current_level >= people_role_skills.required_level and people_role_skills.required_level > 0) as upskilled_count,
        (select AVG(CASE WHEN prs.required_level > prs.current_level THEN (prs.required_level - prs.current_level) END) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ?) as avg_gap,
    (select count(*) from scenario_closure_strategies sc join scenarios s on sc.scenario_id = s.id where s.organization_id = ? and sc.strategy = 'bot') as bot_strategies,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ?) as total_pivot_rows,
    (select AVG(LEAST(1.0, prs.current_level / NULLIF(prs.required_level, 0))) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ?) as avg_readiness,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.required_level > 0 and prs.current_level < (prs.required_level * 0.5)) as critical_gaps,
    (select count(*) from roles where organization_id = ?) as total_roles,
    (select count(distinct sc.role_id) from scenario_closure_strategies sc join scenarios s on sc.scenario_id = s.id join roles r on sc.role_id = r.id where s.organization_id = ? and r.organization_id = ? and sc.strategy = 'bot') as augmented_roles,
            (select AVG(CASE WHEN ep.ai_turnover_risk = 'low' THEN 25 WHEN ep.ai_turnover_risk = 'medium' THEN 60 WHEN ep.ai_turnover_risk = 'high' THEN 85 ELSE 50 END)
                from employee_pulses ep join people p on ep.people_id = p.id where p.organization_id = ?) as avg_turnover_risk,
            (select count(distinct people_role_skills.people_id) from people_role_skills join people on people_role_skills.people_id = people.id where people.organization_id = ? and people_role_skills.required_level > 0 and people_role_skills.current_level >= people_role_skills.required_level) as ready_now,
            (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 0) as level_0_count,
            (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 1) as level_1_count,
            (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 2) as level_2_count,
            (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 3) as level_3_count,
            (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 4) as level_4_count,
            (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 5) as level_5_count
SQL;

                        // Now there are 19 placeholders (added ready_now + level counts)
                        $params = array_fill(0, 19, $organizationId);

                $row = DB::selectOne($sql, $params);
                return $row ?: (object)[];
        }

    /**
     * Public accessor that caches executive aggregates per request to avoid duplicate heavy queries.
     */
    public function fetchExecutiveAggregates(int $organizationId): object
    {
        $key = (int) $organizationId;
        if (isset($this->executiveAggregatesCache[$key])) {
            return $this->executiveAggregatesCache[$key];
        }

        // Prefer reading from precomputed aggregates table when available.
        try {
            $agg = ExecutiveAggregate::where('organization_id', $organizationId)
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($agg) {
                // Convert Eloquent model to a lightweight stdClass-like object matching previous shape
                $row = (object) [
                    'headcount' => $agg->headcount ?? null,
                    'total_scenarios' => $agg->total_scenarios ?? null,
                    'upskilled_count' => $agg->upskilled_count ?? $agg->upskilled_count,
                    'avg_gap' => $agg->avg_gap ?? null,
                    'bot_strategies' => $agg->bot_strategies ?? null,
                    'total_pivot_rows' => $agg->total_pivot_rows ?? null,
                    'avg_readiness' => $agg->avg_readiness ?? null,
                    'critical_gaps' => $agg->critical_gaps ?? null,
                    'total_roles' => $agg->total_roles ?? null,
                    'augmented_roles' => $agg->augmented_roles ?? null,
                    'avg_turnover_risk' => $agg->avg_turnover_risk ?? null,
                    'ready_now' => $agg->ready_now ?? null,
                    'level_0_count' => $agg->level_0_count ?? 0,
                    'level_1_count' => $agg->level_1_count ?? 0,
                    'level_2_count' => $agg->level_2_count ?? 0,
                    'level_3_count' => $agg->level_3_count ?? 0,
                    'level_4_count' => $agg->level_4_count ?? 0,
                    'level_5_count' => $agg->level_5_count ?? 0,
                ];
                $this->executiveAggregatesCache[$key] = $row;
                return $row;
            }
        } catch (\Throwable $e) {
            // ignore and fallback to direct query
        }

        $row = $this->getExecutiveAggregates($organizationId);
        $this->executiveAggregatesCache[$key] = $row;
        return $row;
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
        // Prefer using precomputed executive aggregates when available
        try {
            $aggs = $this->fetchExecutiveAggregates($organizationId);
            if (isset($aggs->avg_readiness) && $aggs->avg_readiness !== null) {
                return (float) $aggs->avg_readiness;
            }
        } catch (\Throwable $e) {
            // fallback to direct calculation below
        }
        $key = "avg_readiness_{$organizationId}";
        if (isset($this->memo[$key])) {
            return (float) $this->memo[$key];
        }

        $total = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
            ->where('people.organization_id', $organizationId)
            ->count();

        if ($total === 0) {
            $this->memo[$key] = 0;
            return 0;
        }

        $avgReadiness = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
            ->where('people.organization_id', $organizationId)
            ->selectRaw('AVG(LEAST(1.0, current_level / NULLIF(required_level, 0))) as avg')
            ->value('avg') ?: 0;

        $this->memo[$key] = (float) $avgReadiness;
        return (float) $avgReadiness;
    }

    /**
     * Estimated ROI = (Internal Growth Savings) + (Attrition Prevention Value)
     */
    private function calculateTotalTalentRoi(int $organizationId): float
    {
        // Allow passing precomputed values to avoid duplicate queries
        $args = func_get_args();
        $preUp = $args[1] ?? null;
        $preBot = $args[2] ?? null;

        if ($preUp !== null) {
            $upskilledCount = (int) $preUp;
        } else {
            // Try using executive aggregates first
            try {
                $aggs = $this->fetchExecutiveAggregates($organizationId);
                if (isset($aggs->upskilled_count)) {
                    $upskilledCount = (int) $aggs->upskilled_count;
                } else {
                    $keyUp = "upskilled_count_{$organizationId}";
                    if (isset($this->memo[$keyUp])) {
                        $upskilledCount = (int) $this->memo[$keyUp];
                    } else {
                        $upskilledCount = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
                            ->where('people.organization_id', $organizationId)
                            ->whereColumn('current_level', '>=', 'required_level')
                            ->where('required_level', '>', 0)
                            ->distinct('people_id')
                            ->count();
                        $this->memo[$keyUp] = $upskilledCount;
                    }
                }
            } catch (\Throwable $e) {
                $keyUp = "upskilled_count_{$organizationId}";
                if (isset($this->memo[$keyUp])) {
                    $upskilledCount = (int) $this->memo[$keyUp];
                } else {
                    $upskilledCount = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
                        ->where('people.organization_id', $organizationId)
                        ->whereColumn('current_level', '>=', 'required_level')
                        ->where('required_level', '>', 0)
                        ->distinct('people_id')
                        ->count();
                    $this->memo[$keyUp] = $upskilledCount;
                }
            }
        }

        $upskillingSavings = $upskilledCount * self::AVG_HIRING_COST;

        if ($preBot !== null) {
            $botStrategies = (int) $preBot;
        } else {
            try {
                $aggs = $aggs ?? $this->fetchExecutiveAggregates($organizationId);
                if (isset($aggs->bot_strategies)) {
                    $botStrategies = (int) $aggs->bot_strategies;
                } else {
                    $keyBot = "bot_strategies_{$organizationId}";
                    if (isset($this->memo[$keyBot])) {
                        $botStrategies = (int) $this->memo[$keyBot];
                    } else {
                        $botStrategies = DB::table('scenario_closure_strategies')
                            ->join('scenarios', 'scenario_closure_strategies.scenario_id', '=', 'scenarios.id')
                            ->where('scenarios.organization_id', $organizationId)
                            ->where('strategy', 'bot')
                            ->count();
                        $this->memo[$keyBot] = $botStrategies;
                    }
                }
            } catch (\Throwable $e) {
                $keyBot = "bot_strategies_{$organizationId}";
                if (isset($this->memo[$keyBot])) {
                    $botStrategies = (int) $this->memo[$keyBot];
                } else {
                    $botStrategies = DB::table('scenario_closure_strategies')
                        ->join('scenarios', 'scenario_closure_strategies.scenario_id', '=', 'scenarios.id')
                        ->where('scenarios.organization_id', $organizationId)
                        ->where('strategy', 'bot')
                        ->count();
                    $this->memo[$keyBot] = $botStrategies;
                }
            }
        }

        $aiProductivityValue = $botStrategies * (self::AVG_ANNUAL_SALARY * 0.2); // Assuming 20% efficiency boost

        return round($upskillingSavings + $aiProductivityValue, 2);
    }

    private function calculateCriticalGapRate(int $organizationId): float
    {
        // Prefer executive aggregates when available
        try {
            $aggs = $this->fetchExecutiveAggregates($organizationId);
            if (isset($aggs->total_pivot_rows) && $aggs->total_pivot_rows > 0 && isset($aggs->critical_gaps)) {
                return $aggs->critical_gaps / $aggs->total_pivot_rows;
            }
        } catch (\Throwable $e) {
            // fallback to direct computation below
        }

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

        // If there are no pivot rows, return empty distributions immediately
        try {
            $aggs = $this->fetchExecutiveAggregates($organizationId);
            if (isset($aggs->total_pivot_rows) && $aggs->total_pivot_rows == 0) {
                return ['skill_levels' => collect(), 'department_readiness' => collect()];
            }
        } catch (\Throwable $e) {
            // proceed to compute as usual
        }

        $keySkill = "distribution_skill_levels_{$organizationId}";
        $keyDept = "distribution_department_readiness_{$organizationId}";

        if (! isset($this->memo[$keySkill])) {
            $this->memo[$keySkill] = DB::table('people_role_skills')
                ->join('people', 'people_role_skills.people_id', '=', 'people.id')
                ->where('people.organization_id', $organizationId)
                ->select('current_level', DB::raw('count(*) as count'))
                ->groupBy('current_level')
                ->orderBy('current_level')
                ->get();
        }

        if (! isset($this->memo[$keyDept])) {
            $this->memo[$keyDept] = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
                ->join('departments', 'people.department_id', '=', 'departments.id')
                ->where('people.organization_id', $organizationId)
                ->select('departments.name', DB::raw('AVG(LEAST(1.0, current_level / NULLIF(required_level, 0))) * 100 as readiness'))
                ->groupBy('departments.name')
                ->get();
        }

        return [
            'skill_levels' => $this->memo[$keySkill],
            'department_readiness' => $this->memo[$keyDept],
        ];
    }

    private function calculateCultureHealthScore(int $organizationId): int
    {
        $stats = $this->getPulseSentimentStats($organizationId);

        $avgSentiment = (float) ($stats['recent_avg'] ?? 50);
        $pulseCount = (int) ($stats['recent_count'] ?? 0);

        $score = 50;
        $score += ($avgSentiment - 50) * 0.4;

        $trend = $this->calculateSentimentTrend($organizationId, $stats);
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

    private function calculateSentimentTrend(int $organizationId, array $stats = []): string
    {
        if (empty($stats)) {
            $stats = $this->getPulseSentimentStats($organizationId);
        }

        $recent = (float) ($stats['recent_avg'] ?? 0);
        $previous = (float) ($stats['previous_avg'] ?? 0);

        if ($recent > ($previous + 5)) {
            return 'improving';
        }

        if ($recent < ($previous - 5)) {
            return 'declining';
        }

        return 'stable';
    }

    /**
     * Devuelve estadísticas agregadas de `pulse_responses` en una sola consulta
     */
    private function getPulseSentimentStats(int $organizationId): array
    {
        $recentSince = now()->subDays(15);
        $previousFrom = now()->subDays(30);
        $previousTo = now()->subDays(15);

        $row = PulseResponse::selectRaw(
            'AVG(CASE WHEN pulse_responses.created_at >= ? THEN pulse_responses.sentiment_score END) as recent_avg, '
            . 'AVG(CASE WHEN pulse_responses.created_at BETWEEN ? AND ? THEN pulse_responses.sentiment_score END) as previous_avg, '
            . 'SUM(CASE WHEN pulse_responses.created_at >= ? THEN 1 ELSE 0 END) as recent_count',
            [$recentSince, $previousFrom, $previousTo, $recentSince]
        )
        ->join('people', 'pulse_responses.people_id', '=', 'people.id')
        ->where('people.organization_id', $organizationId)
        ->whereNull('people.deleted_at')
        ->first();

        return [
            'recent_avg' => $row->recent_avg ?? null,
            'previous_avg' => $row->previous_avg ?? null,
            'recent_count' => (int) ($row->recent_count ?? 0),
        ];
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
