<?php

namespace App\Services\Scenario;

use App\Models\Organization;
use App\Models\People;
use App\Models\Scenario;
use App\Services\AiOrchestratorService;
use App\Services\AuditTrailService;
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
        protected AuditTrailService $audit
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
            'team_merge' => $this->simulateTeamMerge($scenarioId, $params, $currentState),
            'tech_disruption' => $this->simulateTechDisruption($scenarioId, $params),
            'expansion' => $this->simulateExpansion($scenarioId, $params, $currentState),
            'downsizing' => $this->simulateDownsizing($scenarioId, $params),
            default => $this->simulateGenericChange($scenarioId, $params, $currentState),
        };

        // 3. Calcular impacto en KPIs organizacionales
        $kpiImpact = $this->calculateKpiImpact($currentState, $simulationResult);

        // 4. Generar plan de acción agéntico (IA propone acciones)
        $actionPlan = $this->generateAgenticActionPlan($scenario, $simulationResult, $kpiImpact);

        // 5. Calcular viabilidad estratégica
        $viability = $this->calculateViability($simulationResult, $kpiImpact);

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

    protected function simulateTeamMerge(int $scenarioId, array $params, array $state): array
    {
        $teamA = $params['team_a_department_id'] ?? null;
        $teamB = $params['team_b_department_id'] ?? null;

        $teamAPeople = People::where('department_id', $teamA)->count();
        $teamBPeople = People::where('department_id', $teamB)->count();

        $mergedSize = $teamAPeople + $teamBPeople;
        $redundancyRate = $params['expected_redundancy_rate'] ?? 10;
        $potentialRedundancies = (int) ceil($mergedSize * ($redundancyRate / 100));

        // Simular compatibilidad de skills
        $skillOverlap = $this->calculateDepartmentSkillOverlap($teamA, $teamB);

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

    protected function simulateExpansion(int $scenarioId, array $params, array $state): array
    {
        $growthRate = $params['growth_percentage'] ?? 20;
        $currentHeadcount = $state['org_metadata']['total_headcount'] ?? People::count();
        $newPositions = (int) ceil($currentHeadcount * ($growthRate / 100));

        // Mobility map para identificar candidatos internos
        $orgId = Organization::first()?->id;
        $mobilityData = $orgId
            ? $this->careerPaths->generateMobilityMap($orgId)
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
            'estimated_recruitment_cost' => $externalHiresNeeded * 4500,
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

    protected function simulateGenericChange(int $scenarioId, array $params, array $state): array
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

    protected function calculateKpiImpact(array $currentState, array $simulation): array
    {
        $headcountChange = 0;
        $costImpact = 0;
        $productivityImpact = 0;

        $type = $simulation['type'] ?? 'generic';

        switch ($type) {
            case 'team_merge':
                $headcountChange = -($simulation['potential_redundancies'] ?? 0);
                $costImpact = $headcountChange * -45000; // Savings
                $productivityImpact = $simulation['skill_overlap_percentage'] > 50 ? 5 : -10;
                break;

            case 'expansion':
                $headcountChange = $simulation['new_positions'] ?? 0;
                $costImpact = -($simulation['estimated_recruitment_cost'] ?? 0);
                $productivityImpact = 15;
                break;

            case 'skill_obsolescence':
            case 'restructuring':
                $headcountChange = -($simulation['impact']['people_at_severance_risk'] ?? 0);
                $costImpact = -($simulation['impact']['estimated_severance_cost'] ?? 0);
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

    protected function generateAgenticActionPlan(Scenario $scenario, array $simulation, array $kpiImpact): array
    {
        $actions = [];

        if ($kpiImpact['risk_index'] > 60) {
            $actions[] = [
                'phase' => 'Immediate',
                'action' => 'Activar protocolo de comunicación interna para gestionar incertidumbre.',
                'responsible' => 'CHRO',
                'deadline_days' => 7,
            ];
        }

        if ($kpiImpact['headcount_delta'] < 0) {
            $actions[] = [
                'phase' => 'Short-term',
                'action' => 'Evaluar reasignación interna antes de proceder con desvinculaciones.',
                'responsible' => 'HR Team',
                'deadline_days' => 30,
            ];
            $actions[] = [
                'phase' => 'Short-term',
                'action' => 'Activar programa de outplacement para personas afectadas.',
                'responsible' => 'HR Team',
                'deadline_days' => 45,
            ];
        }

        if ($kpiImpact['headcount_delta'] > 0) {
            $actions[] = [
                'phase' => 'Short-term',
                'action' => 'Activar pipeline de reclutamiento con énfasis en skills emergentes.',
                'responsible' => 'Talent Acquisition',
                'deadline_days' => 14,
            ];
        }

        $actions[] = [
            'phase' => 'Medium-term',
            'action' => 'Ejecutar re-evaluación de escenario a los 90 días para medir impacto real.',
            'responsible' => 'Strategic Planning',
            'deadline_days' => 90,
        ];

        return $actions;
    }

    protected function calculateViability(array $simulation, array $kpiImpact): array
    {
        $riskIndex = $kpiImpact['risk_index'] ?? 50;
        $costImpact = $kpiImpact['cost_impact_usd'] ?? 0;

        $viabilityScore = 100 - $riskIndex;
        if ($costImpact < -100000) $viabilityScore -= 15;
        if ($costImpact > 0) $viabilityScore += 10;

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

    protected function calculateDepartmentSkillOverlap(?int $deptA, ?int $deptB): float
    {
        if (!$deptA || !$deptB) return 0;

        $skillsA = \DB::table('people_role_skills')
            ->join('people', 'people.id', '=', 'people_role_skills.people_id')
            ->where('people.department_id', $deptA)
            ->distinct()
            ->pluck('skill_id')
            ->toArray();

        $skillsB = \DB::table('people_role_skills')
            ->join('people', 'people.id', '=', 'people_role_skills.people_id')
            ->where('people.department_id', $deptB)
            ->distinct()
            ->pluck('skill_id')
            ->toArray();

        $overlap = count(array_intersect($skillsA, $skillsB));
        $total = count(array_unique(array_merge($skillsA, $skillsB)));

        return $total > 0 ? round(($overlap / $total) * 100, 1) : 0;
    }
}
