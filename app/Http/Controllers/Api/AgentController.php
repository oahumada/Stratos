<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Services\AiOrchestratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /**
     * Lista todos los agentes activos.
     */
    public function index(): JsonResponse
    {
        $agents = Agent::where('is_active', true)->get();
        return response()->json(['data' => $agents]);
    }

    /**
     * Crea un nuevo agente.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'role_description' => ['required', 'string'],
            'persona' => ['required', 'string'],
            'type' => ['required', 'string'],
            'provider' => ['required', 'string'],
            'model' => ['required', 'string'],
            'expertise_areas' => ['nullable', 'array'],
        ]);

        $agent = Agent::create($data);

        return response()->json(['data' => $agent], 201);
    }

    /**
     * Prueba el razonamiento de un agente.
     */
    public function testAgent(Request $request, AiOrchestratorService $orchestrator): JsonResponse
    {
        $data = $request->validate([
            'agent_name' => ['required', 'string'],
            'prompt' => ['required', 'string'],
        ]);

        try {
            $result = $orchestrator->agentThink($data['agent_name'], $data['prompt']);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza la configuración de un agente.
     */
    public function update(Request $request, Agent $agent): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'role_description' => ['required', 'string'],
            'persona' => ['required', 'string'],
            'provider' => ['required', 'string'],
            'model' => ['required', 'string'],
            'expertise_areas' => ['nullable', 'array'],
        ]);

        $agent->update($data);

        return response()->json(['data' => $agent]);
    }
}
