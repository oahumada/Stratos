<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StrategicPlanningScenarios;
use App\Http\Requests\StoreStrategicPlanningScenariosRequest;
use App\Http\Requests\UpdateStrategicPlanningScenariosRequest;
use App\Repositories\WorkforcePlanningRepository;
use App\Services\WorkforcePlanningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkforcePlanningController extends Controller
{
    protected WorkforcePlanningRepository $repository;
    protected WorkforcePlanningService $service;

    public function __construct(
        WorkforcePlanningRepository $repository,
        WorkforcePlanningService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Listar scenarios por organización
     * GET /api/v1/workforce-planning/scenarios
     */
    public function listScenarios(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $filters = [
            'status' => $request->input('status'),
            'fiscal_year' => $request->input('fiscal_year'),
        ];

        $scenarios = $this->repository->getScenariosByOrganization($organizationId, $filters);

        return response()->json([
            'success' => true,
            'data' => $scenarios->items(),
            'pagination' => [
                'current_page' => $scenarios->currentPage(),
                'total' => $scenarios->total(),
                'per_page' => $scenarios->perPage(),
                'last_page' => $scenarios->lastPage(),
            ],
        ]);
    }

    /**
     * Obtener scenario con todas sus relaciones
     * GET /api/v1/workforce-planning/scenarios/{id}
     */
    public function showScenario($id): JsonResponse
    {
        $scenario = $this->repository->getScenarioById($id);

        if (!$scenario) {
            return response()->json([
                'success' => false,
                'message' => 'Scenario not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $scenario,
        ]);
    }

    /**
     * Crear nuevo scenario
     * POST /api/v1/workforce-planning/scenarios
     */
    public function createScenario(StoreStrategicPlanningScenariosRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['organization_id'] = auth()->user()->organization_id;
        $data['created_by'] = auth()->id();

        $scenario = $this->repository->createScenario($data);

        return response()->json([
            'success' => true,
            'message' => 'Scenario created successfully',
            'data' => $scenario,
        ], 201);
    }

    /**
     * Actualizar scenario
     * PUT /api/v1/workforce-planning/scenarios/{id}
     */
    public function updateScenario($id, UpdateStrategicPlanningScenariosRequest $request): JsonResponse
    {
        $scenario = StrategicPlanningScenarios::find($id);

        if (!$scenario) {
            return response()->json([
                'success' => false,
                'message' => 'Scenario not found',
            ], 404);
        }

        $data = $request->validated();
        $updatedScenario = $this->repository->updateScenario($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Scenario updated successfully',
            'data' => $updatedScenario,
        ]);
    }

    /**
     * Aprobar scenario
     * POST /api/v1/workforce-planning/scenarios/{id}/approve
     */
    public function approveScenario($id): JsonResponse
    {
        $scenario = StrategicPlanningScenarios::find($id);

        if (!$scenario) {
            return response()->json([
                'success' => false,
                'message' => 'Scenario not found',
            ], 404);
        }

        $scenario->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Scenario approved successfully',
            'data' => $scenario,
        ]);
    }

    /**
     * Eliminar scenario
     * DELETE /api/v1/workforce-planning/scenarios/{id}
     */
    public function deleteScenario($id): JsonResponse
    {
        $deleted = $this->repository->deleteScenario($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Scenario not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Scenario deleted successfully',
        ]);
    }

    /**
     * Obtener role forecasts de un scenario
     * GET /api/v1/workforce-planning/scenarios/{id}/role-forecasts
     */
    public function getScenarioForecasts($scenarioId): JsonResponse
    {
        $forecasts = $this->repository->getForecastsByScenario($scenarioId);

        return response()->json([
            'success' => true,
            'data' => $forecasts,
        ]);
    }

    /**
     * Obtener matches de un scenario
     * GET /api/v1/workforce-planning/scenarios/{id}/matches
     */
    public function getScenarioMatches($scenarioId, Request $request): JsonResponse
    {
        $filters = [
            'readiness_level' => $request->input('readiness_level'),
            'min_score' => $request->input('min_score'),
            'max_risk' => $request->input('max_risk'),
        ];

        $matches = $this->repository->getMatchesByScenario($scenarioId, $filters);

        return response()->json([
            'success' => true,
            'data' => $matches->items(),
            'pagination' => [
                'current_page' => $matches->currentPage(),
                'total' => $matches->total(),
                'per_page' => $matches->perPage(),
                'last_page' => $matches->lastPage(),
            ],
        ]);
    }

    /**
     * Obtener skill gaps de un scenario
     * GET /api/v1/workforce-planning/scenarios/{id}/skill-gaps
     */
    public function getScenarioSkillGaps($scenarioId, Request $request): JsonResponse
    {
        $filters = [
            'priority' => $request->input('priority'),
            'department_id' => $request->input('department_id'),
        ];

        $gaps = $this->repository->getSkillGapsByScenario($scenarioId, $filters);

        return response()->json([
            'success' => true,
            'data' => $gaps->items(),
            'pagination' => [
                'current_page' => $gaps->currentPage(),
                'total' => $gaps->total(),
                'per_page' => $gaps->perPage(),
                'last_page' => $gaps->lastPage(),
            ],
        ]);
    }

    /**
     * Obtener succession plans de un scenario
     * GET /api/v1/workforce-planning/scenarios/{id}/succession-plans
     */
    public function getScenarioSuccessionPlans($scenarioId): JsonResponse
    {
        $plans = $this->repository->getSuccessionPlansByScenario($scenarioId);

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    /**
     * Obtener analytics de un scenario
     * GET /api/v1/workforce-planning/scenarios/{id}/analytics
     */
    public function getScenarioAnalytics($scenarioId): JsonResponse
    {
        $analytics = $this->repository->getAnalyticsByScenario($scenarioId);

        if (!$analytics) {
            return response()->json([
                'success' => false,
                'message' => 'No analytics available. Run analysis first.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Ejecutar análisis completo (calcula matching, skill gaps, analytics)
     * POST /api/v1/workforce-planning/scenarios/{id}/analyze
     */
    public function analyzeScenario($scenarioId): JsonResponse
    {
        try {
            $results = $this->service->runFullAnalysis($scenarioId);

            return response()->json([
                'success' => true,
                'message' => 'Analysis completed successfully',
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error during analysis: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener recomendaciones para un match específico
     * GET /api/v1/workforce-planning/matches/{id}/recommendations
     */
    public function getMatchRecommendations($matchId): JsonResponse
    {
        $match = \App\Models\WorkforcePlanningMatch::find($matchId);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found',
            ], 404);
        }

        $recommendations = [
            'match_recommendation' => $match->recommendation,
            'suggested_development_path' => $match->development_path_id ? "Development path assigned" : "No development path assigned",
            'transition_timeline' => $match->transition_months . " months",
            'risk_mitigation' => $this->generateRiskMitigation($match),
        ];

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    private function generateRiskMitigation($match): array
    {
        $mitigations = [];

        if ($match->risk_score >= 60) {
            $mitigations[] = "High risk: Assign mentorship program";
        }

        if (count($match->gaps ?? []) >= 3) {
            $mitigations[] = "Multiple skill gaps: Create structured learning path";
        }

        if ($match->readiness_level === 'not_ready') {
            $mitigations[] = "Not ready: Consider alternative candidates";
        }

        return $mitigations;
    }

    /**
     * Simular crecimiento de headcount
     * POST /api/v1/workforce-planning/scenarios/{id}/simulate-growth
     */
    public function simulateGrowth($scenarioId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'growth_percentage' => 'required|numeric|min:0|max:100',
            'horizon_months' => 'required|integer|in:12,18,24,36',
            'target_departments' => 'nullable|array',
            'external_hiring_ratio' => 'nullable|numeric|min:0|max:100',
            'retention_target' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $scenario = StrategicPlanningScenarios::findOrFail($scenarioId);

            // Obtener analytics del escenario
            $analytics = $this->repository->getAnalyticsByScenario($scenarioId);
            $currentHeadcount = $analytics->total_headcount_current ?? 250;

            // Calcular proyecciones
            $projectedHeadcount = round($currentHeadcount * (1 + $validated['growth_percentage'] / 100));
            $netGrowth = $projectedHeadcount - $currentHeadcount;

            // Calcular distribución por departamento (simulado con lógica de negocio)
            $departmentBreakdown = $this->calculateDepartmentBreakdown($scenario, $currentHeadcount, $netGrowth);

            // Identificar skills necesarias basadas en role forecasts
            $skillsNeeded = $this->calculateSkillsNeeded($scenario, $netGrowth);

            // Identificar riesgos críticos
            $criticalRisks = $this->identifyCriticalRisks($scenario);

            $simulation = [
                'scenario_id' => $scenarioId,
                'current_headcount' => $currentHeadcount,
                'projected_headcount' => $projectedHeadcount,
                'net_growth' => $netGrowth,
                'growth_percentage' => $validated['growth_percentage'],
                'horizon_months' => $validated['horizon_months'],
                'by_department' => $departmentBreakdown,
                'skills_needed' => $skillsNeeded,
                'critical_risks' => $criticalRisks,
                'external_hiring_ratio' => $validated['external_hiring_ratio'] ?? 30,
                'retention_target' => $validated['retention_target'] ?? 95,
                'estimated_cost' => [
                    'recruitment' => $netGrowth * 5000,
                    'training' => $netGrowth * 3000,
                    'total' => $netGrowth * 8000,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => ['simulation' => $simulation],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener posiciones críticas y riesgo de sucesión
     * GET /api/v1/workforce-planning/critical-positions?scenario_id=1
     */
    public function getCriticalPositions(Request $request): JsonResponse
    {
        \Log::info('getCriticalPositions REQUEST DETAILS', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'query_all' => $request->query(),
            'input_all' => $request->all(),
            'scenario_id_query' => $request->query('scenario_id'),
            'scenario_id_input' => $request->input('scenario_id'),
            'headers' => $request->headers->all(),
        ]);

        $scenarioId = $request->query('scenario_id');

        \Log::info('getCriticalPositions scenario_id value: "' . $scenarioId . '" (type: ' . gettype($scenarioId) . ')');

        if ($scenarioId === null || $scenarioId === '') {
            \Log::warning('getCriticalPositions: scenario_id is null or empty');
            return response()->json([
                'success' => false,
                'message' => 'scenario_id is required',
            ], 400);
        }

        try {
            $scenario = StrategicPlanningScenarios::findOrFail($scenarioId);

            // Obtener planes de sucesión con análisis de riesgo
            $successionPlans = $scenario->successionPlans()
                ->with(['role', 'primarySuccessor', 'secondarySuccessor', 'department'])
                ->get();

            $criticalPositions = $successionPlans->map(function ($plan) {
                $readyNow = 0;
                $ready12m = 0;
                $ready24m = 0;
                $notReady = 0;

                // Contar sucesores por nivel de preparación
                if ($plan->primarySuccessor && $plan->primary_readiness === 'ready_now') {
                    $readyNow++;
                } elseif ($plan->primarySuccessor && $plan->primary_readiness === 'ready_12m') {
                    $ready12m++;
                } elseif ($plan->primarySuccessor && $plan->primary_readiness === 'ready_24m') {
                    $ready24m++;
                } else {
                    $notReady++;
                }

                if ($plan->secondarySuccessor && $plan->secondary_readiness === 'ready_now') {
                    $readyNow++;
                } elseif ($plan->secondarySuccessor && $plan->secondary_readiness === 'ready_12m') {
                    $ready12m++;
                } elseif ($plan->secondarySuccessor && $plan->secondary_readiness === 'ready_24m') {
                    $ready24m++;
                }

                $criticalityScore = $this->calculateCriticalityScore($plan);
                $riskStatus = $this->assessRiskStatus($plan, $readyNow);

                return [
                    'id' => $plan->id,
                    'role' => [
                        'id' => $plan->role->id ?? null,
                        'name' => $plan->role->name ?? 'N/A'
                    ],
                    'department' => $plan->department ? $plan->department->name : 'N/A',
                    'criticality_level' => $plan->criticality_level,
                    'criticality_score' => $criticalityScore,
                    'impact_if_vacant' => $plan->impact_if_vacant ?? 'High impact on operations',
                    'replacement_time_months' => $plan->replacement_time_months ?? 6,
                    'successors' => [
                        'ready_now' => $readyNow,
                        'ready_12m' => $ready12m,
                        'ready_24m' => $ready24m,
                        'not_ready' => $notReady,
                    ],
                    'risk_status' => $riskStatus,
                    'recommended_action' => $this->recommendAction($plan, $readyNow),
                ];
            });

            $summary = [
                'total_critical_positions' => $criticalPositions->count(),
                'positions_with_ready_successor' => $criticalPositions->filter(fn($p) => $p['successors']['ready_now'] > 0)->count(),
                'positions_without_successor' => $criticalPositions->filter(fn($p) => $p['successors']['ready_now'] == 0)->count(),
                'high_risk_count' => $criticalPositions->filter(fn($p) => $p['risk_status'] === 'HIGH')->count(),
                'medium_risk_count' => $criticalPositions->filter(fn($p) => $p['risk_status'] === 'MEDIUM')->count(),
                'low_risk_count' => $criticalPositions->filter(fn($p) => $p['risk_status'] === 'LOW')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $criticalPositions,
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Helper methods para simulación
    private function calculateDepartmentBreakdown($scenario, $currentHeadcount, $netGrowth): array
    {
        // Distribución ejemplo basada en forecasts
        $departments = [
            'Engineering' => 0.48,
            'Sales' => 0.32,
            'Operations' => 0.12,
            'Support' => 0.08,
        ];

        $breakdown = [];
        foreach ($departments as $dept => $ratio) {
            $current = round($currentHeadcount * $ratio);
            $growth = round($netGrowth * $ratio);
            $breakdown[$dept] = [
                'current' => $current,
                'projected' => $current + $growth,
                'gap' => $growth,
            ];
        }

        return $breakdown;
    }

    private function calculateSkillsNeeded($scenario, $netGrowth): array
    {
        // Top 5 skills más demandadas según role forecasts
        $topSkills = [
            ['skill_id' => 1, 'skill_name' => 'Cloud Architecture', 'weight' => 0.4],
            ['skill_id' => 2, 'skill_name' => 'Leadership', 'weight' => 0.25],
            ['skill_id' => 3, 'skill_name' => 'Data Analysis', 'weight' => 0.15],
            ['skill_id' => 4, 'skill_name' => 'Python', 'weight' => 0.12],
            ['skill_id' => 5, 'skill_name' => 'Communication', 'weight' => 0.08],
        ];

        return array_map(function ($skill) use ($netGrowth) {
            return [
                'skill_id' => $skill['skill_id'],
                'skill_name' => $skill['skill_name'],
                'count' => round($netGrowth * $skill['weight']),
                'availability_internal' => rand(20, 60) / 100,
            ];
        }, $topSkills);
    }

    private function identifyCriticalRisks($scenario): array
    {
        $successionPlans = $scenario->successionPlans()
            ->where('criticality_level', 'critical')
            ->whereNull('primary_successor_id')
            ->with('role')
            ->get();

        return $successionPlans->map(function ($plan) {
            return [
                'role' => $plan->role->name ?? 'Critical Role',
                'critical_level' => $plan->criticality_level,
                'successors_ready' => 0,
                'action' => 'URGENT: Identify and develop successors immediately',
            ];
        })->toArray();
    }

    private function calculateCriticalityScore($plan): int
    {
        $baseScore = match ($plan->criticality_level) {
            'critical' => 90,
            'high' => 70,
            'medium' => 50,
            default => 30,
        };

        // Ajustar según tiempo de reemplazo
        $timeAdjustment = ($plan->replacement_time_months ?? 6) > 6 ? 10 : 0;

        return min(100, $baseScore + $timeAdjustment);
    }

    private function assessRiskStatus($plan, $readyNow): string
    {
        if ($plan->criticality_level === 'critical' && $readyNow == 0) {
            return 'HIGH';
        }

        if ($plan->criticality_level === 'high' && $readyNow == 0) {
            return 'MEDIUM';
        }

        return 'LOW';
    }

    private function recommendAction($plan, $readyNow): string
    {
        if ($readyNow > 0) {
            return "Continue development of ready successors";
        }

        if ($plan->criticality_level === 'critical') {
            return "URGENT: Immediate succession planning required";
        }

        return "Identify and develop potential successors";
    }

    /**
     * Transicionar estado de decisión de un escenario
     * POST /api/v1/workforce-planning/scenarios/{id}/decision-status
     * 
     * @param StrategicPlanningScenarios $scenario
     * @param \App\Http\Requests\WorkforcePlanning\TransitionDecisionStatusRequest $request
     * @return JsonResponse
     */
    public function transitionDecisionStatus(
        StrategicPlanningScenarios $scenario,
        \App\Http\Requests\WorkforcePlanning\TransitionDecisionStatusRequest $request
    ): JsonResponse {
        $this->authorize('transitionDecisionStatus', [$scenario, $request->to_status]);

        $user = auth()->user();

        $updatedScenario = $this->service->transitionDecisionStatus(
            $scenario,
            $request->to_status,
            $user,
            $request->notes
        );

        return response()->json([
            'success' => true,
            'message' => "Estado transicionado de '{$scenario->decision_status}' a '{$request->to_status}'",
            'data' => $updatedScenario->fresh(['statusEvents']),
        ]);
    }

    /**
     * Iniciar ejecución de escenario aprobado
     * POST /api/v1/workforce-planning/scenarios/{id}/execution/start
     * 
     * @param StrategicPlanningScenarios $scenario
     * @param \App\Http\Requests\WorkforcePlanning\ExecutionActionRequest $request
     * @return JsonResponse
     */
    public function startExecution(
        StrategicPlanningScenarios $scenario,
        \App\Http\Requests\WorkforcePlanning\ExecutionActionRequest $request
    ): JsonResponse {
        $this->authorize('startExecution', $scenario);

        $user = auth()->user();

        $updatedScenario = $this->service->startExecution($scenario, $user);

        return response()->json([
            'success' => true,
            'message' => 'Ejecución iniciada',
            'data' => $updatedScenario->fresh(['statusEvents']),
        ]);
    }

    /**
     * Pausar ejecución de escenario
     * POST /api/v1/workforce-planning/scenarios/{id}/execution/pause
     * 
     * @param StrategicPlanningScenarios $scenario
     * @param \App\Http\Requests\WorkforcePlanning\ExecutionActionRequest $request
     * @return JsonResponse
     */
    public function pauseExecution(
        StrategicPlanningScenarios $scenario,
        \App\Http\Requests\WorkforcePlanning\ExecutionActionRequest $request
    ): JsonResponse {
        $this->authorize('pauseExecution', $scenario);

        $user = auth()->user();

        $updatedScenario = $this->service->pauseExecution($scenario, $user, $request->notes);

        return response()->json([
            'success' => true,
            'message' => 'Ejecución pausada',
            'data' => $updatedScenario->fresh(['statusEvents']),
        ]);
    }

    /**
     * Completar ejecución de escenario
     * POST /api/v1/workforce-planning/scenarios/{id}/execution/complete
     * 
     * @param StrategicPlanningScenarios $scenario
     * @param \App\Http\Requests\WorkforcePlanning\ExecutionActionRequest $request
     * @return JsonResponse
     */
    public function completeExecution(
        StrategicPlanningScenarios $scenario,
        \App\Http\Requests\WorkforcePlanning\ExecutionActionRequest $request
    ): JsonResponse {
        $this->authorize('completeExecution', $scenario);

        $user = auth()->user();

        $updatedScenario = $this->service->completeExecution($scenario, $user);

        return response()->json([
            'success' => true,
            'message' => 'Ejecución completada',
            'data' => $updatedScenario->fresh(['statusEvents']),
        ]);
    }

    /**
     * Crear nueva versión de escenario aprobado (inmutabilidad)
     * POST /api/v1/workforce-planning/scenarios/{id}/versions
     * 
     * @param StrategicPlanningScenarios $scenario
     * @param \App\Http\Requests\WorkforcePlanning\CreateVersionRequest $request
     * @return JsonResponse
     */
    public function createNewVersion(
        StrategicPlanningScenarios $scenario,
        \App\Http\Requests\WorkforcePlanning\CreateVersionRequest $request
    ): JsonResponse {
        $this->authorize('createNewVersion', $scenario);

        $user = auth()->user();

        $newVersion = $this->service->createNewVersion(
            $scenario,
            $request->name,
            $request->description,
            $user,
            $request->notes,
            $request->copy_skills ?? true,
            $request->copy_strategies ?? true
        );

        return response()->json([
            'success' => true,
            'message' => "Nueva versión creada (v{$newVersion->version_number})",
            'data' => $newVersion,
        ], 201);
    }

    /**
     * Listar todas las versiones de un grupo de versionamiento
     * GET /api/v1/workforce-planning/scenarios/{id}/versions
     * 
     * @param StrategicPlanningScenarios $scenario
     * @return JsonResponse
     */
    public function listVersions(StrategicPlanningScenarios $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $versions = StrategicPlanningScenarios::where('version_group_id', $scenario->version_group_id)
            ->orderBy('version_number', 'desc')
            ->with(['owner', 'statusEvents'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'version_group_id' => $scenario->version_group_id,
                'current_version' => $scenario->version_number,
                'total_versions' => $versions->count(),
                'versions' => $versions,
            ],
        ]);
    }

    /**
     * Sincronizar skills obligatorias desde padre
     * POST /api/v1/workforce-planning/scenarios/{id}/sync-parent
     * 
     * @param StrategicPlanningScenarios $scenario
     * @param \App\Http\Requests\WorkforcePlanning\SyncParentSkillsRequest $request
     * @return JsonResponse
     */
    public function syncParentSkills(
        StrategicPlanningScenarios $scenario,
        \App\Http\Requests\WorkforcePlanning\SyncParentSkillsRequest $request
    ): JsonResponse {
        $this->authorize('syncFromParent', $scenario);

        $synced = $this->service->syncParentMandatorySkills($scenario);

        return response()->json([
            'success' => true,
            'message' => "{$synced} skills sincronizadas desde el escenario padre",
            'data' => [
                'synced_count' => $synced,
                'parent_scenario' => $scenario->parent->name ?? null,
            ],
        ]);
    }

    /**
     * Consolidar rollup de escenarios hijos
     * GET /api/v1/workforce-planning/scenarios/{id}/rollup
     * 
     * @param StrategicPlanningScenarios $scenario
     * @return JsonResponse
     */
    public function getRollup(StrategicPlanningScenarios $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        if ($scenario->parent_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Solo los escenarios padre tienen rollup de hijos',
            ], 400);
        }

        $rollup = $this->service->consolidateParent($scenario);

        return response()->json([
            'success' => true,
            'data' => $rollup,
        ]);
    }
}
