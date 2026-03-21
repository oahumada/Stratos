<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Services\Talent\RoleDesignerService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class RoleDesignerController extends Controller
{
    protected RoleDesignerService $designerService;

    public function __construct(RoleDesignerService $designerService)
    {
        $this->designerService = $designerService;
    }

    /**
     * Diseñar un rol usando la metodología de Cubo de Roles (AI).
     */
    public function design(Request $request, $id)
    {
        $isScenario = $request->boolean('is_scenario', false);
        $result = $this->designerService->designRole($id, $isScenario);

        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    /**
     * Realiza un análisis previo de diseño sin necesidad de un ID.
     */
    public function analyzePreview(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        $result = $this->designerService->analyzePreview(
            $request->input('name'),
            $request->input('description')
        );

        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    /**
     * Materializa las competencias sugeridas por la IA.
     */
    public function materializeCompetencies(Request $request, $id)
    {
        $result = $this->designerService->materializeSuggestedSkills((int) $id);

        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    /**
     * Crea una solicitud de aprobación para un rol.
     */
    public function requestApproval(Request $request, $id)
    {
        $request->validate([
            'approver_id' => 'required|exists:people,id',
        ]);

        $result = $this->designerService->requestApproval((int) $id, (int) $request->input('approver_id'));

        return response()->json($result);
    }

    /**
     * Muestra la solicitud de aprobación (público o vía token).
     */
    public function showApprovalRequest($token)
    {
        $request = ApprovalRequest::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $approvable = $request->approvable;
        $component = ($approvable instanceof \App\Models\Roles) ? 'Roles/Approval' : 'Competencies/Approval';
            
        return Inertia::render($component, [
            'token' => $token
        ]);
    }

    /**
     * API para obtener detalles de la solicitud vía token.
     */
    public function getApprovalDetails($token)
    {
        $request = ApprovalRequest::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->load(['approver']);
        
        $approvable = $request->approvable;
        if ($approvable instanceof \App\Models\Roles) {
            $approvable->load(['competencies.skills']);
        }

        return response()->json([
            'success' => true,
            'approval' => $request,
            'approvable' => $approvable,
            'approver' => $request->approver
        ]);
    }

    /**
     * Crea una solicitud de aprobación para una competencia.
     */
    public function requestCompetencyApproval(Request $request, $id)
    {
        $request->validate([
            'approver_id' => 'required|exists:people,id',
        ]);

        $result = $this->designerService->requestCompetencyApproval((int) $id, (int) $request->input('approver_id'));

        return response()->json($result);
    }

    /**
     * Ejecuta la aprobación final.
     */
    public function approve(Request $request, $token)
    {
        $approvalRequest = ApprovalRequest::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $approvable = $approvalRequest->approvable;

        if ($approvable instanceof \App\Models\Roles) {
            $data = $request->validate([
                'purpose' => 'required|string',
                'description' => 'required|string',
                'expected_results' => 'required|string',
            ]);
        } else {
            $data = $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
            ]);
        }

        $result = $this->designerService->finalizeApproval($token, $data);

        return response()->json($result);
    }

    /**
     * Genera un blueprint detallado de habilidades para las competencias sugeridas.
     */
    public function generateSkillBlueprint(Request $request)
    {
        $request->validate([
            'competencies' => 'required|array',
        ]);

        try {
            $result = $this->designerService->generateSkillBlueprint($request->input('competencies'));
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error generando el blueprint: ' . $e->getMessage(),
            ], 500);
        }
    }
}
