<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WorkforcePlan;
use App\Services\WorkforcePlanning\WorkforcePlanningPhase1Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkforcePlanController extends Controller
{
    public function __construct(
        private WorkforcePlanningPhase1Service $phase1Service
    ) {
    }

    public function index(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $plans = WorkforcePlan::forOrganization($orgId)
            ->with(['owner', 'sponsor'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->search, fn($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
            )
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($plans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'planning_horizon_months' => ['required', 'integer', Rule::in([12, 24, 36])],
            'scope_type' => ['required', Rule::in(['organization_wide', 'business_unit', 'department', 'critical_roles_only'])],
            'scope_notes' => 'nullable|string',
            'strategic_context' => 'nullable|string',
            'budget_constraints' => 'nullable|string',
            'legal_constraints' => 'nullable|string',
            'labor_relations_constraints' => 'nullable|string',
            'owner_user_id' => 'required|exists:users,id',
            'sponsor_user_id' => 'nullable|exists:users,id',
            'stakeholders' => 'nullable|array',
            'stakeholders.*.user_id' => 'required|exists:users,id',
            'stakeholders.*.role' => ['required', Rule::in(['sponsor', 'owner', 'contributor', 'reviewer', 'approver', 'informed'])],
            'stakeholders.*.represents' => 'nullable|string|max:255',
        ]);

        $plan = $this->phase1Service->createPlan(
            Auth::user()->organization_id,
            $validated,
            Auth::id()
        );

        return response()->json($plan, 201);
    }

    public function show(WorkforcePlan $workforcePlan)
    {
        $this->authorize('view', $workforcePlan);

        $workforcePlan->load([
            'owner',
            'sponsor',
            'approver',
            'scopeUnits',
            'scopeRoles.role',
            'transformationProjects',
            'talentRisks.affectedRole',
            'stakeholders.user',
            'documents.uploader',
        ]);

        $statistics = $this->phase1Service->getPlanStatistics($workforcePlan);

        return response()->json([
            'plan' => $workforcePlan,
            'statistics' => $statistics,
        ]);
    }

    public function update(Request $request, WorkforcePlan $workforcePlan)
    {
        $this->authorize('update', $workforcePlan);

        if (!$workforcePlan->canBeEdited()) {
            return response()->json([
                'message' => 'El plan no puede ser editado en su estado actual'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'planning_horizon_months' => ['sometimes', 'integer', Rule::in([12, 24, 36])],
            'scope_type' => ['sometimes', Rule::in(['organization_wide', 'business_unit', 'department', 'critical_roles_only'])],
            'scope_notes' => 'nullable|string',
            'strategic_context' => 'nullable|string',
            'budget_constraints' => 'nullable|string',
            'legal_constraints' => 'nullable|string',
            'labor_relations_constraints' => 'nullable|string',
            'owner_user_id' => 'sometimes|exists:users,id',
            'sponsor_user_id' => 'nullable|exists:users,id',
        ]);

        $plan = $this->phase1Service->updatePlan($workforcePlan, $validated, Auth::id());

        return response()->json($plan);
    }

    public function destroy(WorkforcePlan $workforcePlan)
    {
        $this->authorize('delete', $workforcePlan);

        if (!$workforcePlan->isDraft()) {
            return response()->json([
                'message' => 'Solo se pueden eliminar planes en estado borrador'
            ], 403);
        }

        $workforcePlan->delete();

        return response()->json(['message' => 'Plan eliminado exitosamente']);
    }

    public function addScopeUnits(Request $request, WorkforcePlan $workforcePlan)
    {
        $this->authorize('update', $workforcePlan);

        $validated = $request->validate([
            'units' => 'required|array|min:1',
            'units.*.unit_type' => ['required', Rule::in(['department', 'business_unit', 'location', 'cost_center', 'custom'])],
            'units.*.unit_id' => 'nullable|integer',
            'units.*.unit_name' => 'required|string|max:255',
            'units.*.inclusion_reason' => ['required', Rule::in(['critical', 'high_turnover', 'transformation', 'growth', 'downsizing', 'other'])],
            'units.*.notes' => 'nullable|string',
        ]);

        $this->phase1Service->addScopeUnits($workforcePlan, $validated['units']);

        return response()->json([
            'message' => 'Unidades agregadas exitosamente',
            'count' => count($validated['units']),
        ]);
    }

    public function addScopeRoles(Request $request, WorkforcePlan $workforcePlan)
    {
        $this->authorize('update', $workforcePlan);

        $validated = $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*.role_id' => 'required|exists:roles,id',
            'roles.*.inclusion_reason' => ['required', Rule::in(['critical', 'hard_to_fill', 'transformation', 'high_risk', 'other'])],
            'roles.*.notes' => 'nullable|string',
        ]);

        $this->phase1Service->addScopeRoles($workforcePlan, $validated['roles']);

        return response()->json([
            'message' => 'Roles agregados exitosamente',
            'count' => count($validated['roles']),
        ]);
    }

    public function addTransformationProjects(Request $request, WorkforcePlan $workforcePlan)
    {
        $this->authorize('update', $workforcePlan);

        $validated = $request->validate([
            'projects' => 'required|array|min:1',
            'projects.*.project_name' => 'required|string|max:255',
            'projects.*.project_type' => ['required', Rule::in(['digital_transformation', 'process_automation', 'growth', 'downsizing', 'merger_acquisition', 'restructuring', 'other'])],
            'projects.*.expected_impact' => 'nullable|string',
            'projects.*.estimated_fte_impact' => 'nullable|integer',
            'projects.*.start_date' => 'nullable|date',
            'projects.*.end_date' => 'nullable|date|after_or_equal:projects.*.start_date',
        ]);

        $this->phase1Service->addTransformationProjects($workforcePlan, $validated['projects']);

        return response()->json([
            'message' => 'Proyectos de transformación agregados exitosamente',
            'count' => count($validated['projects']),
        ]);
    }

    public function addTalentRisk(Request $request, WorkforcePlan $workforcePlan)
    {
        $this->authorize('update', $workforcePlan);

        $validated = $request->validate([
            'risk_type' => ['required', Rule::in(['aging_workforce', 'high_turnover', 'scarce_skills', 'succession_gap', 'knowledge_loss', 'external_competition', 'other'])],
            'risk_description' => 'required|string',
            'affected_unit_id' => 'nullable|integer',
            'affected_role_id' => 'nullable|exists:roles,id',
            'severity' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'likelihood' => ['required', Rule::in(['low', 'medium', 'high'])],
            'mitigation_strategy' => 'nullable|string',
        ]);

        $risk = $this->phase1Service->addTalentRisk($workforcePlan, $validated);

        return response()->json($risk, 201);
    }

    // Compatibility wrappers for existing route names
    public function addScopeUnit(Request $request, WorkforcePlan $workforcePlan)
    {
        // accepts single unit payload
        $unit = $request->validate([
            'unit_type' => ['required', Rule::in(['department', 'business_unit', 'location', 'cost_center', 'custom'])],
            'unit_id' => 'nullable|integer',
            'unit_name' => 'required|string|max:255',
            'inclusion_reason' => ['required', Rule::in(['critical', 'high_turnover', 'transformation', 'growth', 'downsizing', 'other'])],
            'notes' => 'nullable|string',
        ]);

        $this->phase1Service->addScopeUnits($workforcePlan, [$unit]);

        return response()->json([
            'message' => 'Unidad agregada exitosamente',
        ], 201);
    }

    public function addScopeRole(Request $request, WorkforcePlan $workforcePlan)
    {
        $data = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'inclusion_reason' => ['required', Rule::in(['critical', 'hard_to_fill', 'transformation', 'high_risk', 'other'])],
            'notes' => 'nullable|string',
        ]);

        $this->phase1Service->addScopeRoles($workforcePlan, [$data]);

        return response()->json([
            'message' => 'Rol agregado exitosamente',
        ], 201);
    }

    public function addTransformationProject(Request $request, WorkforcePlan $workforcePlan)
    {
        $data = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_type' => ['required', Rule::in(['digital_transformation', 'process_automation', 'growth', 'downsizing', 'merger_acquisition', 'restructuring', 'other'])],
            'expected_impact' => 'nullable|string',
            'estimated_fte_impact' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $this->phase1Service->addTransformationProjects($workforcePlan, [$data]);

        return response()->json([
            'message' => 'Proyecto de transformación agregado exitosamente',
        ], 201);
    }

    public function analyzeRisks(WorkforcePlan $workforcePlan)
    {
        $this->authorize('update', $workforcePlan);

        $risksCount = $this->phase1Service->saveDetectedRisks($workforcePlan);

        return response()->json([
            'message' => 'Análisis de riesgos completado',
            'risks_detected' => $risksCount,
        ]);
    }

    public function scopeDocument(Request $request, WorkforcePlan $workforcePlan)
    {
        $this->authorize('view', $workforcePlan);

        $document = $this->phase1Service->generateScopeDocument($workforcePlan, $request->all());

        return response()->json($document, 201);
    }

    // Route compatibility: GET statistics -> statistics
    public function statistics(WorkforcePlan $workforcePlan)
    {
        return $this->getStatistics($workforcePlan);
    }

    public function approve(WorkforcePlan $workforcePlan)
    {
        $this->authorize('approve', $workforcePlan);

        $workforcePlan->approve(Auth::id());

        return response()->json([
            'message' => 'Plan aprobado exitosamente',
            'plan' => $workforcePlan->fresh(),
        ]);
    }

    public function activate(WorkforcePlan $workforcePlan)
    {
        $this->authorize('approve', $workforcePlan);

        if (!$workforcePlan->isApproved()) {
            return response()->json([
                'message' => 'El plan debe estar aprobado para ser activado'
            ], 403);
        }

        $workforcePlan->activate();

        return response()->json([
            'message' => 'Plan activado exitosamente',
            'plan' => $workforcePlan->fresh(),
        ]);
    }

    public function archive(WorkforcePlan $workforcePlan)
    {
        $this->authorize('update', $workforcePlan);

        $workforcePlan->archive();

        return response()->json([
            'message' => 'Plan archivado exitosamente',
            'plan' => $workforcePlan->fresh(),
        ]);
    }

    public function getStatistics(WorkforcePlan $workforcePlan)
    {
        $this->authorize('view', $workforcePlan);

        $statistics = $this->phase1Service->getPlanStatistics($workforcePlan);

        return response()->json($statistics);
    }
}
