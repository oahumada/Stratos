<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organizations;
use App\Models\ScenarioGeneration;
use App\Services\ScenarioGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ScenarioGenerationController extends Controller
{
    public function store(Request $request, ScenarioGenerationService $svc)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $rules = [
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
        ];

        if (Schema::hasTable('prompt_instructions')) {
            $rules['instruction_id'] = 'sometimes|integer|exists:prompt_instructions,id';
        }
        else {
            $rules['instruction_id'] = 'sometimes|integer';
        }

        $payload = $request->validate($rules);

        $requestedOrgId = $payload['organization_id'] ?? null;
        $orgId = $user->organization_id ?? null;

        if (!$orgId) {
            return response()->json(['success' => false, 'message' => 'organization_id is required'], 422);
        }

        if ($requestedOrgId !== null && (int)$requestedOrgId !== (int)$orgId) {
            return response()->json(['success' => false, 'message' => 'Forbidden: organization mismatch'], 403);
        }
        $org = Organizations::find($orgId);
        if (!$org) {
            return response()->json(['success' => false, 'message' => 'Organization not found'], 404);
        }

        // Compose prompt and include the operator instruction (DB/file/client)
        try {
            $composed = $svc->composePromptWithInstruction($payload, $user, $org, $payload['instruction_language'] ?? 'es', $payload['instruction_id'] ?? null);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid instruction', 'errors' => $e->errors()], 422);
        }
        $prompt = $composed['prompt'] ?? '';
        $instructionMeta = $composed['instruction'] ?? null;

        // Prefer Abacus provider for UI-initiated generations from the wizard.
        // Align behavior with `scripts/generate_via_abacus.php`: determine model from
        // config('services.abacus.model') or env, and include it as an explicit
        // override so the Abacus client uses the chosen model.
        $model = config('services.abacus.model') ?: env('ABACUS_MODEL', 'gpt-5');
        $providerOptions = [
            'overrides' => ['model' => $model],
        ];

        $metadata = array_merge([
            'initiator' => $user->id,
            'provider' => 'abacus',
            'provider_options' => $providerOptions,
            // record which model we attempted to use for traceability (mirrors script behavior)
            'used_provider_model' => $model,
        ], ['used_instruction' => $instructionMeta]);

        $generation = $svc->enqueueGeneration($prompt, $orgId, $user->id, $metadata);

        return response()->json(['success' => true, 'data' => ['id' => $generation->id, 'status' => $generation->status, 'url' => "/api/strategic-planning/scenarios/generate/{$generation->id}"]], 202);
    }

    public function preview(Request $request, ScenarioGenerationService $svc)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $rules = [
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
        ];

        if (Schema::hasTable('prompt_instructions')) {
            $rules['instruction_id'] = 'sometimes|integer|exists:prompt_instructions,id';
        }
        else {
            $rules['instruction_id'] = 'sometimes|integer';
        }

        $payload = $request->validate($rules);

        $orgId = $payload['organization_id'] ?? ($user->organization_id ?? null);
        if (!$orgId) {
            return response()->json(['success' => false, 'message' => 'organization_id is required'], 422);
        }

        $org = Organizations::find($orgId);
        if (!$org) {
            return response()->json(['success' => false, 'message' => 'Organization not found'], 404);
        }

        try {
            $composed = $svc->composePromptWithInstruction($payload, $user, $org, $payload['instruction_language'] ?? 'es', $payload['instruction_id'] ?? null);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid instruction', 'errors' => $e->errors()], 422);
        }
        $prompt = $composed['prompt'] ?? '';
        $instructionMeta = $composed['instruction'] ?? null;

        return response()->json(['success' => true, 'data' => ['prompt' => $prompt, 'instruction' => $instructionMeta]]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $generation = ScenarioGeneration::find($id);
        if (!$generation) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        // authorization for viewing is based on tenant membership
        if ($generation->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $progress = is_array($generation->metadata) && array_key_exists('progress', $generation->metadata) ? $generation->metadata['progress'] : null;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $generation->id,
                'status' => $generation->status,
                'llm_response' => $generation->llm_response,
                'metadata' => $generation->metadata,
                'progress' => $progress,
                'confidence_score' => $generation->confidence_score,
                'model_version' => $generation->model_version,
                'generated_at' => $generation->generated_at,
            ],
        ]);
    }

    public function accept(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $generation = ScenarioGeneration::find($id);
        if (!$generation) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }


        // authorize accept action via policy
        $this->authorize('accept', $generation);

        if ($generation->status !== 'complete') {
            return response()->json(['success' => false, 'message' => 'Generation not complete'], 422);
        }

        // llm_response is stored redacted array
        $llm = $generation->llm_response ?? null;
        if (!is_array($llm)) {
            return response()->json(['success' => false, 'message' => 'Invalid LLM response stored'], 422);
        }

        $meta = $llm['scenario_metadata'] ?? [];

        $data = [
            'organization_id' => $generation->organization_id,
            'created_by' => $user->id,
            'name' => $meta['name'] ?? ('Generated Scenario ' . $generation->id),
            'description' => $meta['description'] ?? null,
            'scenario_type' => $meta['scenario_type'] ?? 'transformation',
            'horizon_months' => $meta['horizon_months'] ?? ($meta['planning_horizon_months'] ?? 12),
            'fiscal_year' => $meta['fiscal_year'] ?? (int)date('Y'),
            'start_date' => $meta['start_date'] ?? now()->toDateString(),
            'end_date' => $meta['end_date'] ?? now()->addMonths($meta['horizon_months'] ?? 12)->toDateString(),
            'owner_user_id' => $meta['owner_user_id'] ?? $user->id,
            // preserve provenance
            'source_generation_id' => $generation->id,
            'accepted_prompt' => $generation->prompt,
            'accepted_prompt_redacted' => (bool)($generation->redacted ?? true),
            'accepted_prompt_metadata' => $generation->metadata ?? null,
        ];

        $scenario = \App\Models\Scenario::create($data);

        // ensure the generation record references the created scenario for quick lookups
        $generation->scenario_id = $scenario->id;

        // record acceptance on generation metadata
        $generation->metadata = array_merge($generation->metadata ?? [], ['accepted_by' => $user->id, 'accepted_at' => now()->toDateTimeString(), 'created_scenario_id' => $scenario->id]);
        $generation->save();

        // Optional: import capabilities/competencies/skills from the LLM JSON
        // Trigger when request includes `import=true` (UI/OPERATOR decision)
        if ($request->boolean('import', false)) {
            // feature-flag guard
            if (!config('features.import_generation')) {
                // record audit attempt
                $generation->metadata = array_merge($generation->metadata ?? [], ['import_audit' => array_merge($generation->metadata['import_audit'] ?? [], [[
                            'attempted_by' => $user->id,
                            'attempted_at' => now()->toDateTimeString(),
                            'import' => true,
                            'result' => 'skipped_feature_flag',
                        ]])]);
                $generation->save();
                return response()->json(['success' => false, 'message' => 'Import feature disabled'], 403);
            }

            // optional validation of llm_response
            if (config('features.validate_llm_response')) {
                try {
                    $validator = app(\App\Services\LlmResponseValidator::class);
                    $result = $validator->validate($llm);
                    if (!$result['valid']) {
                        // record validation failure in audit
                        $generation->metadata = array_merge($generation->metadata ?? [], ['import_audit' => array_merge($generation->metadata['import_audit'] ?? [], [[
                                    'attempted_by' => $user->id,
                                    'attempted_at' => now()->toDateTimeString(),
                                    'import' => true,
                                    'result' => 'validation_failed',
                                    'errors' => $result['errors'] ?? null,
                                ]])]);
                        $generation->save();
                        return response()->json(['success' => false, 'message' => 'Invalid LLM response', 'errors' => $result['errors']], 422);
                    }
                }
                catch (\Throwable $e) {
                    $generation->metadata = array_merge($generation->metadata ?? [], ['import_audit' => array_merge($generation->metadata['import_audit'] ?? [], [[
                                'attempted_by' => $user->id,
                                'attempted_at' => now()->toDateTimeString(),
                                'import' => true,
                                'result' => 'validation_error',
                                'error' => $e->getMessage(),
                            ]])]);
                    $generation->save();
                    return response()->json(['success' => false, 'message' => 'LLM validation error', 'error' => $e->getMessage()], 500);
                }
            }

            // record start of import attempt
            $generation->metadata = array_merge($generation->metadata ?? [], ['import_audit' => array_merge($generation->metadata['import_audit'] ?? [], [[
                        'attempted_by' => $user->id,
                        'attempted_at' => now()->toDateTimeString(),
                        'import' => true,
                        'result' => 'started',
                    ]])]);
            $generation->save();

            try {
                $importer = app(\App\Services\ScenarioGenerationImporter::class);
                $report = $importer->importGeneration($scenario, $generation);
                // record success
                $generation->metadata = array_merge($generation->metadata ?? [], ['import_audit' => array_merge($generation->metadata['import_audit'] ?? [], [[
                            'attempted_by' => $user->id,
                            'attempted_at' => now()->toDateTimeString(),
                            'import' => true,
                            'result' => 'success',
                            'report' => $report,
                        ]])]);
                $generation->save();
            }
            catch (\Throwable $e) {
                // record failure
                $generation->metadata = array_merge($generation->metadata ?? [], ['import_audit' => array_merge($generation->metadata['import_audit'] ?? [], [[
                            'attempted_by' => $user->id,
                            'attempted_at' => now()->toDateTimeString(),
                            'import' => true,
                            'result' => 'failed',
                            'error' => $e->getMessage(),
                        ]])]);
                $generation->save();
                // don't fail the overall accept flow; return report with error
                return response()->json(['success' => true, 'message' => 'Scenario created; import failed', 'data' => $scenario, 'import_errors' => [$e->getMessage()]], 201);
            }

            return response()->json(['success' => true, 'message' => 'Scenario created from generation and imported', 'data' => $scenario, 'import' => $report], 201);
        }

        return response()->json(['success' => true, 'message' => 'Scenario created from generation', 'data' => $scenario], 201);
    }

    /**
     * Create a prefilled demo generation and enqueue it.
     */
    public function demo(Request $request, ScenarioGenerationService $svc)
    {
    // ... (existing demo code)
    }

    /**
     * Simulation endpoint to test the full import flow using the test JSON file.
     */
    public function simulateImport(Request $request, ScenarioGenerationService $svc)
    {
        $user = $request->user();
        $orgId = $user->organization_id ?? $request->input('organization_id');

        if (!$orgId) {
            return response()->json(['success' => false, 'message' => 'organization_id required'], 422);
        }

        // 1. Load simulated JSON from the prompt_instructions directory
        $filePath = base_path('resources/prompt_instructions/llm_sim_response.md');
        if (!file_exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'Simulated response file not found at ' . $filePath], 404);
        }

        $content = file_get_contents($filePath);
        // Extract JSON from markdown code block if present
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $json = json_decode($matches[1], true);
        }
        else {
            $json = json_decode($content, true);
        }

        if (!$json) {
            return response()->json(['success' => false, 'message' => 'Failed to parse JSON from file'], 500);
        }

        // 2. Create a dummy ScenarioGeneration to track this import
        $generation = ScenarioGeneration::create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'status' => 'complete',
            'llm_response' => $json,
            'metadata' => ['simulated' => true, 'source_file' => 'llm_sim_response.md'],
            'generated_at' => now(),
        ]);

        // 3. Execute the import logic
        try {
            $report = $svc->finalizeScenarioImport($generation);
            return response()->json([
                'success' => true,
                'message' => 'Simulated import successful',
                'data' => $report
            ]);
        }
        catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
