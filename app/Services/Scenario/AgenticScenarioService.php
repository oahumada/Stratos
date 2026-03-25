<?php

namespace App\Services\Scenario;

use App\Models\Organization;
use App\Models\People;
use App\Models\Scenario;
use App\Services\AiOrchestratorService;
use App\Services\AuditTrailService;
use App\Services\Intelligence\ImpactEngineService;
use Illuminate\Support\Facades\Log;

/**
 * AgenticScenarioService — Cierra Fase 6 (Escenarios Agénticos).
 *
 * Orquesta simulaciones autónomas de cambios organizacionales
 * usando Gemelos Digitales + Agentes IA para:
 * - Simular fusiones de equipos
 * - Predecir impacto de nuevas tecnologías
 * - Optimizar estructura organizacional
 * - Ejecutar "What-If" agénticos
 */
class AgenticScenarioService
{
    public function __construct(
        protected DigitalTwinService $digitalTwin,
        protected CrisisSimulatorService $crisisSimulator,
        protected CareerPathService $careerPaths,
        protected AiOrchestratorService $orchestrator,
        protected AuditTrailService $audit,
        protected ImpactEngineService $impactEngine
    ) {}

    /**
     * Ejecuta un escenario agéntico completo — el Agente simula,
     * analiza impacto y propone plan de acción autónomamente.
     */
    public function runAgenticSimulation(int $scenarioId, array $params): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        Log::info("AgenticScenario: Iniciando simulación agéntica para Scenario #{$scenarioId}");

        // 1. Capturar el estado actual (Digital Twin)
        $orgId = $scenario->organization_id ?? Organization::first()?->id;
        $currentState = $orgId
            ? $this->digitalTwin->captureState(Organization::findOrFail($orgId))
            : ['nodes' => [], 'edges' => []];

        // 2. Simular el cambio propuesto
        $changeType = $params['change_type'] ?? 'generic';
        $simulationResult = match ($changeType) {
            'team_merge' => $this->simulateTeamMerge($params, $currentState),
            'tech_disruption' => $this->simulateTechDisruption($scenarioId, $params),
            'expansion' => $this->simulateExpansion($params, $currentState, $orgId),
            'downsizing' => $this->simulateDownsizing($scenarioId, $params),
            default => $this->simulateGenericChange($params, $currentState),
        };

        // 3. Calcular impacto en KPIs organizacionales
        $kpiImpact = $this->calculateKpiImpact($simulationResult, $orgId);

        // 4. Generar plan de acción agéntico (IA propone acciones)
        $actionPlan = $this->generateAgenticActionPlan($simulationResult, $kpiImpact);

        // 5. Calcular viabilidad estratégica
        $viability = $this->calculateViability($kpiImpact);

        $result = [
            'scenario_id' => $scenarioId,
            'simulation_type' => $changeType,
            'parameters' => $params,
            'current_state_snapshot' => [
                'total_headcount' => $currentState['org_metadata']['total_headcount'] ?? People::count(),
                'departments' => $currentState['org_metadata']['sectors'] ?? [],
            ],
            'simulation_result' => $simulationResult,
            'kpi_impact' => $kpiImpact,
            'action_plan' => $actionPlan,
            'viability' => $viability,
            'executed_at' => now()->toIso8601String(),
        ];

        $this->audit->logDecision(
            'Agentic Scenario',
            "scenario_{$scenarioId}",
            "Simulación agéntica tipo '{$changeType}' ejecutada",
            $result,
            'Simulador Orgánico'
        );

        return $result;
    }

    /**
     * Genera una vista de "What-If" rápida sin simulación completa.
     */
    public function quickWhatIf(string $question, int $scenarioId): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        $prompt = "Actúa como el Simulador Orgánico de Stratos. Se te plantea el siguiente escenario hipotético:
        
        Contexto del Escenario: {$scenario->name}
        Pregunta What-If: {$question}
        
        Analiza rápidamente:
        1. Impacto probable (positivo/negativo) en el talento
        2. Riesgo principal
        3. Acción inmediata recomendada
        4. Probabilidad de éxito (0-100%)
        
        Responde en formato JSON con: 'impact' (string), 'main_risk' (string), 'immediate_action' (string), 'success_probability' (int).";

        try {
            $result = $this->orchestrator->agentThink('Simulador Orgánico', $prompt);

            return [
                'status' => 'success',
                'question' => $question,
                'analysis' => $result['response'],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'question' => $question,
                'analysis' => [
                    'impact' => 'No se pudo evaluar automáticamente.',
                    'main_risk' => 'Análisis manual requerido.',
                    'immediate_action' => 'Revisar con el equipo de RRHH.',
                    'success_probability' => 50,
                ],
            ];
        }
    }

    // ── Simulation Types ─────────────────────────────────────

    protected function simulateTeamMerge(array $params, array $state): array
    {
        $teamA = $params['team_a_department_id'] ?? null;
        $teamB = $params['team_b_department_id'] ?? null;

        // Usar los nodos capturados en el Digital Twin para contar personas por departamento
        $people = collect($state['nodes']['people'] ?? []);
        $teamAPeople = $people->where('department_id', $teamA)->count();
        $teamBPeople = $people->where('department_id', $teamB)->count();

        $mergedSize = $teamAPeople + $teamBPeople;
        $redundancyRate = $params['expected_redundancy_rate'] ?? 10;
        $potentialRedundancies = (int) ceil($mergedSize * ($redundancyRate / 100));

        // Simular compatibilidad de skills usando el Skill Mesh o nodos de personas
        $skillOverlap = $this->calculateDepartmentSkillOverlapFromState($teamA, $teamB, $state);

        return [
            'type' => 'team_merge',
            'team_a_size' => $teamAPeople,
            'team_b_size' => $teamBPeople,
            'merged_size' => $mergedSize,
            'potential_redundancies' => $potentialRedundancies,
            'skill_overlap_percentage' => $skillOverlap,
            'culture_risk' => $skillOverlap > 60 ? 'low' : ($skillOverlap > 30 ? 'medium' : 'high'),
            'estimated_transition_months' => $mergedSize > 50 ? 6 : 3,
        ];
    }

    protected function simulateTechDisruption(int $scenarioId, array $params): array
    {
        return $this->crisisSimulator->simulateSkillObsolescence($scenarioId, [
            'obsolete_skill_ids' => $params['obsolete_skill_ids'] ?? [],
            'emerging_skills' => $params['emerging_skills'] ?? [],
            'horizon_months' => $params['horizon_months'] ?? 18,
        ]);
    }

    protected function simulateExpansion(array $params, array $state, ?int $orgId = null): array
    {
        $resolvedOrgId = $orgId ?? Organization::first()?->id;
        $benchmarks = $this->impactEngine->getFinancialBenchmarks($resolvedOrgId ?? 1);

        $growthRate = $params['growth_percentage'] ?? 20;
        $currentHeadcount = $state['org_metadata']['total_headcount'] ?? People::count();
        $newPositions = (int) ceil($currentHeadcount * ($growthRate / 100));

        // Mobility map para identificar candidatos internos
        $mobilityData = $resolvedOrgId
            ? $this->careerPaths->generateMobilityMap($resolvedOrgId)
            : ['mobility_index' => 0, 'total_viable_transitions' => 0];

        $internalFillRate = min(100, $mobilityData['mobility_index'] * 1.5);
        $externalHiresNeeded = (int) ceil($newPositions * (1 - ($internalFillRate / 100)));

        return [
            'type' => 'expansion',
            'current_headcount' => $currentHeadcount,
            'growth_rate' => $growthRate,
            'new_positions' => $newPositions,
            'internal_fill_rate' => round($internalFillRate),
            'internal_promotions' => $newPositions - $externalHiresNeeded,
            'external_hires_needed' => $externalHiresNeeded,
            'estimated_recruitment_cost' => $externalHiresNeeded * $benchmarks['avg_recruitment_cost'],
            'estimated_ramp_up_months' => 4,
            'mobility_index' => $mobilityData['mobility_index'],
        ];
    }

    protected function simulateDownsizing(int $scenarioId, array $params): array
    {
        return $this->crisisSimulator->simulateRestructuring($scenarioId, [
            'merge_departments' => $params['merge_departments'] ?? [],
            'eliminate_role_ids' => $params['eliminate_role_ids'] ?? [],
            'new_roles' => $params['new_roles'] ?? [],
        ]);
    }

    protected function simulateGenericChange(array $params, array $state): array
    {
        return [
            'type' => 'generic',
            'description' => $params['description'] ?? 'Cambio organizacional genérico',
            'current_headcount' => $state['org_metadata']['total_headcount'] ?? People::count(),
            'estimated_impact' => 'Requiere análisis detallado',
            'risk_level' => 'medium',
        ];
    }

    // ── Impact Calculation ───────────────────────────────────

    protected function calculateKpiImpact(array $simulationResult, ?int $orgId = null): array
    {
        $resolvedOrgId = $orgId ?? Organization::first()?->id ?? 1;
        $benchmarks = $this->impactEngine->getFinancialBenchmarks($resolvedOrgId);

        $headcountChange = 0;
        $costImpact = 0;
        $productivityImpact = 0;

        $type = $simulationResult['type'] ?? 'generic';

        switch ($type) {
            case 'team_merge':
                $headcountChange = -($simulationResult['potential_redundancies'] ?? 0);
                $costImpact = $headcountChange * -$benchmarks['avg_annual_salary']; // Savings
                $productivityImpact = $simulationResult['skill_overlap_percentage'] > 50 ? 5 : -10;
                break;

            case 'expansion':
                $headcountChange = $simulationResult['new_positions'] ?? 0;
                $costImpact = -($simulationResult['estimated_recruitment_cost'] ?? 0);
                $productivityImpact = 15;
                break;

            case 'skill_obsolescence':
            case 'restructuring':
                $headcountChange = -($simulationResult['impact']['people_at_severance_risk'] ?? 0);
                // Severance cost basado en benchmark real
                $costImpact = -($simulationResult['impact']['people_at_severance_risk'] ?? 0) * ($benchmarks['avg_monthly_salary'] * $benchmarks['avg_severance_multiplier']);
                $productivityImpact = -20;
                break;
        }

        return [
            'headcount_delta' => $headcountChange,
            'cost_impact_usd' => $costImpact,
            'productivity_impact_pct' => $productivityImpact,
            'time_to_stabilize_months' => abs($headcountChange) > 20 ? 12 : 6,
            'risk_index' => min(100, abs($headcountChange) * 3 + abs($productivityImpact)),
        ];
    }

    protected function generateAgenticActionPlan(array $simulationResult, array $kpiImpact): array
    {
        $actions = [];

        // Capa Humana (Individual & Grupal)
        if ($kpiImpact['risk_index'] > 60) {
            $actions[] = [
                'phase' => 'Immediate',
                'category' => 'human',
                'action' => 'Activar protocolo de comunicación interna para gestionar incertidumbre estratégica.',
                'responsible' => 'CHRO',
                'deadline_days' => 7,
            ];

            $actions[] = [
                'phase' => 'Short-term',
                'category' => 'policy',
                'action' => 'Lanzamiento de Plan de Retención Individualizado para talentos en nodos críticos.',
                'responsible' => 'Talent Management',
                'deadline_days' => 15,
            ];
        }

        if ($kpiImpact['headcount_delta'] < 0) {
            $actions[] = [
                'phase' => 'Short-term',
                'category' => 'human',
                'action' => 'Evaluar reasignación interna antes de proceder con desvinculaciones.',
                'responsible' => 'HR Team',
                'deadline_days' => 30,
            ];
        }

        // Intervenciones Organizacionales (Cultura y Clima)
        if ($simulationResult['type'] === 'team_merge' || $simulationResult['type'] === 'downsizing') {
            $actions[] = [
                'phase' => 'Short-term',
                'category' => 'policy',
                'action' => 'Proyecto de mejora de clima organizacional: Talleres de motivación y resiliencia post-cambio.',
                'responsible' => 'Culture Lead',
                'deadline_days' => 20,
            ];

            $actions[] = [
                'phase' => 'Medium-term',
                'category' => 'policy',
                'action' => 'Programa de Mentoring Inter-equipos para acelerar la simbiosis cultural.',
                'responsible' => 'L&D Manager',
                'deadline_days' => 45,
            ];
        }

        // Capa Sintética (Stratos Radar Exclusive)
        if ($kpiImpact['productivity_impact_pct'] < -10 || $kpiImpact['risk_index'] > 50) {
            $actions[] = [
                'phase' => 'Immediate',
                'category' => 'synthetic',
                'action' => 'Desplegar Agente IA de Acompañamiento (Companion) para absorber picos de carga operativa.',
                'responsible' => 'AI Orchestrator',
                'deadline_days' => 3,
            ];

            $actions[] = [
                'phase' => 'Short-term',
                'category' => 'synthetic',
                'action' => 'Instanciar Agentes de Extracción Semántica (DNA Extract) para documentar skills críticas en riesgo.',
                'responsible' => 'Stratos Radar',
                'deadline_days' => 10,
            ];
        }

        if ($simulationResult['type'] === 'expansion') {
            $actions[] = [
                'phase' => 'Short-term',
                'category' => 'synthetic',
                'action' => 'Activar Agente de Reclutamiento Agéntico para filtrar candidatos por "Cultural DNA Fit".',
                'responsible' => 'Talent Acquisition Bot',
                'deadline_days' => 14,
            ];
        }

        $actions[] = [
            'phase' => 'Medium-term',
            'category' => 'hybrid',
            'action' => 'Auditoría de simbiosis Humano-Sintético: Evaluar efectividad de los agentes desplegados.',
            'responsible' => 'Strategic Planning',
            'deadline_days' => 90,
        ];

        return $actions;
    }

    protected function calculateViability(array $kpiImpact): array
    {
        $riskIndex = $kpiImpact['risk_index'] ?? 50;
        $costImpact = $kpiImpact['cost_impact_usd'] ?? 0;

        $viabilityScore = 100 - $riskIndex;
        if ($costImpact < -100000) {
            $viabilityScore -= 15;
        }
        if ($costImpact > 0) {
            $viabilityScore += 10;
        }

        $viabilityScore = max(0, min(100, $viabilityScore));

        return [
            'score' => $viabilityScore,
            'level' => $viabilityScore >= 70 ? 'high' : ($viabilityScore >= 40 ? 'medium' : 'low'),
            'semaphore' => $viabilityScore >= 70 ? 'green' : ($viabilityScore >= 40 ? 'yellow' : 'red'),
            'recommendation' => $viabilityScore >= 70
                ? 'VIABLE — Proceder con implementación planificada.'
                : ($viabilityScore >= 40
                    ? 'CONDICIONAL — Requiere mitigaciones antes de proceder.'
                    : 'NO RECOMENDABLE — Riesgo demasiado alto. Replantear escenario.'),
        ];
    }

    // ── Helpers ───────────────────────────────────────────────

    protected function calculateDepartmentSkillOverlapFromState(?int $deptA, ?int $deptB, array $state): float
    {
        if (! $deptA || ! $deptB || ! isset($state['nodes']['people'])) {
            return 0;
        }

        $people = collect($state['nodes']['people']);

        $skillsA = $people->where('department_id', $deptA)
            ->pluck('skill_ids')
            ->flatten()
            ->unique()
            ->toArray();

        $skillsB = $people->where('department_id', $deptB)
            ->pluck('skill_ids')
            ->flatten()
            ->unique()
            ->toArray();

        $overlap = count(array_intersect($skillsA, $skillsB));
        $total = count(array_unique(array_merge($skillsA, $skillsB)));

        return $total > 0 ? round(($overlap / $total) * 100, 1) : 0;
    }
}
