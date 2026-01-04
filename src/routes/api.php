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
Route::prefix('v1/workforce-planning')->group(function () {
    // Scenarios CRUD
    Route::get('/scenarios', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'listScenarios']);
    Route::post('/scenarios', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'createScenario']);
    Route::get('/scenarios/{id}', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'showScenario']);
    Route::put('/scenarios/{id}', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'updateScenario']);
    Route::delete('/scenarios/{id}', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'deleteScenario']);
    
    // Scenario approval
    Route::post('/scenarios/{id}/approve', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'approveScenario']);
    
    // Analysis endpoints
    Route::get('/scenarios/{id}/role-forecasts', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'getScenarioForecasts']);
    Route::get('/scenarios/{id}/matches', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'getScenarioMatches']);
    Route::get('/scenarios/{id}/skill-gaps', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'getScenarioSkillGaps']);
    Route::get('/scenarios/{id}/succession-plans', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'getScenarioSuccessionPlans']);
    Route::get('/scenarios/{id}/analytics', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'getScenarioAnalytics']);
    
    // Run full analysis
    Route::post('/scenarios/{id}/analyze', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'analyzeScenario']);
    
    // Match recommendations
    Route::get('/matches/{id}/recommendations', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'getMatchRecommendations']);
});

// Catálogos dinámicos para selectores
Route::get('catalogs', [CatalogsController::class, 'getCatalogs'])->name('catalogs.index');
require __DIR__ . '/form-schema-complete.php';
