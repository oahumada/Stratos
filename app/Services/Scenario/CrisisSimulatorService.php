<?php

namespace App\Services\Scenario;

use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Services\AiOrchestratorService;
use App\Services\AuditTrailService;

/**
 * CrisisSimulatorService — Cierra C2 de Fase 3 del Roadmap.
 *
 * Simula escenarios de crisis organizacional (war-gaming):
 * - Retiro masivo / attrition cascading
 * - Obsolescencia tecnológica
 * - Disrupciones de mercado
 * - Restructuración departamental
 */
class CrisisSimulatorService
{
    public function __construct(
        protected DigitalTwinService $digitalTwin,
        protected AiOrchestratorService $orchestrator,
        protected AuditTrailService $audit
    ) {}

    /**
     * Simula una crisis de retiro masivo y su impacto cascada.
     */
    public function simulateMassAttrition(int $scenarioId, array $params, array $state = []): array
    {
        $attritionRate = $params['attrition_rate'] ?? 15; // % de la fuerza laboral
        $targetDepartments = $params['departments'] ?? [];

        // Obtener personas en riesgo
        $query = People::with(['role', 'skills']);
        if (! empty($targetDepartments)) {
            $query->whereIn('department_id', $targetDepartments);
        }
        $workforce = $query->get();

        $totalAtRisk = (int) ceil($workforce->count() * ($attritionRate / 100));

        // Simular quiénes se irían (priorizar por tenure bajo, sin desarrollo activo)
        $attritionCandidates = $workforce->sortBy(function ($person) {
            $tenureMonths = $person->hired_at
                ? now()->diffInMonths($person->hired_at)
                : 24;
            $hasDevelopment = $person->developmentPaths()->where('status', 'active')->exists();

            return $tenureMonths + ($hasDevelopment ? 100 : 0); // Priorizar sin desarrollo
        })->take($totalAtRisk);

        // Calcular impacto en skills y sucesión
        $skillsImpact = $this->calculateSkillsImpact($attritionCandidates);
        $successionImpact = $this->calculateSuccessionImpact($attritionCandidates, $state);

        // --- NEW: CASCADING IMPACT (Gaph Based) ---
        // Si un manager se va, el riesgo de sus reportes aumenta
        $cascadingRisk = $this->calculateCascadingImpact($attritionCandidates, $state);

        // Costo total estimado del attrition
        $replacementCost = $totalAtRisk * 45000 * 0.5; // 50% de salario promedio

        $simulation = [
            'crisis_type' => 'mass_attrition',
            'scenario_id' => $scenarioId,
            'parameters' => $params,
            'impact' => [
                'total_workforce' => $workforce->count(),
                'people_at_risk' => $totalAtRisk,
                'attrition_rate' => $attritionRate,
                'replacement_cost_usd' => $replacementCost,
                'time_to_recover_months' => $this->estimateRecoveryTime($totalAtRisk, $skillsImpact),
                'skills_impact' => $skillsImpact,
                'succession_impact' => $successionImpact,
                'cascading_risk_count' => count($cascadingRisk),
                'critical_roles_exposed' => $successionImpact['exposed_roles'] ?? 0,
            ],
            'cascading_details' => array_slice($cascadingRisk, 0, 10),
            'mitigation_strategies' => $this->generateAttritionMitigations($totalAtRisk, $skillsImpact, $replacementCost),
            'risk_score' => $this->calculateCrisisRiskScore($totalAtRisk, $workforce->count(), $skillsImpact) + (count($cascadingRisk) > 0 ? 10 : 0),
        ];

        $this->audit->logDecision(
            'Crisis Simulator',
            "scenario_{$scenarioId}",
            "Simulación de attrition masivo ({$attritionRate}%)",
            $simulation,
            'Crisis Simulator'
        );

        return $simulation;
    }

    /**
     * Simula obsolescencia tecnológica — skills que se vuelven irrelevantes.
     */
    public function simulateSkillObsolescence(int $scenarioId, array $params): array
    {
        $obsoleteSkillIds = $params['obsolete_skill_ids'] ?? [];
        $emergingSkillNames = $params['emerging_skills'] ?? [];
        $horizonMonths = $params['horizon_months'] ?? 18;

        // Personas afectadas por obsolescencia
        $affectedPeople = PeopleRoleSkills::whereIn('skill_id', $obsoleteSkillIds)
            ->with(['person', 'skill'])
            ->get();

        $uniqueAffected = $affectedPeople->pluck('people_id')->unique()->count();

        // Impacto por departamento
        $departmentImpact = $affectedPeople->groupBy(function ($prs) {
            return $prs->person->department_id ?? 'unknown';
        })->map(function ($group) {
            return [
                'people_affected' => $group->pluck('people_id')->unique()->count(),
                'avg_current_level' => round($group->avg('current_level'), 1),
            ];
        });

        // Costo de reskilling
        $reskillCostPerPerson = 3500; // Promedio industria
        $totalReskillCost = $uniqueAffected * $reskillCostPerPerson;

        $simulation = [
            'crisis_type' => 'skill_obsolescence',
            'scenario_id' => $scenarioId,
            'parameters' => $params,
            'impact' => [
                'obsolete_skills_count' => count($obsoleteSkillIds),
                'people_affected' => $uniqueAffected,
                'department_impact' => $departmentImpact,
                'total_reskill_cost_usd' => $totalReskillCost,
                'reskill_duration_months' => min($horizonMonths, 12),
                'emerging_skills_needed' => $emergingSkillNames,
            ],
            'transition_plan' => [
                'phase_1' => [
                    'name' => 'Assessment & Mapping',
                    'duration_weeks' => 4,
                    'actions' => ['Evaluar nivel actual de cada persona en skills emergentes', 'Mapear transferibilidad de skills obsoletas'],
                ],
                'phase_2' => [
                    'name' => 'Accelerated Reskilling',
                    'duration_weeks' => 16,
                    'actions' => ['Programa intensivo de capacitación', 'Mentoría con expertos externos (Borrow)'],
                ],
                'phase_3' => [
                    'name' => 'Applied Learning',
                    'duration_weeks' => 12,
                    'actions' => ['Proyectos prácticos con nuevas skills', 'Evaluación de competencia alcanzada'],
                ],
            ],
            'risk_score' => min(100, ($uniqueAffected / max(People::count(), 1)) * 200),
        ];

        $this->audit->logDecision(
            'Crisis Simulator',
            "scenario_{$scenarioId}",
            'Simulación de obsolescencia tecnológica',
            $simulation,
            'Crisis Simulator'
        );

        return $simulation;
    }

    /**
     * Simula restructuración departamental.
     */
    public function simulateRestructuring(int $scenarioId, array $params): array
    {
        $mergeDepartments = $params['merge_departments'] ?? [];
        $eliminateRoles = $params['eliminate_role_ids'] ?? [];
        $newRoles = $params['new_roles'] ?? [];

        $affectedByMerge = People::whereIn('department_id', $mergeDepartments)->count();
        $affectedByElimination = People::whereIn('role_id', $eliminateRoles)->count();

        // Personas reasignables (tienen skills transferibles)
        $reasignablePeople = People::whereIn('role_id', $eliminateRoles)
            ->whereHas('skills', function ($q) {
                $q->where('current_level', '>=', 3);
            })
            ->count();

        $simulation = [
            'crisis_type' => 'restructuring',
            'scenario_id' => $scenarioId,
            'parameters' => $params,
            'impact' => [
                'people_affected_by_merge' => $affectedByMerge,
                'people_affected_by_role_elimination' => $affectedByElimination,
                'people_reasignable' => $reasignablePeople,
                'people_at_severance_risk' => max(0, $affectedByElimination - $reasignablePeople),
                'new_roles_to_fill' => count($newRoles),
                'estimated_severance_cost' => max(0, $affectedByElimination - $reasignablePeople) * 15000,
                'estimated_transition_months' => 6,
            ],
            'feasibility_score' => $this->calculateRestructuringFeasibility($reasignablePeople, $affectedByElimination),
            'recommendations' => [
                'Priorizar reasignación interna sobre severance',
                'Implementar programa de transición de 90 días',
                'Activar mentoring cruzado entre departamentos fusionados',
            ],
        ];

        return $simulation;
    }

    // ── Helpers ──────────────────────────────────────────────

    protected function calculateSkillsImpact($attritionCandidates): array
    {
        $skillsLost = [];
        foreach ($attritionCandidates as $person) {
            $personSkills = PeopleRoleSkills::where('people_id', $person->id)
                ->with('skill')
                ->where('current_level', '>=', 3)
                ->get();

            foreach ($personSkills as $ps) {
                $skillName = $ps->skill->name ?? 'Unknown';
                if (! isset($skillsLost[$skillName])) {
                    $skillsLost[$skillName] = ['count' => 0, 'avg_level' => 0, 'levels' => []];
                }
                $skillsLost[$skillName]['count']++;
                $skillsLost[$skillName]['levels'][] = $ps->current_level;
            }
        }

        // Calcular promedio
        foreach ($skillsLost as &$data) {
            $data['avg_level'] = round(array_sum($data['levels']) / count($data['levels']), 1);
            unset($data['levels']);
        }

        // Ordenar por impacto
        uasort($skillsLost, fn ($a, $b) => $b['count'] <=> $a['count']);

        return array_slice($skillsLost, 0, 10, true);
    }

    protected function calculateSuccessionImpact($attritionCandidates, array $state = []): array
    {
        $exposedRoles = 0;
        $criticalNodes = [];

        foreach ($attritionCandidates as $person) {
            if (! $person->role) {
                continue;
            }

            // Si tenemos el estado del gemelo digital, usamos el skill_mesh para ver quién más tiene esos skills
            $alternativesCount = 0;
            if (! empty($state)) {
                $roleSkills = collect($state['nodes']['roles'])->where('id', $person->role_id)->first()['required_skills'] ?? [];
                $alternativesCount = collect($state['nodes']['people'])
                    ->where('id', '!=', $person->id)
                    ->filter(function ($p) use ($roleSkills) {
                        return count(array_intersect($p['skills'], $roleSkills)) >= count($roleSkills) * 0.7;
                    })->count();
            } else {
                $alternativesCount = People::where('role_id', $person->role_id)
                    ->where('id', '!=', $person->id)
                    ->count();
            }

            if ($alternativesCount === 0) {
                $exposedRoles++;
                $criticalNodes[] = [
                    'person' => $person->full_name ?? $person->name,
                    'role' => $person->role->name,
                    'alternatives' => 0,
                    'severity' => 'critical',
                ];
            }
        }

        return [
            'exposed_roles' => $exposedRoles,
            'critical_nodes' => array_slice($criticalNodes, 0, 5),
        ];
    }

    protected function calculateCascadingImpact($attritionCandidates, array $state): array
    {
        if (empty($state) || ! isset($state['edges']['hierarchies'])) {
            return [];
        }

        $attritionIds = $attritionCandidates->pluck('id')->toArray();
        $hierarchies = collect($state['edges']['hierarchies']);
        $cascadingRisks = [];

        foreach ($attritionIds as $managerId) {
            // Buscar reportes directos en el grafo
            $reports = $hierarchies->where('source', $managerId)->pluck('target');

            foreach ($reports as $subordinateId) {
                if (! in_array($subordinateId, $attritionIds)) {
                    $subordinate = collect($state['nodes']['people'])->where('id', $subordinateId)->first();
                    $cascadingRisks[] = [
                        'person_id' => $subordinateId,
                        'name' => $subordinate['name'] ?? "ID $subordinateId",
                        'reason' => 'Perdida de manager directo',
                        'risk_increase' => 25,
                    ];
                }
            }
        }

        return $cascadingRisks;
    }

    protected function estimateRecoveryTime(int $attritionCount, array $skillsImpact): int
    {
        $avgImpact = count($skillsImpact) > 0
            ? collect($skillsImpact)->avg('count')
            : 0;

        if ($attritionCount > 20 || $avgImpact > 5) {
            return 18;
        }
        if ($attritionCount > 10 || $avgImpact > 3) {
            return 12;
        }
        if ($attritionCount > 5) {
            return 6;
        }

        return 3;
    }

    protected function generateAttritionMitigations(int $totalAtRisk, array $skillsImpact, float $cost): array
    {
        $mitigations = [];

        if ($totalAtRisk > 10) {
            $mitigations[] = [
                'strategy' => 'retention',
                'action' => 'Programa de retención preventiva: bonos, planes de carrera, engagement',
                'cost_estimate' => $totalAtRisk * 5000,
                'impact' => 'Reducción de attrition en 30-40%',
            ];
        }

        if (count($skillsImpact) > 3) {
            $mitigations[] = [
                'strategy' => 'build',
                'action' => 'Programa acelerado de cross-training para skills críticas',
                'cost_estimate' => count($skillsImpact) * 8000,
                'impact' => 'Redundancia de skills en 60 días',
            ];
        }

        $mitigations[] = [
            'strategy' => 'buy',
            'action' => 'Pipeline de reclutamiento pre-activado para roles críticos',
            'cost_estimate' => $cost * 0.3,
            'impact' => 'Reducción de time-to-hire de 45 a 20 días',
        ];

        $mitigations[] = [
            'strategy' => 'bot',
            'action' => 'Automatización de tareas operativas para reducir dependencia de headcount',
            'cost_estimate' => 25000,
            'impact' => 'Absorción de 20% de la carga perdida',
        ];

        return $mitigations;
    }

    protected function calculateCrisisRiskScore(int $atRisk, int $total, array $skillsImpact): int
    {
        $attritionRatio = $total > 0 ? ($atRisk / $total) * 100 : 0;
        $skillsConcentration = count($skillsImpact);

        $score = ($attritionRatio * 2) + ($skillsConcentration * 5);

        return min(100, max(0, (int) round($score)));
    }

    protected function calculateRestructuringFeasibility(int $reasignable, int $affected): int
    {
        if ($affected === 0) {
            return 100;
        }

        return min(100, (int) round(($reasignable / $affected) * 100));
    }
}
