<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organizations;
use App\Models\ScenarioGeneration;
use App\Services\ScenarioGenerationService;
use Illuminate\Http\Request;

class ScenarioGenerationController extends Controller
{
    public function store(Request $request, ScenarioGenerationService $svc)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'industry' => 'sometimes|string|max:255',
            'sub_industry' => 'sometimes|string|max:255',
            'company_size' => 'sometimes|integer',
            'geographic_scope' => 'sometimes|string',
            'organizational_cycle' => 'sometimes|string',
            'current_challenges' => 'sometimes|string',
            'current_capabilities' => 'sometimes|string',
            'current_gaps' => 'sometimes|string',
            'current_roles_count' => 'sometimes|integer',
            'has_formal_competency_model' => 'sometimes|boolean',
            'strategic_goal' => 'sometimes|string',
            'target_markets' => 'sometimes|string',
            'expected_growth' => 'sometimes|string',
            'transformation_type' => 'sometimes|array',
            'key_initiatives' => 'sometimes|string',
            'budget_level' => 'sometimes|string',
            'talent_availability' => 'sometimes|string',
            'training_capacity' => 'sometimes|string',
            'technology_maturity' => 'sometimes|string',
            'critical_constraints' => 'sometimes|string',
            'time_horizon' => 'sometimes|string',
            'urgency_level' => 'sometimes|string',
            'milestones' => 'sometimes|string',
            'organization_id' => 'sometimes|integer',
        ]);

        $requestedOrgId = $payload['organization_id'] ?? null;
        $orgId = $user->organization_id ?? null;

        if (! $orgId) {
            return response()->json(['success' => false, 'message' => 'organization_id is required'], 422);
        }

        if ($requestedOrgId !== null && (int) $requestedOrgId !== (int) $orgId) {
            return response()->json(['success' => false, 'message' => 'Forbidden: organization mismatch'], 403);
        }
        $org = Organizations::find($orgId);
        if (! $org) {
            return response()->json(['success' => false, 'message' => 'Organization not found'], 404);
        }

        $prompt = $svc->preparePrompt($payload, $user, $org);
        $generation = $svc->enqueueGeneration($prompt, $orgId, $user->id, ['initiator' => $user->id]);

        return response()->json(['success' => true, 'data' => ['id' => $generation->id, 'status' => $generation->status, 'url' => "/api/strategic-planning/scenarios/generate/{$generation->id}"]], 202);
    }

    public function preview(Request $request, ScenarioGenerationService $svc)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'industry' => 'sometimes|string|max:255',
            'sub_industry' => 'sometimes|string|max:255',
            'company_size' => 'sometimes|integer',
            'geographic_scope' => 'sometimes|string',
            'organizational_cycle' => 'sometimes|string',
            'current_challenges' => 'sometimes|string',
            'current_capabilities' => 'sometimes|string',
            'current_gaps' => 'sometimes|string',
            'current_roles_count' => 'sometimes|integer',
            'has_formal_competency_model' => 'sometimes|boolean',
            'strategic_goal' => 'sometimes|string',
            'target_markets' => 'sometimes|string',
            'expected_growth' => 'sometimes|string',
            'transformation_type' => 'sometimes|array',
            'key_initiatives' => 'sometimes|string',
            'budget_level' => 'sometimes|string',
            'talent_availability' => 'sometimes|string',
            'training_capacity' => 'sometimes|string',
            'technology_maturity' => 'sometimes|string',
            'critical_constraints' => 'sometimes|string',
            'time_horizon' => 'sometimes|string',
            'urgency_level' => 'sometimes|string',
            'milestones' => 'sometimes|string',
            'organization_id' => 'sometimes|integer',
        ]);

        $orgId = $payload['organization_id'] ?? ($user->organization_id ?? null);
        if (! $orgId) {
            return response()->json(['success' => false, 'message' => 'organization_id is required'], 422);
        }

        $org = Organizations::find($orgId);
        if (! $org) {
            return response()->json(['success' => false, 'message' => 'Organization not found'], 404);
        }

        $prompt = $svc->preparePrompt($payload, $user, $org);

        return response()->json(['success' => true, 'data' => ['prompt' => $prompt]]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $generation = ScenarioGeneration::find($id);
        if (! $generation) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        if ($generation->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $generation->id,
                'status' => $generation->status,
                'llm_response' => $generation->llm_response,
                'metadata' => $generation->metadata,
                'confidence_score' => $generation->confidence_score,
                'model_version' => $generation->model_version,
                'generated_at' => $generation->generated_at,
            ],
        ]);
    }

    public function accept(Request $request, $id)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $generation = ScenarioGeneration::find($id);
        if (! $generation) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        if ($generation->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if ($generation->status !== 'complete') {
            return response()->json(['success' => false, 'message' => 'Generation not complete'], 422);
        }

        // llm_response is stored redacted array
        $llm = $generation->llm_response ?? null;
        if (! is_array($llm)) {
            return response()->json(['success' => false, 'message' => 'Invalid LLM response stored'], 422);
        }

        $meta = $llm['scenario_metadata'] ?? [];

        $data = [
            'organization_id' => $generation->organization_id,
            'created_by' => $user->id,
            'name' => $meta['name'] ?? ('Generated Scenario '.$generation->id),
            'description' => $meta['description'] ?? null,
            'scenario_type' => $meta['scenario_type'] ?? 'transformation',
            'horizon_months' => $meta['horizon_months'] ?? ($meta['planning_horizon_months'] ?? 12),
            'fiscal_year' => $meta['fiscal_year'] ?? (int) date('Y'),
            'start_date' => $meta['start_date'] ?? now()->toDateString(),
            'end_date' => $meta['end_date'] ?? now()->addMonths($meta['horizon_months'] ?? 12)->toDateString(),
            'owner_user_id' => $meta['owner_user_id'] ?? $user->id,
            // preserve provenance
            'source_generation_id' => $generation->id,
            'accepted_prompt' => $generation->prompt,
            'accepted_prompt_redacted' => (bool) ($generation->redacted ?? true),
            'accepted_prompt_metadata' => $generation->metadata ?? null,
        ];

        $scenario = \App\Models\Scenario::create($data);

        // record acceptance on generation metadata
        $generation->metadata = array_merge($generation->metadata ?? [], ['accepted_by' => $user->id, 'accepted_at' => now()->toDateTimeString(), 'created_scenario_id' => $scenario->id]);
        $generation->save();

        return response()->json(['success' => true, 'message' => 'Scenario created from generation', 'data' => $scenario], 201);
    }
}
