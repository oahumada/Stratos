<?php

use App\Http\Controllers\Api\CatalogsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* // Catálogos dinámicos para selectores Route::get('/catalogs', function (Illuminate\Http\Request $request) {
 $endpoints = $request->query('endpoints', []);
 $result = [];
 foreach ((array) $endpoints as $endpoint) {
 try {
 $result[$endpoint] = match ($endpoint) {
 'role', 'roles' => \App\Models\Roles::select('id', 'name')->get(),
 'skill', 'skills' => \App\Models\Skill::select('id', 'name', 'category')->get(),
 'department', 'departments' => \App\Models\Department::select('id', 'name')->get(),
 default => [],
 };
 } catch (\Exception $e) {
 \Log::error("Catalog error for {$endpoint}: " . $e->getMessage());
 $result[$endpoint] = [];
 }
 }
 return response()->json($result); }); */

// Core services
Route::post('/gap-analysis', [\App\Http\Controllers\Api\GapAnalysisController::class , 'analyze']);
Route::get('/development-paths', [\App\Http\Controllers\Api\DevelopmentPathController::class , 'index']);
Route::post('/development-paths/generate', [\App\Http\Controllers\Api\DevelopmentPathController::class , 'generate']);
Route::delete('/development-paths/{id}', [\App\Http\Controllers\Api\DevelopmentPathController::class , 'destroy']);

// Job Openings (Día 4-5)
Route::get('/job-openings', [\App\Http\Controllers\Api\JobOpeningController::class , 'index']);
Route::get('/job-openings/{id}', [\App\Http\Controllers\Api\JobOpeningController::class , 'show']);
Route::get('/job-openings/{id}/candidates', [\App\Http\Controllers\Api\JobOpeningController::class , 'candidates']);

// Applications (Día 5)
Route::get('/applications', [\App\Http\Controllers\Api\ApplicationController::class , 'index']);
Route::get('/applications/{id}', [\App\Http\Controllers\Api\ApplicationController::class , 'show']);
Route::post('/applications', [\App\Http\Controllers\Api\ApplicationController::class , 'store']);
Route::patch('/applications/{id}', [\App\Http\Controllers\Api\ApplicationController::class , 'update']);

// CRUD genérico: People, Skills, Roles, Departments
// Gestionado por form-schema-complete.php con FormSchemaController

// Dashboard
Route::get('/dashboard/metrics', [\App\Http\Controllers\Api\DashboardController::class , 'metrics']);

// Marketplace (Día 5 - Internal opportunities)
Route::get('/people/{people_id}/marketplace', [\App\Http\Controllers\Api\MarketplaceController::class , 'opportunities']); // Vista candidato
Route::get('/marketplace/recruiter', [\App\Http\Controllers\Api\MarketplaceController::class , 'recruiterView']); // Vista reclutador

// Scenario Planning

// Lista simple de escenarios (stub para frontend durante desarrollo)
\Route::get('/scenario-planning-list', function () {
    return response()->json([
    [
    'id' => 1,
    'name' => 'Adopción de IA',
    'description' => 'Acelerar la adopción de IA en productos core',
    'scenario_type' => 'transformation',
    'status' => 'draft',
    'decision_status' => 'draft',
    'execution_status' => 'planned',
    'is_current_version' => true,
    'version_number' => 1,
    'time_horizon_weeks' => 24,
    'created_at' => now()->toDateTimeString(),
    ],
    [
    'id' => 2,
    'name' => 'Optimización de Plataforma',
    'description' => 'Reducir costos y mejorar escalabilidad',
    'scenario_type' => 'automation',
    'status' => 'active',
    'decision_status' => 'approved',
    'execution_status' => 'in_progress',
    'is_current_version' => true,
    'version_number' => 2,
    'time_horizon_weeks' => 52,
    'created_at' => now()->toDateTimeString(),
    ],
    ]);
});

// Scenario Planning API (canonical, without /v1 prefix)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/strategic-planning/scenarios', [\App\Http\Controllers\Api\ScenarioController::class , 'listScenarios']);
    // Prompt / Instruction management for generation wizard (register before the {id} route)
    Route::get('/strategic-planning/scenarios/instructions', [\App\Http\Controllers\Api\PromptInstructionController::class , 'index']);
    Route::post('/strategic-planning/scenarios/instructions', [\App\Http\Controllers\Api\PromptInstructionController::class , 'store']);
    Route::post('/strategic-planning/scenarios/instructions/restore-default', [\App\Http\Controllers\Api\PromptInstructionController::class , 'restoreDefault']);
    Route::get('/strategic-planning/scenarios/instructions/{id}', [\App\Http\Controllers\Api\PromptInstructionController::class , 'show']);
    Route::patch('/strategic-planning/scenarios/instructions/{id}', [\App\Http\Controllers\Api\PromptInstructionController::class , 'update']);

    Route::get('/strategic-planning/scenarios/{id}', [\App\Http\Controllers\Api\ScenarioController::class , 'showScenario']);
    Route::get('/strategic-planning/scenarios/{id}/capability-tree', [\App\Http\Controllers\Api\ScenarioController::class , 'getCapabilityTree']);

    // ChangeSet API: create, preview, apply
    Route::post('/strategic-planning/scenarios/{scenarioId}/change-sets', [\App\Http\Controllers\Api\ChangeSetController::class , 'store']);
    Route::get('/strategic-planning/change-sets/{id}/preview', [\App\Http\Controllers\Api\ChangeSetController::class , 'preview']);
    Route::post('/strategic-planning/change-sets/{id}/apply', [\App\Http\Controllers\Api\ChangeSetController::class , 'apply']);
    Route::post('/strategic-planning/change-sets/{id}/ops', [\App\Http\Controllers\Api\ChangeSetController::class , 'addOp']);
    Route::get('/strategic-planning/change-sets/{id}/can-apply', [\App\Http\Controllers\Api\ChangeSetController::class , 'canApply']);
    Route::post('/strategic-planning/change-sets/{id}/approve', [\App\Http\Controllers\Api\ChangeSetController::class , 'approve']);
    Route::post('/strategic-planning/change-sets/{id}/reject', [\App\Http\Controllers\Api\ChangeSetController::class , 'reject']);

    // Scenario generation (LLM-driven)
    Route::post('/strategic-planning/scenarios/generate', [\App\Http\Controllers\Api\ScenarioGenerationController::class , 'store']);
    // Create a demo prefilled generation for demo/testing
    Route::post('/strategic-planning/scenarios/generate/demo', [\App\Http\Controllers\Api\ScenarioGenerationController::class , 'demo']);
    Route::post('/strategic-planning/scenarios/generate/preview', [\App\Http\Controllers\Api\ScenarioGenerationController::class , 'preview']);
    // ABACUS-backed immediate generation endpoint (uses AbacusClient streaming)
    Route::post('/strategic-planning/scenarios/generate/abacus', [\App\Http\Controllers\Api\ScenarioGenerationAbacusController::class , 'generate']);
    Route::get('/strategic-planning/scenarios/generate/{id}', [\App\Http\Controllers\Api\ScenarioGenerationController::class , 'show']);
    Route::post('/strategic-planning/scenarios/generate/{id}/accept', [\App\Http\Controllers\Api\ScenarioGenerationController::class , 'accept']);
    // Read streaming chunks for a generation (for UI progress/debug)
    Route::get('/strategic-planning/scenarios/generate/{id}/chunks', [\App\Http\Controllers\Api\GenerationChunkController::class , 'index']);
    // Read compacted blob (decoded) for a generation (if compacted exists)
    Route::get('/strategic-planning/scenarios/generate/{id}/compacted', [\App\Http\Controllers\Api\GenerationChunkController::class , 'compacted']);
    // Lightweight progress endpoint used by the wizard for polling progress and quick assembly
    Route::get('/strategic-planning/scenarios/generate/{id}/progress', [\App\Http\Controllers\Api\GenerationChunkController::class , 'progress']);

    // Scenario Planning - Simulation & Strategic Talent Modeling
    Route::prefix('strategic-planning')->group(function () {
            // Simulation (Paso 1 - Final Stage)
            Route::post('/scenarios/{id}/simulate-growth', [\App\Http\Controllers\Api\ScenarioSimulationController::class , 'simulateGrowth']);
            Route::get('/critical-talents', [\App\Http\Controllers\Api\ScenarioSimulationController::class , 'getCriticalTalents']);

            // ROI Calculator (CFO Perspective)
            Route::post('/roi-calculator/calculate', [\App\Http\Controllers\Api\ScenarioRoiController::class , 'calculate']);
            Route::get('/roi-calculator/scenarios', [\App\Http\Controllers\Api\ScenarioRoiController::class , 'listCalculations']);

            // Strategy Assignment (CHRO Perspective - 4B)
            Route::get('/scenarios/{id}/gaps-for-assignment', [\App\Http\Controllers\Api\ScenarioStrategyController::class , 'getGapsForAssignment']);
            Route::post('/strategies/assign', [\App\Http\Controllers\Api\ScenarioStrategyController::class , 'assignStrategy']);
            Route::get('/strategies/portfolio/{scenario_id}', [\App\Http\Controllers\Api\ScenarioStrategyController::class , 'getStrategyPortfolio']);

            // Test/Simulation Endpoints
            Route::post('/scenarios/simulate-import', [\App\Http\Controllers\Api\ScenarioGenerationController::class , 'simulateImport']);
        }
        );

        // Lightweight telemetry endpoint for frontend analytics (logs event server-side)
        Route::post('/telemetry/event', [\App\Http\Controllers\Api\TelemetryController::class , 'store']);

        // Prompt / Instruction management for generation wizard (moved earlier to avoid parameter conflicts)
    
        // Dev API: manage capability_competencies pivot (competency assignments per capability per scenario)
        // Supports both creating new competencies and attaching existing ones
        Route::post('/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies', function (Illuminate\Http\Request $request, $scenarioId, $capabilityId) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $scenario = App\Models\Scenario::find($scenarioId);
            if (!$scenario) {
                return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
            }
            if ($scenario->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $cap = App\Models\Capability::find($capabilityId);
            if (!$cap) {
                return response()->json(['success' => false, 'message' => 'Capability not found'], 404);
            }

            // Accept either `competency_id` (use existing) or `competency` payload to create a new competency.
            $competencyId = $request->input('competency_id');

            try {
                $result = \DB::transaction(function () use ($request, $scenarioId, $capabilityId, $competencyId, $user) {
                            // If competency_id provided, validate existence and tenant
                            if ($competencyId) {
                                $comp = App\Models\Competency::find($competencyId);
                                if (!$comp) {
                                    throw new \Exception('Competency not found');
                                }
                                if ($comp->organization_id !== ($user->organization_id ?? null)) {
                                    throw new \Exception('Forbidden');
                                }
                                $createdCompetencyId = $comp->id;
                            }
                            else {
                                // Create new competency (without capability_id; the relationship is via the pivot table)
                                $payload = $request->input('competency', []);
                                $name = trim($payload['name'] ?? '');
                                if (empty($name)) {
                                    throw new \Exception('Competency name is required');
                                }
                                $comp = App\Models\Competency::create([
                                    'organization_id' => $user->organization_id ?? null,
                                    'name' => $name,
                                    'description' => $payload['description'] ?? null,
                                ]);
                                $createdCompetencyId = $comp->id;
                            }

                            // Prepare pivot insert
                            // Accept both `weight` and `strategic_weight` from frontend (map `strategic_weight` -> `weight`).
                            $resolvedWeight = null;
                            if ($request->has('weight')) {
                                $resolvedWeight = (int)$request->input('weight');
                            }
                            elseif ($request->has('strategic_weight')) {
                                $resolvedWeight = (int)$request->input('strategic_weight');
                            }

                            $insert = [
                                'scenario_id' => $scenarioId,
                                'capability_id' => $capabilityId,
                                'competency_id' => $createdCompetencyId,
                                'required_level' => (int)$request->input('required_level', 3),
                                'rationale' => $request->input('rationale', null),
                                'is_required' => (bool)$request->input('is_required', false),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            // Only include optional columns if they exist in the DB schema (tests may run against different snapshots)
                            if (\Illuminate\Support\Facades\Schema::hasColumn('capability_competencies', 'strategic_weight')) {
                                $insert['strategic_weight'] = $resolvedWeight;
                            }
                            elseif (\Illuminate\Support\Facades\Schema::hasColumn('capability_competencies', 'weight')) {
                                $insert['weight'] = $resolvedWeight;
                            }
                            if (\Illuminate\Support\Facades\Schema::hasColumn('capability_competencies', 'priority')) {
                                $insert['priority'] = $request->has('priority') ? (int)$request->input('priority') : null;
                            }

                            $exists = \DB::table('capability_competencies')
                                ->where('scenario_id', $scenarioId)
                                ->where('capability_id', $capabilityId)
                                ->where('competency_id', $createdCompetencyId)
                                ->exists();
                            if ($exists) {
                                // return existing row info
                                $row = \DB::table('capability_competencies')
                                    ->where('scenario_id', $scenarioId)
                                    ->where('capability_id', $capabilityId)
                                    ->where('competency_id', $createdCompetencyId)
                                    ->first();

                                return ['status' => 'exists', 'row' => $row];
                            }

                            \DB::table('capability_competencies')->insert($insert);

                            return ['status' => 'created', 'data' => $insert];
                        }
                        );

                        if ($result['status'] === 'created') {
                            return response()->json(['success' => true, 'data' => $result['data']], 201);
                        }

                        return response()->json(['success' => true, 'data' => $result['row'], 'note' => 'already_exists'], 200);
                    }
                    catch (\Exception $e) {
                        \Log::error('Error creating capability_competency (POST): ' . $e->getMessage(), ['scenario_id' => $scenarioId, 'capability_id' => $capabilityId]);
                        if (str_contains($e->getMessage(), 'Forbidden')) {
                            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
                        }
                        if (str_contains($e->getMessage(), 'Competency name is required')) {
                            return response()->json(['success' => false, 'message' => 'Competency name is required'], 422);
                        }

                        return response()->json(['success' => false, 'message' => 'Server error creating relation'], 500);
                    }
                }
                );

                Route::patch('/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}', function (Illuminate\Http\Request $request, $scenarioId, $capabilityId, $competencyId) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $scenario = App\Models\Scenario::find($scenarioId);
            if (!$scenario) {
                return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
            }
            if ($scenario->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            $exists = \DB::table('capability_competencies')
                ->where('scenario_id', $scenarioId)
                ->where('capability_id', $capabilityId)
                ->where('competency_id', $competencyId)
                ->exists();
            if (!$exists) {
                return response()->json(['success' => false, 'message' => 'Relation not found'], 404);
            }
            $update = [];
            // Support updating `priority` and accept `strategic_weight` as alias for `weight`.
            foreach (['required_level', 'strategic_weight', 'priority', 'rationale', 'is_required'] as $f) {
                if ($request->has($f)) {
                    $update[$f] = $request->input($f);
                }
            }

            // Accept `weight` as legacy alias and map to `strategic_weight` in the update payload.
            if ($request->has('weight')) {
                $update['strategic_weight'] = $request->input('weight');
            }
            // Also accept `is_critical` from UI and map to pivot's `is_required` boolean.
            if ($request->has('is_critical') && !$request->has('is_required')) {
                $update['is_required'] = $request->input('is_critical');
            }

            // Normalize weight field to whatever column exists in DB to support different snapshots.
            if (array_key_exists('strategic_weight', $update)) {
                if (
                !\Illuminate\Support\Facades\Schema::hasColumn('capability_competencies', 'strategic_weight')
                && \Illuminate\Support\Facades\Schema::hasColumn('capability_competencies', 'weight')
                ) {
                    $update['weight'] = $update['strategic_weight'];
                    unset($update['strategic_weight']);
                }
            }
            elseif (array_key_exists('weight', $update)) {
                if (
                !\Illuminate\Support\Facades\Schema::hasColumn('capability_competencies', 'weight')
                && \Illuminate\Support\Facades\Schema::hasColumn('capability_competencies', 'strategic_weight')
                ) {
                    $update['strategic_weight'] = $update['weight'];
                    unset($update['weight']);
                }
            }

            if (!empty($update)) {
                $update['updated_at'] = now();
                // Filter update keys to existing columns to avoid sqlite "no such column" errors in tests
                $filtered = [];
                foreach ($update as $k => $v) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('capability_competencies', $k)) {
                        $filtered[$k] = $v;
                    }
                }
                if (!empty($filtered)) {
                    \DB::table('capability_competencies')
                        ->where('scenario_id', $scenarioId)
                        ->where('capability_id', $capabilityId)
                        ->where('competency_id', $competencyId)
                        ->update($filtered);
                }
            }

            return response()->json(['success' => true, 'updated' => $update]);
        }
        );

        Route::delete('/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}', function (Illuminate\Http\Request $request, $scenarioId, $capabilityId, $competencyId) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $scenario = App\Models\Scenario::find($scenarioId);
            if (!$scenario) {
                return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
            }
            if ($scenario->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            $deleted = \DB::table('capability_competencies')
                ->where('scenario_id', $scenarioId)
                ->where('capability_id', $capabilityId)
                ->where('competency_id', $competencyId)
                ->delete();
            if ($deleted) {
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Relation not found'], 404);
        }
        );

        // Dev API: create a Capability under a Scenario (multi-tenant)
        Route::post('/strategic-planning/scenarios/{id}/capabilities', function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $scenario = App\Models\Scenario::find($id);
            if (!$scenario) {
                return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
            }
            try {
                $data = $request->only(['name', 'description', 'importance', 'is_critical']);
                if (empty($data['name'])) {
                    return response()->json(['success' => false, 'message' => 'Name is required'], 422);
                }
                // Create capability WITHOUT setting `discovered_in_scenario_id` here so
                // the route can control the pivot insertion with the provided pivot fields.
                // The Capability model has a booted() hook that inserts a pivot when
                // `discovered_in_scenario_id` is set; creating without it avoids an
                // early insert with defaults.
                $cap = App\Models\Capability::create([
                    'organization_id' => $user->organization_id ?? null,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    // importance column is NOT NULL in sqlite; default defined in migration.
                    // Avoid inserting explicit NULL which violates constraint — use default 3 when missing.
                    'importance' => isset($data['importance']) ? $data['importance'] : 3,
                ]);

                // Insert relationship-specific attributes into pivot `scenario_capabilities`.
                // Only include fields that belong to the relationship (not to Capability entity).
                $pivot = [
                    'scenario_id' => $scenario->id,
                    'capability_id' => $cap->id,
                    'strategic_role' => $request->input('strategic_role', 'target'),
                    'strategic_weight' => (int)$request->input('strategic_weight', 10),
                    'priority' => (int)$request->input('priority', 1),
                    'rationale' => $request->input('rationale', null),
                    'required_level' => (int)$request->input('required_level', 3),
                    'is_critical' => (bool)$request->input('is_critical', false),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Respect unique constraint; insert only if not exists.
                if (
                !\DB::table('scenario_capabilities')
                ->where('scenario_id', $scenario->id)
                ->where('capability_id', $cap->id)
                ->exists()
                ) {
                    \DB::table('scenario_capabilities')->insert($pivot);
                }

                return response()->json(['success' => true, 'data' => $cap], 201);
            }
            catch (\Throwable $e) {
                \Log::error('Error creating capability for scenario ' . $id . ': ' . $e->getMessage(), [
                    'exception' => $e,
                    'payload' => $request->all(),
                    'user_id' => $user->id ?? null,
                    'scenario_id' => $id,
                ]);

                return response()->json(['success' => false, 'message' => 'Server error creating capability', 'error' => $e->getMessage()], 500);
            }
        }
        );

        // Competency versions CRUD
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/competencies/{competencyId}/versions', [App\Http\Controllers\CompetencyVersionController::class , 'index']);
            Route::post('/competencies/{competencyId}/versions', [App\Http\Controllers\CompetencyVersionController::class , 'store']);
            Route::get('/competencies/{competencyId}/versions/{id}', [App\Http\Controllers\CompetencyVersionController::class , 'show']);
            Route::delete('/competencies/{competencyId}/versions/{id}', [App\Http\Controllers\CompetencyVersionController::class , 'destroy']);
        }
        );

        // Transform competency: create new competency_version from existing competency
        Route::middleware(['auth:sanctum'])->post('/competencies/{competencyId}/transform', [App\Http\Controllers\TransformCompetencyController::class , 'transform']);

        // Dev API: retrieve a single Capability entity (multi-tenant safe)
        Route::get('/capabilities/{id}', function ($id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $cap = App\Models\Capability::find($id);
            if (!$cap) {
                return response()->json(['success' => false, 'message' => 'Capability not found'], 404);
            }
            if (isset($cap->organization_id) && $cap->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            return response()->json(['success' => true, 'data' => $cap->toArray()]);
        }
        );

        // Promote a single incubated capability (remove discovered flag and mark active)
        Route::post('/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/promote', function (Illuminate\Http\Request $request, $scenarioId, $capabilityId) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $scenario = App\Models\Scenario::find($scenarioId);
            if (!$scenario) {
                return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
            }
            if ($scenario->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            $cap = App\Models\Capability::find($capabilityId);
            if (!$cap) {
                return response()->json(['success' => false, 'message' => 'Capability not found'], 404);
            }
            if (isset($cap->discovered_in_scenario_id) && (int)$cap->discovered_in_scenario_id !== (int)$scenarioId) {
                return response()->json(['success' => false, 'message' => 'Capability not associated with this scenario'], 422);
            }

            try {
                $cap->discovered_in_scenario_id = null;
                $cap->status = 'active';
                $cap->save();

                return response()->json(['success' => true, 'data' => $cap]);
            }
            catch (\Throwable $e) {
                \Log::error('Error promoting capability: ' . $e->getMessage(), ['capability_id' => $capabilityId]);
                return response()->json(['success' => false, 'message' => 'Server error promoting capability'], 500);
            }
        }
        );

        // Record an accepted_prompt view for a generation (audit)
        Route::post('/strategic-planning/scenarios/generate/{id}/record-view', function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $generation = App\Models\ScenarioGeneration::find($id);
            if (!$generation) {
                return response()->json(['success' => false, 'message' => 'Not found'], 404);
            }
            if ($generation->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $generation->metadata = array_merge($generation->metadata ?? [], ['accepted_prompt_views' => array_merge($generation->metadata['accepted_prompt_views'] ?? [], [[
                        'viewed_by' => $user->id,
                        'viewed_at' => now()->toDateTimeString(),
                    ]])]);
            $generation->save();

            return response()->json(['success' => true]);
        }
        );

        // Dev API: update a Capability entity (multi-tenant safe)
        Route::patch('/capabilities/{id}', function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $cap = App\Models\Capability::find($id);
            if (!$cap) {
                return response()->json(['success' => false, 'message' => 'Capability not found'], 404);
            }
            if (isset($cap->organization_id) && $cap->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            try {
                $data = $request->only(['name', 'description', 'importance', 'position_x', 'position_y', 'type', 'category', 'is_critical']);
                // Only set fields that are present
                foreach ($data as $k => $v) {
                    if ($v !== null) {
                        $cap->{ $k} = $v;
                    }
                }
                $cap->save();

                return response()->json(['success' => true, 'data' => $cap]);
            }
            catch (\Throwable $e) {
                \Log::error('Error updating capability ' . $id . ': ' . $e->getMessage());

                return response()->json(['success' => false, 'message' => 'Server error updating capability'], 500);
            }
        }
        );

        // Dev API: retrieve a single Competency entity (multi-tenant safe)
        Route::get('/competencies/{id}', function ($id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $comp = App\Models\Competency::find($id);
            if (!$comp) {
                return response()->json(['success' => false, 'message' => 'Competency not found'], 404);
            }
            if (isset($comp->organization_id) && $comp->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            return response()->json(['success' => true, 'data' => $comp->toArray()]);
        }
        );

        // Dev API: create a Competency entity (multi-tenant safe)
        Route::post('/competencies', function (Illuminate\Http\Request $request) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $name = trim($request->input('name', ''));
            if (empty($name)) {
                return response()->json(['success' => false, 'message' => 'Name is required'], 422);
            }
            try {
                $comp = App\Models\Competency::create([
                    'organization_id' => $user->organization_id ?? null,
                    'name' => $name,
                    'description' => $request->input('description', null),
                ]);

                return response()->json(['success' => true, 'data' => $comp], 201);
            }
            catch (\Throwable $e) {
                \Log::error('Error creating competency: ' . $e->getMessage());

                return response()->json(['success' => false, 'message' => 'Server error creating competency'], 500);
            }
        }
        );

        // Dev API: delete a Competency entity (multi-tenant safe)
        Route::delete('/competencies/{id}', function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $comp = App\Models\Competency::find($id);
            if (!$comp) {
                return response()->json(['success' => false, 'message' => 'Competency not found'], 404);
            }
            if (isset($comp->organization_id) && $comp->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            try {
                $comp->delete();

                return response()->json(['success' => true, 'message' => 'Competency deleted']);
            }
            catch (\Throwable $e) {
                \Log::error('Error deleting competency ' . $id . ': ' . $e->getMessage());

                return response()->json(['success' => false, 'message' => 'Server error deleting competency'], 500);
            }
        }
        );

        // Dev API: update a Competency entity (multi-tenant safe)
        Route::patch('/competencies/{id}', function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $comp = App\Models\Competency::find($id);
            if (!$comp) {
                return response()->json(['success' => false, 'message' => 'Competency not found'], 404);
            }
            if (isset($comp->organization_id) && $comp->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            try {
                // Only accept name, description, skills. 'readiness' is a calculated field and cannot be saved.
                $data = $request->only(['name', 'description', 'skills']);
                // Only set fields that are present
                foreach ($data as $k => $v) {
                    if ($v !== null) {
                        if ($k === 'skills' && is_array($v)) {
                            $comp->skills()->sync($v);
                        }
                        else {
                            $comp->{ $k} = $v;
                        }
                    }
                }
                $comp->save();

                return response()->json(['success' => true, 'data' => $comp]);
            }
            catch (\Throwable $e) {
                \Log::error('Error updating competency ' . $id . ': ' . $e->getMessage());

                return response()->json(['success' => false, 'message' => 'Server error updating competency'], 500);
            }
        }
        );

        // Dev API: delete a CompetencySkill relation (remove a skill from a competency)
        Route::delete('/competencies/{competencyId}/skills/{skillId}', function (Illuminate\Http\Request $request, $competencyId, $skillId) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $comp = App\Models\Competency::find($competencyId);
            if (!$comp) {
                return response()->json(['success' => false, 'message' => 'Competency not found'], 404);
            }
            if (isset($comp->organization_id) && $comp->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            // Check the skill exists and belongs to user's organization
            $skill = App\Models\Skill::find($skillId);
            if (!$skill) {
                return response()->json(['success' => false, 'message' => 'Skill not found'], 404);
            }
            if (isset($skill->organization_id) && $skill->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            // Log the deletion attempt
            \Log::info('[DELETE /competencies/{competencyId}/skills/{skillId}]', [
                'competencyId' => $competencyId,
                'skillId' => $skillId,
                'user_id' => $user->id,
            ]);

            // Delete the skill completely (cascade will remove pivot records)
            try {
                // First remove from pivot table (competency_skills)
                \DB::table('competency_skills')
                    ->where('skill_id', $skillId)
                    ->delete();

                \Log::info('[DELETE] Removed all competency_skills relations for skill:', ['skillId' => $skillId]);

                // Then delete the skill itself
                $deleted = $skill->delete();

                \Log::info('[DELETE] Skill deletion result:', ['deleted' => $deleted, 'skillId' => $skillId]);

                if (!$deleted) {
                    return response()->json(['success' => false, 'message' => 'Could not delete skill'], 400);
                }

                return response()->json(['success' => true, 'message' => 'Skill deleted successfully']);
            }
            catch (\Throwable $e) {
                \Log::error('Error deleting skill: ' . $e->getMessage(), [
                    'exception' => $e,
                    'competencyId' => $competencyId,
                    'skillId' => $skillId,
                ]);

                return response()->json(['success' => false, 'message' => 'Server error deleting skill', 'error' => $e->getMessage()], 500);
            }
        }
        );

        // Dev API: update pivot attributes for scenario_capabilities
        Route::patch('/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}', function (Illuminate\Http\Request $request, $scenarioId, $capabilityId) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $scenario = App\Models\Scenario::find($scenarioId);
            if (!$scenario) {
                return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
            }
            if ($scenario->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            // debug logging to help frontend troubleshooting (dev-only)
            \Log::info('PATCH scenario-capability called', [
                'user_id' => $user->id ?? null,
                'user_org' => $user->organization_id ?? null,
                'scenario_id' => $scenarioId,
                'capability_id' => $capabilityId,
                'input' => $request->all(),
            ]);

            $exists = \DB::table('scenario_capabilities')
                ->where('scenario_id', $scenarioId)
                ->where('capability_id', $capabilityId)
                ->exists();

            // If the pivot relation does not exist, create it (upsert behavior)
            if (!$exists) {
                \Log::info('Pivot relation not found; creating new relation (upsert)', ['scenario_id' => $scenarioId, 'capability_id' => $capabilityId]);
                // build pivot values with sensible defaults
                $insert = [
                    'scenario_id' => $scenarioId,
                    'capability_id' => $capabilityId,
                    'strategic_role' => $request->input('strategic_role', 'target'),
                    'strategic_weight' => (int)$request->input('strategic_weight', 10),
                    'priority' => (int)$request->input('priority', 1),
                    'rationale' => $request->input('rationale', null),
                    'required_level' => (int)$request->input('required_level', 3),
                    'is_critical' => (bool)$request->input('is_critical', false),
                    'position_x' => $request->has('position_x') ? $request->input('position_x') : null,
                    'position_y' => $request->has('position_y') ? $request->input('position_y') : null,
                    'is_fixed' => (bool)$request->input('is_fixed', false),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                try {
                    \DB::table('scenario_capabilities')->insert($insert);
                    // mark that we created the relation so frontend can act accordingly
                    $exists = true;
                    \Log::info('Inserted pivot relation', ['insert' => $insert]);
                }
                catch (\Throwable $e) {
                    \Log::error('Failed to insert pivot relation during PATCH upsert: ' . $e->getMessage(), ['exception' => $e]);

                    return response()->json(['success' => false, 'message' => 'Failed to create relation'], 500);
                }
            }
            $update = [];
            $fields = ['strategic_role', 'strategic_weight', 'priority', 'rationale', 'required_level', 'is_critical', 'position_x', 'position_y', 'is_fixed'];
            foreach ($fields as $f) {
                if ($request->has($f)) {
                    $update[$f] = $request->input($f);
                }
            }
            if (!empty($update)) {
                $update['updated_at'] = now();
                \DB::table('scenario_capabilities')
                    ->where('scenario_id', $scenarioId)
                    ->where('capability_id', $capabilityId)
                    ->update($update);
            }
            \Log::info('Relation updated for scenario-capability', ['scenario_id' => $scenarioId, 'capability_id' => $capabilityId, 'updated' => $update]);

            return response()->json(['success' => true, 'message' => 'Relation updated', 'relation_exists' => true, 'updated' => $update]);
        }
        );

        // Read a competency including its related skills (multi-tenant safe)
        Route::get('/competencies/{id}', function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $comp = App\Models\Competency::with('skills')->find($id);
            if (!$comp) {
                return response()->json(['success' => false, 'message' => 'Competency not found'], 404);
            }
            if (isset($comp->organization_id) && $comp->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            return response()->json(['success' => true, 'data' => $comp]);
        }
        );

        // Shortcut endpoint returning only skills for a competency (multi-tenant safe)
        Route::get('/competencies/{id}/skills', function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            $comp = App\Models\Competency::with('skills')->find($id);
            if (!$comp) {
                return response()->json(['success' => false, 'message' => 'Competency not found'], 404);
            }
            if (isset($comp->organization_id) && $comp->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            $skills = $comp->skills ?? [];

            return response()->json(['success' => true, 'data' => $skills]);
        }
        );

        // Dev API: attach a skill to a competency. Accepts either `skill_id` (existing) or `skill` payload to create then attach.
        Route::post('/competencies/{id}/skills', function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $comp = App\Models\Competency::find($id);
            if (!$comp) {
                return response()->json(['success' => false, 'message' => 'Competency not found'], 404);
            }
            if (isset($comp->organization_id) && $comp->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            try {
                $result = \DB::transaction(function () use ($request, $comp, $user) {
                            $skillId = $request->input('skill_id');
                            $createdSkill = null;

                            if ($skillId) {
                                $skill = App\Models\Skill::find($skillId);
                                if (!$skill) {
                                    throw new \Exception('Skill not found');
                                }
                                if (isset($skill->organization_id) && $skill->organization_id !== ($user->organization_id ?? null)) {
                                    throw new \Exception('Forbidden');
                                }
                                $skillToAttach = $skill;
                            }
                            else {
                                $payload = $request->input('skill', []);
                                $name = trim($payload['name'] ?? '');
                                if (empty($name)) {
                                    throw new \Exception('Skill name is required');
                                }

                                // Buscar skill existente con el mismo nombre en la organización
                                $existingSkill = App\Models\Skill::where('organization_id', $user->organization_id ?? null)
                                    ->where('name', $name)
                                    ->first();

                                if ($existingSkill) {
                                    // Skill duplicada - informar al usuario
                                    throw new \Exception('Skill duplicada: Ya existe una skill con el nombre "' . $name . '". Use una existente o cree una con nombre diferente.');
                                }
                                else {
                                    // Crear nueva skill
                                    $createdSkill = App\Models\Skill::create([
                                        'organization_id' => $user->organization_id ?? null,
                                        'name' => $name,
                                        'description' => $payload['description'] ?? null,
                                        'category' => $payload['category'] ?? null,
                                    ]);
                                    $skillToAttach = $createdSkill;
                                }
                            }

                            // Avoid duplicate pivot
                            $exists = \DB::table('competency_skills')
                                ->where('competency_id', $comp->id)
                                ->where('skill_id', $skillToAttach->id)
                                ->exists();
                            if (!$exists) {
                                $insert = [
                                    'competency_id' => $comp->id,
                                    'skill_id' => $skillToAttach->id,
                                    'weight' => (int)$request->input('weight', 10),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                                \DB::table('competency_skills')->insert($insert);
                            }

                            return ['skill' => $skillToAttach];
                        }
                        );

                        return response()->json(['success' => true, 'data' => $result['skill']], 201);
                    }
                    catch (\Exception $e) {
                        if (str_contains($e->getMessage(), 'Forbidden')) {
                            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
                        }
                        if (str_contains($e->getMessage(), 'Skill name is required')) {
                            return response()->json(['success' => false, 'message' => 'Skill name is required'], 422);
                        }
                        if (str_contains($e->getMessage(), 'Skill duplicada:')) {
                            return response()->json(['success' => false, 'message' => $e->getMessage()], 409);
                        }
                        \Log::error('Error attaching skill to competency: ' . $e->getMessage(), ['competency_id' => $id]);

                        return response()->json(['success' => false, 'message' => 'Server error attaching skill'], 500);
                    }
                }
                );

                // Dev API: delete the relationship (pivot) between a scenario and a capability
                Route::delete('/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}', function (Illuminate\Http\Request $request, $scenarioId, $capabilityId) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            // ensure scenario exists and belongs to user org
            $scenario = App\Models\Scenario::find($scenarioId);
            if (!$scenario) {
                return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
            }
            if ($scenario->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            $deleted = \DB::table('scenario_capabilities')
                ->where('scenario_id', $scenarioId)
                ->where('capability_id', $capabilityId)
                ->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Relationship deleted']);
            }

            return response()->json(['success' => false, 'message' => 'Relation not found'], 404);
        }
        );

        // Dev API: delete a Capability entity (multi-tenant safe)
        Route::delete('/capabilities/{id}', function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $cap = App\Models\Capability::find($id);
            if (!$cap) {
                return response()->json(['success' => false, 'message' => 'Capability not found'], 404);
            }

            // Tenant check if capability has organization_id
            if (isset($cap->organization_id) && $cap->organization_id !== ($user->organization_id ?? null)) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }

            try {
                $cap->delete();

                return response()->json(['success' => true, 'message' => 'Capability deleted']);
            }
            catch (\Throwable $e) {
                \Log::error('Error deleting capability ' . $id . ': ' . $e->getMessage());

                return response()->json(['success' => false, 'message' => 'Server error deleting capability'], 500);
            }
        }
        );
    });

// Dev-only: accept saved positions for prototype mapping (no auth)
Route::post('/strategic-planning/scenarios/{id}/capability-tree/save-positions', function (Request $request, $id) {
    // Persist positions for prototype mapping. This endpoint is dev-friendly but will persist into pivot.
    $positions = $request->input('positions', []);
    $updated = 0;
    $inserted = 0;
    foreach ($positions as $p) {
        $capId = $p['id'] ?? $p['capability_id'] ?? null;
        if (!$capId) {
            continue;
        }
        $x = array_key_exists('x', $p) ? $p['x'] : null;
        $y = array_key_exists('y', $p) ? $p['y'] : null;
        $isFixed = array_key_exists('is_fixed', $p) ? (bool)$p['is_fixed'] : true;

        try {
            $exists = \DB::table('scenario_capabilities')
                ->where('scenario_id', $id)
                ->where('capability_id', $capId)
                ->exists();
            if ($exists) {
                \DB::table('scenario_capabilities')
                    ->where('scenario_id', $id)
                    ->where('capability_id', $capId)
                    ->update([
                    'position_x' => $x,
                    'position_y' => $y,
                    'is_fixed' => $isFixed,
                    'updated_at' => now(),
                ]);
                $updated++;
            }
            else {
                // create minimal pivot row with sensible defaults plus positions
                \DB::table('scenario_capabilities')->insert([
                    'scenario_id' => $id,
                    'capability_id' => $capId,
                    'strategic_role' => 'target',
                    'strategic_weight' => 10,
                    'priority' => 1,
                    'rationale' => null,
                    'required_level' => 3,
                    'is_critical' => false,
                    'position_x' => $x,
                    'position_y' => $y,
                    'is_fixed' => $isFixed,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            }
        }
        catch (\Throwable $e) {
            \Log::error('Error saving position for cap ' . $capId . ': ' . $e->getMessage());
        }
    }
    \Log::info('Saved positions for scenario ' . $id, ['updated' => $updated, 'inserted' => $inserted]);

    return response()->json(['status' => 'ok', 'updated' => $updated, 'inserted' => $inserted]);
});

// Canonical Strategic Planning API - Normalized naming (replaced workforce-planning)
Route::prefix('strategic-planning')->group(function () {
    Route::get('scenarios', [\App\Http\Controllers\Api\ScenarioController::class , 'listScenarios']);
    Route::post('scenarios', [\App\Http\Controllers\Api\ScenarioController::class , 'store']);
    Route::get('scenarios/{id}', [\App\Http\Controllers\Api\ScenarioController::class , 'showScenario']);
    Route::patch('scenarios/{id}', [\App\Http\Controllers\Api\ScenarioController::class , 'updateScenario']);
    Route::post('scenarios/{template_id}/instantiate-from-template', [\App\Http\Controllers\Api\ScenarioController::class , 'instantiateFromTemplate']);
    Route::post('scenarios/{id}/calculate-gaps', [\App\Http\Controllers\Api\ScenarioController::class , 'calculateGaps']);
    Route::post('scenarios/{id}/refresh-suggested-strategies', [\App\Http\Controllers\Api\ScenarioController::class , 'refreshSuggestedStrategies']);
    Route::get('scenario-templates', [\App\Http\Controllers\Api\ScenarioTemplateController::class , 'index']);
});

//Talent Engineering
// Orquestación (nueva)
Route::post('/strategic-planning/scenarios/{id}/orchestrate', [\App\Http\Controllers\Api\ScenarioController::class , 'orchestrate'])
    ->middleware('auth:sanctum');

// PASO 2: Roles ↔ Competencies Mapping
Route::middleware('auth:sanctum')->prefix('scenarios/{scenarioId}/step2')->group(function () {
    Route::get('data', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class , 'getMatrixData']);
    Route::post('mappings', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class , 'saveMapping']);
    Route::delete('mappings/{mappingId}', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class , 'deleteMapping']);
    Route::post('roles', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class , 'addRole']);
    Route::get('role-forecasts', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class , 'getRoleForecasts']);
    Route::get('skill-gaps-matrix', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class , 'getSkillGapsMatrix']);
    Route::get('matching-results', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class , 'getMatchingResults']);
    Route::get('succession-plans', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class , 'getSuccessionPlans']);
});

// Catálogos dinámicos para selectores
Route::get('catalogs', [CatalogsController::class , 'getCatalogs'])->name('catalogs.index');
require __DIR__ . '/form-schema-complete.php';

// TODO: recordar que estas rutas están protegidas por el middleware 'auth' en RouteServiceProvider.php y son Multinenant deben filtrar el organization_id del usuario autenticado
