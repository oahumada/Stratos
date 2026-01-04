<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WorkforcePlanningScenario;
use App\Http\Requests\StoreWorkforcePlanningScenarioRequest;
use App\Http\Requests\UpdateWorkforcePlanningScenarioRequest;
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
    public function createScenario(StoreWorkforcePlanningScenarioRequest $request): JsonResponse
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
    public function updateScenario($id, UpdateWorkforcePlanningScenarioRequest $request): JsonResponse
    {
        $scenario = WorkforcePlanningScenario::find($id);

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
        $scenario = WorkforcePlanningScenario::find($id);

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
}
