<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CatalogsController;

/* // Catálogos dinámicos para selectores
Route::get('/catalogs', function (Illuminate\Http\Request $request) {
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

    return response()->json($result);
}); */

// Core services
Route::post('/gap-analysis', [\App\Http\Controllers\Api\GapAnalysisController::class, 'analyze']);
Route::get('/development-paths', [\App\Http\Controllers\Api\DevelopmentPathController::class, 'index']);
Route::post('/development-paths/generate', [\App\Http\Controllers\Api\DevelopmentPathController::class, 'generate']);
Route::delete('/development-paths/{id}', [\App\Http\Controllers\Api\DevelopmentPathController::class, 'destroy']);

// Job Openings (Día 4-5)
Route::get('/job-openings', [\App\Http\Controllers\Api\JobOpeningController::class, 'index']);
Route::get('/job-openings/{id}', [\App\Http\Controllers\Api\JobOpeningController::class, 'show']);
Route::get('/job-openings/{id}/candidates', [\App\Http\Controllers\Api\JobOpeningController::class, 'candidates']);

// Applications (Día 5)
Route::get('/applications', [\App\Http\Controllers\Api\ApplicationController::class, 'index']);
Route::get('/applications/{id}', [\App\Http\Controllers\Api\ApplicationController::class, 'show']);
Route::post('/applications', [\App\Http\Controllers\Api\ApplicationController::class, 'store']);
Route::patch('/applications/{id}', [\App\Http\Controllers\Api\ApplicationController::class, 'update']);

// CRUD genérico: People, Skills, Roles, Departments
// Gestionado por form-schema-complete.php con FormSchemaController

// Dashboard
Route::get('/dashboard/metrics', [\App\Http\Controllers\Api\DashboardController::class, 'metrics']);

// Marketplace (Día 5 - Internal opportunities)
Route::get('/people/{people_id}/marketplace', [\App\Http\Controllers\Api\MarketplaceController::class, 'opportunities']); // Vista candidato
Route::get('/marketplace/recruiter', [\App\Http\Controllers\Api\MarketplaceController::class, 'recruiterView']); // Vista reclutador

// Workforce Planning (Phase 2)
Route::prefix('v1/workforce-planning')->middleware(['auth:sanctum'])->group(function () {
    // Scenario Templates
    Route::get('/scenario-templates', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'index']);
    Route::get('/scenario-templates/{template}', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'show']);

    // Workforce Scenarios
    Route::get('/workforce-scenarios', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'index']);
    Route::post('/workforce-scenarios', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'store']);
    Route::post('/workforce-scenarios/{template}/instantiate-from-template', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'instantiateFromTemplate']);
    Route::get('/workforce-scenarios/{scenario}', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'show']);
    Route::put('/workforce-scenarios/{scenario}', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'update']);
    Route::patch('/workforce-scenarios/{scenario}', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'update']);
    Route::delete('/workforce-scenarios/{scenario}', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'destroy']);
    Route::post('/workforce-scenarios/{scenario}/calculate-gaps', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'calculateGaps']);
    Route::post('/workforce-scenarios/{scenario}/refresh-suggested-strategies', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'refreshSuggestedStrategies']);

    // Scenario Comparisons
    Route::post('/scenario-comparisons', [\App\Http\Controllers\Api\ScenarioComparisonController::class, 'store']);
    Route::get('/scenario-comparisons', [\App\Http\Controllers\Api\ScenarioComparisonController::class, 'index']);
    Route::get('/scenario-comparisons/{comparison}', [\App\Http\Controllers\Api\ScenarioComparisonController::class, 'show']);

    // Use Case activation per organization
    Route::get('/use-cases', [\App\Http\Controllers\Api\OrganizationUseCaseController::class, 'index']);
    Route::post('/use-cases/{template}/activate', [\App\Http\Controllers\Api\OrganizationUseCaseController::class, 'activate']);
    Route::post('/use-cases/{template}/deactivate', [\App\Http\Controllers\Api\OrganizationUseCaseController::class, 'deactivate']);

    // Legacy compatibility routes (temporary) to avoid 404s while migrating frontend
    Route::get('/scenarios/{scenario}', [\App\Http\Controllers\Api\WorkforceScenarioController::class, 'show']);
    Route::get('/scenarios/{id}/role-forecasts', function ($id) {
        return response()->json(['success' => true, 'data' => []]);
    });
    Route::get('/scenarios/{id}/matches', function ($id) {
        return response()->json(['success' => true, 'data' => []]);
    });
    Route::get('/scenarios/{id}/skill-gaps', function ($id) {
        return response()->json(['success' => true, 'data' => []]);
    });
    Route::get('/scenarios/{id}/succession-plans', function ($id) {
        return response()->json(['success' => true, 'data' => []]);
    });
});

// Catálogos dinámicos para selectores
Route::get('catalogs', [CatalogsController::class, 'getCatalogs'])->name('catalogs.index');
require __DIR__ . '/form-schema-complete.php';
