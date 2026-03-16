<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Talent\SuccessionPlanningService;
use Illuminate\Http\Request;

class SuccessionController extends Controller
{
    protected SuccessionPlanningService $service;

    public function __construct(SuccessionPlanningService $service)
    {
        $this->service = $service;
    }

    /**
     * Obtiene los mejores sucesores para un rol específico.
     */
    public function getSuccessors(Request $request, $roleId)
    {
        try {
            $successors = $this->service->getSuccessorsForRole((int) $roleId);
            
            return response()->json([
                'success' => true,
                'role_id' => $roleId,
                'candidates' => $successors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular sucesores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analiza profundamente a un candidato específico para un rol.
     */
    public function analyzeCandidate(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:people,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        try {
            $person = \App\Models\People::findOrFail($request->person_id);
            $role = \App\Models\Roles::findOrFail($request->role_id);
            
            $analysis = $this->service->analyzeSuccessionReadiness($person, $role);

            return response()->json([
                'success' => true,
                'analysis' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en el análisis de sucesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista los planes de sucesión guardados.
     */
    public function index(Request $request)
    {
        $plans = \App\Models\SuccessionPlan::with(['person', 'targetRole'])
            ->where('organization_id', $request->user()->organization_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'plans' => $plans
        ]);
    }

    /**
     * Formaliza un plan de sucesión.
     */
    public function storePlan(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:people,id',
            'target_role_id' => 'required|exists:roles,id',
            'readiness_score' => 'required|numeric',
            'readiness_level' => 'required|string',
            'estimated_months' => 'nullable|integer',
            'metrics' => 'nullable|array',
            'gaps' => 'nullable|array'
        ]);

        try {
            $plan = $this->service->saveSuccessionPlan($request->all(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Plan de sucesión formalizado correctamente',
                'plan' => $plan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al formalizar el plan: ' . $e->getMessage()
            ], 500);
        }
    }
}
