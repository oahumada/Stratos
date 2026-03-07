<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Scenario\AgenticScenarioService;
use App\Services\Scenario\CareerPathService;
use App\Services\Scenario\CrisisSimulatorService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScenarioIQController extends Controller
{
    use ApiResponses;

    public function __construct(
        protected CrisisSimulatorService $crisisSimulator,
        protected CareerPathService $careerPaths,
        protected AgenticScenarioService $agenticScenario
    ) {}

    // ── Crisis Simulator (C2) ────────────────────────────────

    /**
     * POST /api/strategic-planning/scenarios/{id}/crisis/attrition
     */
    public function simulateAttrition(int $scenarioId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'attrition_rate' => 'required|numeric|min:1|max:100',
            'departments' => 'nullable|array',
            'timeframe_months' => 'nullable|integer|min:1|max:36',
        ]);

        try {
            $result = $this->crisisSimulator->simulateMassAttrition($scenarioId, $validated);

            return $this->success($result, 'Simulación de attrition ejecutada.');
        } catch (\Exception $e) {
            return $this->error('Error en simulación: '.$e->getMessage(), 500);
        }
    }

    /**
     * POST /api/strategic-planning/scenarios/{id}/crisis/obsolescence
     */
    public function simulateObsolescence(int $scenarioId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'obsolete_skill_ids' => 'required|array|min:1',
            'emerging_skills' => 'nullable|array',
            'horizon_months' => 'nullable|integer|min:6|max:36',
        ]);

        try {
            $result = $this->crisisSimulator->simulateSkillObsolescence($scenarioId, $validated);

            return $this->success($result, 'Simulación de obsolescencia ejecutada.');
        } catch (\Exception $e) {
            return $this->error('Error en simulación: '.$e->getMessage(), 500);
        }
    }

    /**
     * POST /api/strategic-planning/scenarios/{id}/crisis/restructuring
     */
    public function simulateRestructuring(int $scenarioId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'merge_departments' => 'nullable|array',
            'eliminate_role_ids' => 'nullable|array',
            'new_roles' => 'nullable|array',
        ]);

        try {
            $result = $this->crisisSimulator->simulateRestructuring($scenarioId, $validated);

            return $this->success($result, 'Simulación de restructuración ejecutada.');
        } catch (\Exception $e) {
            return $this->error('Error en simulación: '.$e->getMessage(), 500);
        }
    }

    // ── Career Paths (C3) ────────────────────────────────────

    /**
     * GET /api/career-paths/{peopleId}
     */
    public function getCareerPaths(int $peopleId): JsonResponse
    {
        try {
            $paths = $this->careerPaths->calculateCareerPaths($peopleId);

            return $this->success($paths, 'Rutas de carrera calculadas.');
        } catch (\Exception $e) {
            return $this->error('Error al calcular rutas: '.$e->getMessage(), 500);
        }
    }

    /**
     * GET /api/career-paths/route/{fromRoleId}/{toRoleId}
     */
    public function getOptimalRoute(int $fromRoleId, int $toRoleId): JsonResponse
    {
        try {
            $route = $this->careerPaths->calculateOptimalRoute($fromRoleId, $toRoleId);

            return $this->success($route, 'Ruta óptima calculada.');
        } catch (\Exception $e) {
            return $this->error('Error al calcular ruta: '.$e->getMessage(), 500);
        }
    }

    /**
     * GET /api/career-paths/mobility-map/{organizationId}
     */
    public function getMobilityMap(int $organizationId): JsonResponse
    {
        try {
            $map = $this->careerPaths->generateMobilityMap($organizationId);

            return $this->success($map, 'Mapa de movilidad generado.');
        } catch (\Exception $e) {
            return $this->error('Error al generar mapa: '.$e->getMessage(), 500);
        }
    }

    /**
     * GET /api/career-paths/predict/{peopleId}/{targetRoleId}
     */
    public function predictTransition(int $peopleId, int $targetRoleId): JsonResponse
    {
        try {
            $prediction = $this->careerPaths->predictTransitionSuccess($peopleId, $targetRoleId);

            return $this->success($prediction, 'Predicción calculada.');
        } catch (\Exception $e) {
            return $this->error('Error en predicción: '.$e->getMessage(), 500);
        }
    }

    // ── Agentic Scenarios (Fase 6) ───────────────────────────

    /**
     * POST /api/strategic-planning/scenarios/{id}/agentic-simulation
     */
    public function runAgenticSimulation(int $scenarioId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'change_type' => 'required|in:team_merge,tech_disruption,expansion,downsizing,generic',
            'description' => 'nullable|string',
            'growth_percentage' => 'nullable|numeric',
            'team_a_department_id' => 'nullable|integer',
            'team_b_department_id' => 'nullable|integer',
            'obsolete_skill_ids' => 'nullable|array',
            'emerging_skills' => 'nullable|array',
            'eliminate_role_ids' => 'nullable|array',
            'merge_departments' => 'nullable|array',
            'new_roles' => 'nullable|array',
            'horizon_months' => 'nullable|integer',
        ]);

        try {
            $result = $this->agenticScenario->runAgenticSimulation($scenarioId, $validated);

            return $this->success($result, 'Simulación agéntica ejecutada.');
        } catch (\Exception $e) {
            return $this->error('Error en simulación agéntica: '.$e->getMessage(), 500);
        }
    }

    /**
     * POST /api/strategic-planning/scenarios/{id}/what-if
     */
    public function quickWhatIf(int $scenarioId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
        ]);

        try {
            $result = $this->agenticScenario->quickWhatIf($validated['question'], $scenarioId);

            return $this->success($result, 'Análisis What-If completado.');
        } catch (\Exception $e) {
            return $this->error('Error en What-If: '.$e->getMessage(), 500);
        }
    }
}
