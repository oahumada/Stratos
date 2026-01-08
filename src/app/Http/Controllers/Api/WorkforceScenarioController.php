<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkforceScenarioRequest;
use App\Http\Requests\UpdateWorkforceScenarioRequest;
use App\Http\Requests\RefreshSuggestedStrategiesRequest;
use App\Http\Requests\InstantiateScenarioFromTemplateRequest;
use App\Models\ScenarioTemplate;
use App\Models\ScenarioSkillDemand;
use App\Models\WorkforcePlanningScenario;
use App\Services\WorkforcePlanningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkforceScenarioController extends Controller
{
    public function __construct(private WorkforcePlanningService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', WorkforcePlanningScenario::class);

        $organizationId = auth()->user()->organization_id;

        $query = WorkforcePlanningScenario::query()
            ->forOrganization($organizationId)
            ->with(['template']);

        if ($status = $request->query('status')) {
            $query->byStatus($status);
        }

        if ($type = $request->query('scenario_type')) {
            $query->byType($type);
        }

        $scenarios = $query->orderByDesc('created_at')->paginate(15);

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

    public function store(StoreWorkforceScenarioRequest $request): JsonResponse
    {
        $this->authorize('create', WorkforcePlanningScenario::class);

        $data = $request->validated();
        $data['organization_id'] = auth()->user()->organization_id;
        $data['created_by'] = auth()->id();

        $scenario = WorkforcePlanningScenario::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Scenario created successfully',
            'data' => $scenario,
        ], 201);
    }

    public function instantiateFromTemplate(ScenarioTemplate $template, InstantiateScenarioFromTemplateRequest $request): JsonResponse
    {
        $this->authorize('view', $template);
        $this->authorize('create', WorkforcePlanningScenario::class);

        $data = $request->validated();
        $organizationId = auth()->user()->organization_id;
        $customizations = $data['customizations'] ?? [];

        // Extract versionamiento fields
        $versionGroupId = \Illuminate\Support\Str::uuid();
        $scopeType = $customizations['scope_type'] ?? 'organization';
        $parentId = $customizations['parent_id'] ?? null;

        $scenario = WorkforcePlanningScenario::create([
            'organization_id' => $organizationId,
            'template_id' => $template->id,
            'name' => $customizations['name'] ?? $template->name,
            'description' => $customizations['description'] ?? $template->description,
            'scenario_type' => $customizations['scenario_type'] ?? $template->scenario_type,
            'scope_type' => $scopeType,
            'parent_id' => $parentId,
            'version_group_id' => $versionGroupId,
            'version_number' => 1,
            'is_current_version' => true,
            'decision_status' => 'draft',
            'execution_status' => 'not_started',
            'horizon_months' => $customizations['horizon_months'] ?? 12,
            'time_horizon_weeks' => $customizations['time_horizon_weeks'] ?? null,
            'target_date' => $customizations['target_date'] ?? null,
            'status' => 'draft',
            'assumptions' => $customizations['assumptions'] ?? ($template->config['assumptions'] ?? null),
            'custom_config' => $customizations['custom_config'] ?? null,
            'estimated_budget' => $customizations['estimated_budget'] ?? null,
            'fiscal_year' => $customizations['fiscal_year'] ?? now()->year,
            'owner' => $customizations['owner'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $predefined = $template->config['predefined_skills'] ?? [];
        foreach ($predefined as $skill) {
            ScenarioSkillDemand::create([
                'scenario_id' => $scenario->id,
                'skill_id' => $skill['skill_id'] ?? null,
                'role_id' => $skill['role_id'] ?? null,
                'department' => $skill['department'] ?? null,
                'required_headcount' => $skill['required_headcount'] ?? 0,
                'required_level' => $skill['required_level'] ?? 3,
                'priority' => $skill['priority'] ?? 'medium',
                'rationale' => $skill['rationale'] ?? null,
                'target_date' => $skill['target_date'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Scenario instantiated from template',
            'data' => $scenario->load(['template', 'skillDemands', 'parent', 'statusEvents']),
        ], 201);
    }

    public function show(WorkforcePlanningScenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        return response()->json([
            'success' => true,
            'data' => $scenario->load(['template', 'skillDemands', 'closureStrategies', 'milestones']),
        ]);
    }

    public function update(UpdateWorkforceScenarioRequest $request, WorkforcePlanningScenario $scenario): JsonResponse
    {
        $this->authorize('update', $scenario);

        $scenario->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Scenario updated successfully',
            'data' => $scenario,
        ]);
    }

    public function destroy(WorkforcePlanningScenario $scenario): JsonResponse
    {
        $this->authorize('delete', $scenario);

        $scenario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Scenario deleted successfully',
        ]);
    }

    public function calculateGaps(WorkforcePlanningScenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $result = $this->service->calculateScenarioGaps($scenario);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function refreshSuggestedStrategies(RefreshSuggestedStrategiesRequest $request, WorkforcePlanningScenario $scenario): JsonResponse
    {
        $this->authorize('update', $scenario);

        $created = $this->service->refreshSuggestedStrategies($scenario, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Suggested strategies refreshed',
            'created' => $created,
        ]);
    }
}
