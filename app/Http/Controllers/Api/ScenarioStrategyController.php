<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use App\Models\ScenarioClosureStrategy;
use App\Models\TalentBlueprint;
use App\Models\Roles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ScenarioStrategyController extends Controller
{
    /**
     * Obtiene los gaps de capacidad identificados en el escenario para asignarles estrategias.
     * GET /api/strategic-planning/scenarios/{id}/gaps-for-assignment
     */
    public function getGapsForAssignment($scenarioId): JsonResponse
    {
        try {
            Scenario::findOrFail($scenarioId);

            // Gaps simulados basados en el modelo de talentos/capacidades
            $gaps = [
                [
                    'id' => 'gap_talent_001',
                    'capability' => 'Cloud Native Orchestration',
                    'role_archetype' => 'Site Reliability Engineer',
                    'gap_type' => 'skill_proficiency',
                    'description' => 'Brecha crítica en Kubernetes & Multi-cloud security',
                    'talent_requirement_count' => 4,
                    'affected_teams' => ['DevOps', 'Security Core'],
                    'criticality' => 'critical',
                    'estimated_market_cost' => 120000,
                    'internal_pipeline_capacity' => 2,
                    'recommended_strategy' => 'BUILD (Upskilling) + BUY (1 Senior)',
                ],
                [
                    'id' => 'gap_talent_002',
                    'capability' => 'AI Ethics & Governance',
                    'role_archetype' => 'AI Compliance Officer',
                    'gap_type' => 'new_role',
                    'description' => 'Rol emergente requerido por regulación EU AI Act',
                    'talent_requirement_count' => 1,
                    'affected_teams' => ['Legal', 'Data Science'],
                    'criticality' => 'high',
                    'estimated_market_cost' => 95000,
                    'internal_pipeline_capacity' => 0,
                    'recommended_strategy' => 'BUY (External recruitment)',
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $gaps,
            ]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Asignar una estrategia específica a un gap de capacidad.
     * POST /api/strategic-planning/strategies/assign
     */
    public function assignStrategy(Request $request): JsonResponse
    {
        $request->validate([
            'gap_id' => 'required|string',
            'strategy_type' => 'required|in:build,buy,borrow,bot',
            'rationale' => 'required|string|min:10',
            'appointed_owner_id' => 'required|integer',
            'target_completion_date' => 'nullable|date',
            'budget_allocation' => 'nullable|numeric',
        ]);

        try {
            // En una implementación real, esto persistiría en una tabla scenario_strategies
            // Por ahora, simulamos éxito para el Paso 1
            $assignmentId = rand(100, 999);

            return response()->json([
                'success' => true,
                'message' => 'Estrategia asignada exitosamente al gap de capacidad.',
                'data' => [
                    'assignment_id' => $assignmentId,
                    'status' => 'active',
                    'next_step' => 'Initiate talent search / Learning path creation'
                ]
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene el portafolio consolidado de estrategias aplicadas a un escenario.
     * GET /api/strategic-planning/strategies/portfolio/{scenario_id}
     */
    public function getStrategyPortfolio($scenarioId): JsonResponse
    {
        try {
            $portfolio = [
                'build' => [
                    'count' => 8,
                    'investment' => 45000,
                    'talent_nodes_affected' => 24,
                    'avg_readiness_improvement' => '45%',
                    'status' => 'In Implementation'
                ],
                'buy' => [
                    'count' => 3,
                    'investment' => 180000,
                    'talent_nodes_affected' => 3,
                    'avg_readiness_improvement' => '90% (Instant)',
                    'status' => 'Search Active'
                ],
                'borrow' => [
                    'count' => 1,
                    'investment' => 15000,
                    'talent_nodes_affected' => 5,
                    'avg_readiness_improvement' => 'N/A (Temporal)',
                    'status' => 'Onboarding'
                ],
                'bot' => [
                    'count' => 2,
                    'investment' => 60000,
                    'talent_nodes_affected' => 50,
                    'avg_readiness_improvement' => '100% (Scale)',
                    'status' => 'Development'
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'scenario_id' => $scenarioId,
                    'portfolio' => $portfolio,
                    'summary_metrics' => [
                        'total_strategic_initiatives' => 14,
                        'total_talent_investment' => 300000,
                        'capability_coverage_delta' => '+28%',
                        'risk_reduction_index' => 0.65,
                    ],
                ],
            ]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Obtiene el listado de estrategias individuales para un escenario.
     * GET /api/strategic-planning/scenarios/{id}/strategies
     */
    public function getStrategiesByScenario($scenarioId): JsonResponse
    {
        try {
            $strategies = ScenarioClosureStrategy::with(['skill', 'role'])
                ->where('scenario_id', $scenarioId)
                ->get();
                
            $blueprints = TalentBlueprint::where('scenario_id', $scenarioId)->get()->keyBy('role_name');

            $data = $strategies->map(function (ScenarioClosureStrategy $s) use ($blueprints) {
                $roleName = $s->role ? $s->role->name : null;
                $blueprint = $roleName ? ($blueprints[$roleName] ?? null) : null;
                
                $strategyArray = $s->toArray();
                $strategyArray['skill_name'] = $s->skill ? $s->skill->name : 'General';
                $strategyArray['role_name'] = $roleName;
                $strategyArray['blueprint'] = $blueprint;
                
                return $strategyArray;
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
