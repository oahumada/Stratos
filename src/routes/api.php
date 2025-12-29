<?php

use Illuminate\Support\Facades\Route;

// Catálogos dinámicos para selectores
Route::get('/catalogs', function (Illuminate\Http\Request $request) {
    $endpoints = $request->query('endpoints', []);
    $result = [];

    foreach ((array) $endpoints as $endpoint) {
        try {
            $result[$endpoint] = match ($endpoint) {
                'role' => \App\Models\Role::select('id', 'name')->get(),
                'skill' => \App\Models\Skill::select('id', 'name', 'category')->get(),
                'department' => \App\Models\Department::select('id', 'name')->get(),
                'departments' => \App\Models\Department::select('id', 'name')->get(),
                default => [],
            };
        } catch (\Exception $e) {
            \Log::error("Catalog error for {$endpoint}: " . $e->getMessage());
            $result[$endpoint] = [];
        }
    }

    return response()->json($result);
});

// Core services
Route::post('/gap-analysis', [\App\Http\Controllers\Api\GapAnalysisController::class, 'analyze']);
Route::post('/development-paths/generate', [\App\Http\Controllers\Api\DevelopmentPathController::class, 'generate']);

// Job Openings (Día 4-5)
Route::get('/job-openings', [\App\Http\Controllers\Api\JobOpeningController::class, 'index']);
Route::get('/job-openings/{id}', [\App\Http\Controllers\Api\JobOpeningController::class, 'show']);
Route::get('/job-openings/{id}/candidates', [\App\Http\Controllers\Api\JobOpeningController::class, 'candidates']);

// Applications (Día 5)
Route::get('/applications', [\App\Http\Controllers\Api\ApplicationController::class, 'index']);
Route::get('/applications/{id}', [\App\Http\Controllers\Api\ApplicationController::class, 'show']);
Route::post('/applications', [\App\Http\Controllers\Api\ApplicationController::class, 'store']);
Route::patch('/applications/{id}', [\App\Http\Controllers\Api\ApplicationController::class, 'update']);

// Lectura: Person
Route::get('/person', [\App\Http\Controllers\Api\PersonController::class, 'index']);
Route::get('/person/{id}', [\App\Http\Controllers\Api\PersonController::class, 'show']);

// Lectura: Roles
Route::get('/roles', [\App\Http\Controllers\Api\RolesController::class, 'index']);
Route::get('/roles/{id}', [\App\Http\Controllers\Api\RolesController::class, 'show']);

// Lectura: Skills
Route::get('/skills', [\App\Http\Controllers\Api\SkillsController::class, 'index']);
Route::get('/skills/{id}', [\App\Http\Controllers\Api\SkillsController::class, 'show']);

// Dashboard
Route::get('/dashboard/metrics', [\App\Http\Controllers\Api\DashboardController::class, 'metrics']);

// Marketplace (Día 5 - Internal opportunities)
Route::get('/person/{person_id}/marketplace', [\App\Http\Controllers\Api\MarketplaceController::class, 'opportunities']);

require __DIR__ . '/form-schema-complete.php';
