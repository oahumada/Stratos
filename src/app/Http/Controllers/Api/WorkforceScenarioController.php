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
use App\Models\WorkforcePlan;
use App\Models\WorkforcePlanScopeRole;
use App\Models\WorkforcePlanScopeUnit;
use App\Services\WorkforcePlanningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Create a minimal WorkforcePlan to represent Phase 1 basics for this scenario
        $workforcePlan = null;
        $workforcePlanError = null;
        try {
            $weeks = $scenario->time_horizon_weeks ?? $scenario->horizon_months * 4 ?? null;
            $planningMonths = $weeks ? (int) ceil($weeks / 4) : ($scenario->horizon_months ?? 12);

            $startDate = now()->startOfMonth();
            $endDate = (clone $startDate)->addMonths($planningMonths);

            // Map incoming scope types to DB enum values
            $scopeMap = [
                'organization' => 'organization_wide',
                'department' => 'department',
                'role_family' => 'critical_roles_only',
                'business_unit' => 'business_unit',
            ];

            $mappedScope = $scopeMap[$scopeType] ?? ($scopeMap[$scenario->scope_type] ?? 'organization_wide');

            $workforcePlan = WorkforcePlan::create([
                'organization_id' => $organizationId,
                'name' => $scenario->name,
                'description' => $scenario->description,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'planning_horizon_months' => $planningMonths,
                'scope_type' => $mappedScope,
                'status' => 'draft',
                'owner_user_id' => auth()->id(),
                'created_by' => auth()->id(),
            ]);

            // Persist link from scenario -> workforce_plan
            $scenario->workforce_plan_id = $workforcePlan->id;
            $scenario->save();

            // Attach minimal reference into scenario payload for frontend convenience
            // eager load related plan data for frontend
            $workforcePlan->load(['scopeRoles', 'scopeUnits', 'transformationProjects', 'talentRisks', 'stakeholders', 'documents']);
            $scenario->workforce_plan = $workforcePlan;

            // Create initial scope roles/units based on template predefined_skills when possible
            foreach ($predefined as $skill) {
                if (!empty($skill['role_id'])) {
                    try {
                        WorkforcePlanScopeRole::firstOrCreate([
                            'workforce_plan_id' => $workforcePlan->id,
                            'role_id' => $skill['role_id'],
                        ], [
                            'inclusion_reason' => 'critical',
                            'notes' => $skill['rationale'] ?? null,
                        ]);
                    } catch (\Exception $e) {
                        report($e);
                    }
                }

                if (!empty($skill['department']) || !empty($skill['unit_id']) || !empty($skill['unit_name'])) {
                    try {
                        WorkforcePlanScopeUnit::firstOrCreate([
                            'workforce_plan_id' => $workforcePlan->id,
                            'unit_id' => $skill['unit_id'] ?? null,
                            'unit_name' => $skill['unit_name'] ?? ($skill['department'] ?? 'Unnamed'),
                        ], [
                            'unit_type' => $skill['unit_type'] ?? 'department',
                            'inclusion_reason' => 'growth',
                            'notes' => $skill['rationale'] ?? null,
                        ]);
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            }
        } catch (\Exception $e) {
            // don't break scenario creation if plan creation fails; log for debugging
            report($e);
            $workforcePlanError = $e->getMessage();
        }
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

        $scenario->load(['template', 'skillDemands', 'parent', 'statusEvents', 'workforcePlan.scopeRoles', 'workforcePlan.scopeUnits', 'workforcePlan.transformationProjects', 'workforcePlan.talentRisks', 'workforcePlan.stakeholders', 'workforcePlan.documents']);

        return response()->json([
            'success' => true,
            'message' => 'Scenario instantiated from template',
            'data' => $scenario,
            'workforce_plan' => $scenario->workforce_plan ?? null,
            'workforce_plan_created' => $workforcePlan ? true : false,
            'workforce_plan_error' => $workforcePlanError,
        ], 201);
    }

    public function show(WorkforcePlanningScenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $scenario->load(['template', 'skillDemands', 'closureStrategies', 'milestones', 'workforcePlan.scopeRoles', 'workforcePlan.scopeUnits', 'workforcePlan.transformationProjects', 'workforcePlan.talentRisks', 'workforcePlan.stakeholders', 'workforcePlan.documents']);

        return response()->json([
            'success' => true,
            'data' => $scenario,
        ]);
    }

    public function update(UpdateWorkforceScenarioRequest $request, WorkforcePlanningScenario $scenario): JsonResponse
    {
        $this->authorize('update', $scenario);

        $data = $request->validated();

        // If step1 payload is present, handle metadata, skills and roles mapping
        if (!empty($data['step1'])) {
            $step1 = $data['step1'];
            $metadata = $step1['metadata'] ?? [];

            // update basic metadata fields on scenario
            $scenario->fill([
                'name' => $metadata['name'] ?? $scenario->name,
                'description' => $metadata['description'] ?? $scenario->description,
                'time_horizon_weeks' => $metadata['time_horizon_weeks'] ?? $scenario->time_horizon_weeks,
                'horizon_months' => $metadata['planning_horizon_months'] ?? $scenario->horizon_months,
                'scope_type' => $metadata['scope_type'] ?? $scenario->scope_type,
                'target_date' => $metadata['target_date'] ?? $metadata['start_date'] ?? $scenario->target_date,
                'owner' => $metadata['owner'] ?? $scenario->owner,
            ]);
            $scenario->save();

            // process skills: update or create ScenarioSkillDemand
            $skills = $step1['skills'] ?? [];
            foreach ($skills as $s) {
                // normalize fields
                $skillId = $s['skill_id'] ?? null;
                $id = $s['id'] ?? null;
                $payload = [
                    'scenario_id' => $scenario->id,
                    'skill_id' => $skillId,
                    'role_id' => $s['role_id'] ?? null,
                    'department' => $s['department'] ?? null,
                    'required_headcount' => $s['required_headcount'] ?? 0,
                    'required_level' => $s['required_level'] ?? null,
                    'priority' => $s['priority'] ?? 'medium',
                    'rationale' => $s['rationale'] ?? null,
                    'target_date' => $s['target_date'] ?? null,
                ];

                try {
                    if ($id) {
                        $ssd = ScenarioSkillDemand::where('id', $id)->where('scenario_id', $scenario->id)->first();
                        if ($ssd) {
                            $ssd->update($payload);
                        } else {
                            ScenarioSkillDemand::create($payload);
                        }
                    } elseif ($skillId) {
                        ScenarioSkillDemand::updateOrCreate(
                            ['scenario_id' => $scenario->id, 'skill_id' => $skillId],
                            $payload
                        );
                    }
                } catch (\Exception $e) {
                    report($e);
                }
            }

            // persist roles into scenario.custom_config to be reviewed later (will be imported into plan on demand)
            $roles = $step1['roles'] ?? [];
            $custom = $scenario->custom_config ?? [];
            $custom['step1_roles'] = $roles;
            $scenario->custom_config = $custom;
            $scenario->save();

            // If frontend requested import or any item marked for import, persist into WorkforcePlan scope
            $shouldImport = !empty($step1['import_to_plan']);
            // also check per-item flags
            foreach (($roles ?? []) as $r) {
                if (!empty($r['action']) && $r['action'] === 'import') {
                    $shouldImport = true;
                    break;
                }
                if (!empty($r['import'])) {
                    $shouldImport = true;
                    break;
                }
            }

            foreach (($skills ?? []) as $s) {
                if (!empty($s['action']) && $s['action'] === 'import') {
                    $shouldImport = true;
                    break;
                }
                if (!empty($s['import'])) {
                    $shouldImport = true;
                    break;
                }
            }

            if ($shouldImport) {
                DB::transaction(function () use ($scenario, $roles, $skills) {
                    // ensure a WorkforcePlan exists for this scenario
                    $plan = $scenario->workforce_plan ?? null;
                    if (!$plan) {
                        $weeks = $scenario->time_horizon_weeks ?? $scenario->horizon_months * 4 ?? null;
                        $planningMonths = $weeks ? (int) ceil($weeks / 4) : ($scenario->horizon_months ?? 12);
                        $startDate = now()->startOfMonth();
                        $endDate = (clone $startDate)->addMonths($planningMonths);

                        $scopeMap = [
                            'organization' => 'organization_wide',
                            'department' => 'department',
                            'role_family' => 'critical_roles_only',
                            'business_unit' => 'business_unit',
                        ];
                        $mappedScope = $scopeMap[$scenario->scope_type] ?? 'organization_wide';

                        $plan = WorkforcePlan::create([
                            'organization_id' => $scenario->organization_id,
                            'name' => $scenario->name ?? ('Plan for ' . $scenario->id),
                            'description' => $scenario->description ?? null,
                            'start_date' => $startDate->toDateString(),
                            'end_date' => $endDate->toDateString(),
                            'planning_horizon_months' => $planningMonths,
                            'scope_type' => $mappedScope,
                            'status' => 'draft',
                            'owner_user_id' => $scenario->owner ?? auth()->id(),
                            'created_by' => auth()->id(),
                        ]);

                        $scenario->workforce_plan_id = $plan->id;
                        $scenario->save();
                    }

                    // Import roles into plan (only if role_id present)
                    foreach (($roles ?? []) as $r) {
                        $doImport = false;
                        if (!empty($r['action']) && $r['action'] === 'import') $doImport = true;
                        if (!empty($r['import'])) $doImport = true;
                        if (!empty($r['include']) && $r['include'] === true && !isset($r['action'])) $doImport = true;

                        if ($doImport && !empty($r['role_id'])) {
                            try {
                                WorkforcePlanScopeRole::firstOrCreate([
                                    'workforce_plan_id' => $plan->id,
                                    'role_id' => $r['role_id'],
                                ], [
                                    'inclusion_reason' => $r['inclusion_reason'] ?? 'from_step1',
                                    'notes' => $r['notes'] ?? null,
                                ]);
                            } catch (\Exception $e) {
                                report($e);
                            }
                        }
                    }

                    // Import units from skills or roles
                    // From roles: if unit info provided
                    foreach (($roles ?? []) as $r) {
                        $doImport = false;
                        if (!empty($r['action']) && $r['action'] === 'import') $doImport = true;
                        if (!empty($r['import'])) $doImport = true;
                        if (!empty($r['include']) && $r['include'] === true && !isset($r['action'])) $doImport = true;

                        if ($doImport) {
                            $unitId = $r['unit_id'] ?? null;
                            $unitName = $r['unit_name'] ?? ($r['department'] ?? null);
                            if ($unitId || $unitName) {
                                try {
                                    WorkforcePlanScopeUnit::firstOrCreate([
                                        'workforce_plan_id' => $plan->id,
                                        'unit_id' => $unitId,
                                        'unit_name' => $unitName,
                                    ], [
                                        'unit_type' => $r['unit_type'] ?? 'department',
                                        'inclusion_reason' => $r['inclusion_reason'] ?? 'from_step1',
                                        'notes' => $r['notes'] ?? null,
                                    ]);
                                } catch (\Exception $e) {
                                    report($e);
                                }
                            }
                        }
                    }

                    // From skills
                    foreach (($skills ?? []) as $s) {
                        $doImport = false;
                        if (!empty($s['action']) && $s['action'] === 'import') $doImport = true;
                        if (!empty($s['import'])) $doImport = true;
                        if (!empty($s['include']) && $s['include'] === true && !isset($s['action'])) $doImport = true;

                        if ($doImport) {
                            $unitId = $s['unit_id'] ?? null;
                            $unitName = $s['unit_name'] ?? ($s['department'] ?? null);
                            if ($unitId || $unitName) {
                                try {
                                    WorkforcePlanScopeUnit::firstOrCreate([
                                        'workforce_plan_id' => $plan->id,
                                        'unit_id' => $unitId,
                                        'unit_name' => $unitName,
                                    ], [
                                        'unit_type' => $s['unit_type'] ?? 'department',
                                        'inclusion_reason' => $s['inclusion_reason'] ?? 'from_step1',
                                        'notes' => $s['rationale'] ?? null,
                                    ]);
                                } catch (\Exception $e) {
                                    report($e);
                                }
                            }
                        }
                    }
                });
            }

            $scenario->load(['template', 'skillDemands', 'workforcePlan.scopeRoles', 'workforcePlan.scopeUnits']);

            return response()->json([
                'success' => true,
                'message' => 'Step 1 saved successfully',
                'data' => $scenario,
            ]);
        }

        // Fallback: default update
        $scenario->update($data);

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
