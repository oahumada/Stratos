<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScenarioSimulationController extends Controller
{
    /**
     * Simular crecimiento de talento basado en el escenario estratégico.
     * POST /api/strategic-planning/scenarios/{id}/simulate-growth
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
            $scenario = Scenario::findOrFail($scenarioId);

            // Simulación de impacto en el talento (datos demo para Paso 1)
            $currentTalentPool = 250;
            $projectedTalentRequirement = $currentTalentPool * (1 + $validated['growth_percentage'] / 100);
            $netGap = $projectedTalentRequirement - $currentTalentPool;

            $simulation = [
                'scenario_id' => $scenarioId,
                'name' => $scenario->name,
                'current_talent_pool' => $currentTalentPool,
                'projected_talent_requirement' => $projectedTalentRequirement,
                'net_capacity_gap' => $netGap,
                'by_capability_area' => [
                    'Engineering' => ['current' => 120, 'projected' => 156, 'gap' => 36],
                    'Product' => ['current' => 80, 'projected' => 100, 'gap' => 20],
                ],
                'strategic_skills_needed' => [
                    [
                        'skill_id' => 1,
                        'skill_name' => 'Cloud Architecture / AI Engineering',
                        'count_needed' => intval($netGap * 0.4),
                        'internal_availability_pct' => 40,
                    ],
                    [
                        'skill_id' => 2,
                        'skill_name' => 'Ethical AI Governance',
                        'count_needed' => intval($netGap * 0.15),
                        'internal_availability_pct' => 10,
                    ]
                ],
                'critical_talent_risks' => [
                    [
                        'role' => 'VP of Future Engineering',
                        'critical_level' => 'critical',
                        'successors_ready' => 0,
                        'recommendation' => 'URGENT TALENT ACQUISITION / RESKILLING'
                    ]
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => ['simulation' => $simulation],
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en simulación: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener talentos y habilidades en riesgo crítico.
     * GET /api/strategic-planning/critical-talents
     */
    public function getCriticalTalents(Request $request): JsonResponse
    {
        $scenarioId = $request->input('scenario_id');

        try {
            $scenario = Scenario::findOrFail($scenarioId);

            $criticalTalents = [
                [
                    'id' => 1,
                    'capability' => 'AI Core Orchestration',
                    'role_archetype' => 'Principal AI Architect',
                    'criticality_score' => 95,
                    'impact_analysis' => 'Vital para el roadmap de automatización Q3',
                    'replacement_difficulty' => 'Extrema (Escasez en mercado)',
                    'internal_succession' => [
                        'ready_now' => 0,
                        'ready_12m' => 1,
                        'not_ready' => 5,
                    ],
                    'risk_status' => 'HIGH',
                    'mitigation_strategy' => 'Retention Bonus + Internal Shadowing program',
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $criticalTalents,
                'summary' => [
                    'total_critical_nodes' => 12,
                    'nodes_secured' => 3,
                    'nodes_at_risk' => 9,
                    'high_risk_exposure_pct' => 75,
                ],
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener talentos críticos: ' . $e->getMessage(),
            ], 500);
        }
    }
}
