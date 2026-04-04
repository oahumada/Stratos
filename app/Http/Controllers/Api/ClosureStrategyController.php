<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use App\Models\ScenarioClosureStrategy;
use App\Services\ClosureStrategyMotor;
use Illuminate\Http\JsonResponse;

class ClosureStrategyController extends Controller
{
    public function __construct(private readonly ClosureStrategyMotor $motor) {}

    /**
     * GET /api/scenarios/{id}/recommendations
     * Lista las recomendaciones existentes para un escenario.
     */
    public function index(int $id): JsonResponse
    {
        $scenario = Scenario::where('id', $id)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        $this->authorize('view', $scenario);

        $strategies = ScenarioClosureStrategy::with(['role', 'skill'])
            ->where('scenario_id', $scenario->id)
            ->orderByDesc('ia_confidence_score')
            ->get();

        return response()->json([
            'scenario_id'   => $scenario->id,
            'total'         => $strategies->count(),
            'recommendations' => $strategies,
        ]);
    }

    /**
     * POST /api/scenarios/{id}/recommendations/generate
     * Ejecuta el motor de palancas y genera recomendaciones para todas las brechas.
     */
    public function generate(int $id): JsonResponse
    {
        $scenario = Scenario::with(['roles.competencies.competency'])
            ->where('id', $id)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        $this->authorize('view', $scenario);

        $result = $this->motor->generateForScenario($scenario);

        return response()->json([
            'message'        => "Motor ejecutado: {$result['generated']} recomendaciones generadas para {$result['gaps_analyzed']} brechas.",
            'gaps_analyzed'  => $result['gaps_analyzed'],
            'generated'      => $result['generated'],
            'recommendations' => $result['recommendations'],
        ], 201);
    }
}
