<?php

namespace App\Services;

use App\Models\Scenario;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\DevelopmentPath;
use App\Services\ScenarioAnalyticsService;
use App\Services\TalentRoiService;
use App\Services\CultureSentinelService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ImpactReportService — Cierra Fase 2 del Roadmap.
 *
 * Genera reportes de impacto y ROI automatizados consolidando
 * todas las métricas de la plataforma en un informe ejecutivo.
 */
class ImpactReportService
{
    public function __construct(
        protected ScenarioAnalyticsService $scenarioAnalytics,
        protected TalentRoiService $roiService,
        protected CultureSentinelService $cultureSentinel,
        protected AiOrchestratorService $orchestrator,
        protected AuditTrailService $audit
    ) {}

    /**
     * Genera un reporte de impacto completo para un escenario.
     */
    public function generateScenarioImpactReport(int $scenarioId): array
    {
        $scenario = Scenario::with(['roles.skills', 'capabilities'])->findOrFail($scenarioId);

        $iq = $this->scenarioAnalytics->calculateScenarioIQ($scenarioId);
        $impact = $this->scenarioAnalytics->calculateImpact($scenarioId);
        $confidence = $this->scenarioAnalytics->getConfidenceScore($scenarioId);

        // Estrategias 4B aplicadas
        $strategyBreakdown = DB::table('scenario_closure_strategies')
            ->where('scenario_id', $scenarioId)
            ->select('strategy', DB::raw('count(*) as count'), DB::raw('AVG(estimated_cost) as avg_cost'))
            ->groupBy('strategy')
            ->get()
            ->keyBy('strategy')
            ->toArray();

        // Brechas por prioridad
        $gapsByPriority = $this->calculateGapsByPriority($scenarioId);

        // Headcount proyectado
        $headcountProjection = $this->projectHeadcount($scenarioId);

        $report = [
            'meta' => [
                'report_type' => 'scenario_impact',
                'scenario_id' => $scenarioId,
                'scenario_name' => $scenario->name,
                'generated_at' => now()->toIso8601String(),
                'version' => '1.0',
            ],
            'executive_summary' => [
                'scenario_iq' => $iq,
                'confidence_score' => $confidence,
                'overall_readiness' => $impact['readiness_percentage'] ?? 0,
                'total_investment_required' => $impact['total_estimated_cost'] ?? 0,
                'estimated_roi' => $impact['estimated_roi'] ?? 0,
                'risk_level' => $impact['risk_level'] ?? 'medium',
            ],
            'impact_analysis' => $impact,
            'strategy_breakdown' => $strategyBreakdown,
            'gaps_by_priority' => $gapsByPriority,
            'headcount_projection' => $headcountProjection,
            'timeline' => $this->generateTimeline($scenarioId, $strategyBreakdown),
            'recommendations' => $this->generateRecommendations($scenario, $impact, $gapsByPriority),
        ];

        // Audit Trail
        $this->audit->logDecision(
            'Impact Report',
            "scenario_{$scenarioId}",
            "Reporte de impacto generado automáticamente",
            ['report_hash' => md5(json_encode($report))],
            'ImpactReportService'
        );

        Log::info("ImpactReport generado para Scenario #{$scenarioId}");

        return $report;
    }

    /**
     * Genera un reporte de ROI organizacional global.
     */
    public function generateOrganizationalRoiReport(): array
    {
        $executiveSummary = $this->roiService->getExecutiveSummary();
        $distributions = $this->roiService->getDistributionData();

        // Escenarios activos con sus métricas
        $activeScenarios = Scenario::where('status', 'active')
            ->orWhere('status', 'published')
            ->get()
            ->map(function ($scenario) {
                return [
                    'id' => $scenario->id,
                    'name' => $scenario->name,
                    'iq' => $this->scenarioAnalytics->calculateScenarioIQ($scenario->id),
                    'impact' => $this->scenarioAnalytics->calculateImpact($scenario->id),
                ];
            });

        // Learning Paths en progreso
        $learningProgress = DevelopmentPath::where('status', 'active')
            ->with('actions')
            ->get()
            ->map(function ($path) {
                $totalActions = $path->actions->count();
                $completedActions = $path->actions->where('status', 'completed')->count();
                return [
                    'path_id' => $path->id,
                    'title' => $path->action_title,
                    'progress' => $totalActions > 0 ? round(($completedActions / $totalActions) * 100) : 0,
                ];
            });

        // Inversión total en talento
        $totalInvestment = DB::table('scenario_closure_strategies')
            ->sum('estimated_cost') ?: 0;

        $savingsFromUpskilling = PeopleRoleSkills::whereColumn('current_level', '>=', 'required_level')
            ->where('required_level', '>', 0)
            ->distinct('people_id')
            ->count() * TalentRoiService::AVG_HIRING_COST;

        return [
            'meta' => [
                'report_type' => 'organizational_roi',
                'generated_at' => now()->toIso8601String(),
                'version' => '1.0',
            ],
            'kpis' => $executiveSummary,
            'roi_metrics' => [
                'total_talent_investment' => $totalInvestment,
                'savings_from_upskilling' => $savingsFromUpskilling,
                'net_roi' => $savingsFromUpskilling - $totalInvestment,
                'roi_ratio' => $totalInvestment > 0 ? round($savingsFromUpskilling / $totalInvestment, 2) : 0,
            ],
            'active_scenarios' => $activeScenarios,
            'learning_progress' => $learningProgress,
            'distributions' => $distributions,
            'talent_pipeline' => $this->getTalentPipelineMetrics(),
        ];
    }

    /**
     * Genera un informe completo consolidado (todos los módulos).
     */
    public function generateConsolidatedReport(?int $organizationId = null): array
    {
        $scenarioReport = null;
        $latestScenario = Scenario::latest()->first();
        if ($latestScenario) {
            $scenarioReport = $this->generateScenarioImpactReport($latestScenario->id);
        }

        $roiReport = $this->generateOrganizationalRoiReport();

        return [
            'meta' => [
                'report_type' => 'consolidated',
                'generated_at' => now()->toIso8601String(),
                'version' => '1.0',
            ],
            'scenario_impact' => $scenarioReport,
            'organizational_roi' => $roiReport,
            'strategic_recommendations' => $this->generateStrategicInsights($scenarioReport, $roiReport),
        ];
    }

    // ── Helpers ──────────────────────────────────────────────

    protected function calculateGapsByPriority(int $scenarioId): array
    {
        $gaps = DB::table('scenario_role_skills')
            ->join('skills', 'scenario_role_skills.skill_id', '=', 'skills.id')
            ->where('scenario_role_skills.scenario_id', $scenarioId)
            ->select(
                'skills.name as skill_name',
                DB::raw('(required_level - COALESCE(current_level, 0)) as gap_size'),
                DB::raw("CASE
                    WHEN (required_level - COALESCE(current_level, 0)) >= 3 THEN 'critical'
                    WHEN (required_level - COALESCE(current_level, 0)) = 2 THEN 'high'
                    WHEN (required_level - COALESCE(current_level, 0)) = 1 THEN 'medium'
                    ELSE 'low'
                END as priority")
            )
            ->orderByDesc('gap_size')
            ->get();

        return [
            'critical' => $gaps->where('priority', 'critical')->count(),
            'high' => $gaps->where('priority', 'high')->count(),
            'medium' => $gaps->where('priority', 'medium')->count(),
            'low' => $gaps->where('priority', 'low')->count(),
            'details' => $gaps->take(10)->toArray(),
        ];
    }

    protected function projectHeadcount(int $scenarioId): array
    {
        $currentHeadcount = People::count();
        $buyStrategies = DB::table('scenario_closure_strategies')
            ->where('scenario_id', $scenarioId)
            ->where('strategy', 'buy')
            ->count();

        return [
            'current' => $currentHeadcount,
            'projected_additions' => $buyStrategies,
            'projected_total' => $currentHeadcount + $buyStrategies,
            'growth_rate' => $currentHeadcount > 0
                ? round(($buyStrategies / $currentHeadcount) * 100, 1)
                : 0,
        ];
    }

    protected function generateTimeline(int $scenarioId, $strategies): array
    {
        $phases = [];
        $monthOffset = 0;

        // Fase 1: Quick Wins (Bot + Borrow strategies — immediate)
        $quickWins = ($strategies['bot']->count ?? 0) + ($strategies['borrow']->count ?? 0);
        if ($quickWins > 0) {
            $phases[] = [
                'phase' => 'Quick Wins',
                'start_month' => 1,
                'end_month' => 3,
                'actions' => $quickWins,
                'type' => 'immediate',
            ];
            $monthOffset = 3;
        }

        // Fase 2: Build strategies (medium-term)
        $buildCount = $strategies['build']->count ?? 0;
        if ($buildCount > 0) {
            $phases[] = [
                'phase' => 'Internal Development',
                'start_month' => $monthOffset + 1,
                'end_month' => $monthOffset + 6,
                'actions' => $buildCount,
                'type' => 'build',
            ];
            $monthOffset += 6;
        }

        // Fase 3: Buy strategies (recruitment)
        $buyCount = $strategies['buy']->count ?? 0;
        if ($buyCount > 0) {
            $phases[] = [
                'phase' => 'Talent Acquisition',
                'start_month' => 1,
                'end_month' => 4,
                'actions' => $buyCount,
                'type' => 'buy',
            ];
        }

        return $phases;
    }

    protected function generateRecommendations(Scenario $scenario, array $impact, array $gaps): array
    {
        $recommendations = [];

        if (($gaps['critical'] ?? 0) > 3) {
            $recommendations[] = [
                'priority' => 'critical',
                'action' => 'Iniciar programa de reskilling urgente para las ' . $gaps['critical'] . ' brechas críticas.',
                'impact' => 'Reducción de riesgo operacional en 30-40%',
            ];
        }

        if (($impact['readiness_percentage'] ?? 0) < 60) {
            $recommendations[] = [
                'priority' => 'high',
                'action' => 'Activar estrategia BUY para roles clave con readiness < 50%.',
                'impact' => 'Aceleración de time-to-readiness en 60%',
            ];
        }

        if (($gaps['high'] ?? 0) > 5) {
            $recommendations[] = [
                'priority' => 'medium',
                'action' => 'Implementar programa de mentoría cruzada para brechas de nivel 2.',
                'impact' => 'Mejora de cobertura de skills en 25%',
            ];
        }

        $recommendations[] = [
            'priority' => 'standard',
            'action' => 'Programar revisión de impacto en 90 días para evaluar progreso.',
            'impact' => 'Aseguramiento de ciclo de mejora continua',
        ];

        return $recommendations;
    }

    protected function getTalentPipelineMetrics(): array
    {
        $totalPeople = People::count();
        $withPaths = DevelopmentPath::distinct('people_id')->count('people_id');
        $readyNow = PeopleRoleSkills::whereColumn('current_level', '>=', 'required_level')
            ->where('required_level', '>', 0)
            ->distinct('people_id')
            ->count();

        return [
            'total_workforce' => $totalPeople,
            'in_development' => $withPaths,
            'ready_now' => $readyNow,
            'pipeline_coverage' => $totalPeople > 0
                ? round(($withPaths / $totalPeople) * 100, 1)
                : 0,
        ];
    }

    protected function generateStrategicInsights(
        ?array $scenarioReport,
        array $roiReport
    ): array {
        $insights = [];

        $netRoi = $roiReport['roi_metrics']['net_roi'] ?? 0;
        if ($netRoi > 0) {
            $insights[] = [
                'type' => 'positive',
                'insight' => "La inversión en talento está generando un retorno neto positivo de \${$netRoi} USD.",
            ];
        } else {
            $insights[] = [
                'type' => 'warning',
                'insight' => 'La inversión en talento aún no genera retorno positivo. Considerar optimizar la distribución de estrategias 4B.',
            ];
        }

        $pipelineCoverage = $roiReport['talent_pipeline']['pipeline_coverage'] ?? 0;
        if ($pipelineCoverage < 50) {
            $insights[] = [
                'type' => 'action_needed',
                'insight' => "Solo el {$pipelineCoverage}% del workforce tiene planes de desarrollo activos. Meta: >70%.",
            ];
        }

        return $insights;
    }
}
