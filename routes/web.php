<?php

use App\Http\Controllers\Api\ScenarioController;
use App\Http\Controllers\Api\ScenarioSimulationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App as AppFacade;

// Dev-only endpoint for E2E: logs in as admin (id from env or 2)
Route::get('/__e2e_login', function () {
    if (! (AppFacade::environment('local') || env('E2E_BYPASS', false))) {
        return response()->json(['error' => 'not allowed'], 403);
    }
    $userId = env('E2E_ADMIN_ID', 2);
    Auth::loginUsingId($userId);
    return response()->json(['success' => true, 'user_id' => $userId]);
});

Route::get('/scenario-demo', function () {
    return Inertia::render('ScenarioDemo');
});

// External Assessment Feedback Form (Public)
Route::get('/assessments/feedback/{token}', [\App\Http\Controllers\Api\AssessmentController::class, 'showExternalForm']);

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard/analytics', function () {
    return Inertia::render('Dashboard/Analytics');
})->middleware(['auth', 'verified'])->name('dashboard.analytics');

Route::get('/dashboard/investor', function () {
    return Inertia::render('Dashboard/Investor');
})->middleware(['auth', 'verified', 'role:admin,hr_leader,observer'])->name('dashboard.investor');

Route::get('/people', function () {
    return Inertia::render('People/Index');
})->middleware(['auth', 'verified'])->name('people.index');

Route::get('/people/{id}', function ($id) {
    return Inertia::render('People/Show', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('people.show');

Route::get('/roles', function () {
    return Inertia::render('Roles/Index');
})->middleware(['auth', 'verified'])->name('roles.index');

Route::get('/roles/{id}', function ($id) {
    return Inertia::render('Roles/Show', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('roles.show');

Route::get('/skills', function () {
    return Inertia::render('Skills/Index');
})->middleware(['auth', 'verified'])->name('skills.index');

Route::get('/gap-analysis', function () {
    return Inertia::render('GapAnalysis/Index');
})->middleware(['auth', 'verified'])->name('gap-analysis.index');

Route::get('/learning-paths', function () {
    return Inertia::render('LearningPaths/Index');
})->middleware(['auth', 'verified'])->name('learning-paths.index');

Route::get('/marketplace', function () {
    return Inertia::render('Marketplace/Index');
})->middleware(['auth', 'verified'])->name('marketplace.index');

// Alias routes for new frontend naming: /scenario-planning
Route::get('/scenario-planning', function () {
    return Inertia::render('ScenarioPlanning/ScenarioList');
})->middleware(['auth', 'verified'])->name('scenario-planning.index');

Route::get('/scenario-planning/{id}', function ($id) {
    return Inertia::render('ScenarioPlanning/ScenarioDetail', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('scenario-planning.show');

// Backwards-compatibility aliases: /strategic-planning -> /scenario-planning
Route::get('/strategic-planning', function () {
    return Inertia::render('ScenarioPlanning/ScenarioList');
})->middleware(['auth', 'verified'])->name('strategic-planning.index');

Route::get('/strategic-planning/{id}', function ($id) {
    return Inertia::render('ScenarioPlanning/ScenarioDetail', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('strategic-planning.show');

Route::get('/talento360', function () {
    return Inertia::render('Talento360/Dashboard');
})->middleware(['auth', 'verified'])->name('talento360.index');

Route::get('/talento360/results/{id}', function ($id) {
    return Inertia::render('Talento360/AssessmentResults', ['sessionId' => $id]);
})->middleware(['auth', 'verified'])->name('talento360.results');

Route::get('/talento360/relationships', function () {
    return Inertia::render('Talento360/RelationshipMap');
})->middleware(['auth', 'verified'])->name('talento360.relationships');

Route::get('/talento360/bars', function () {
    return Inertia::render('Talento360/BARS/Index');
})->middleware(['auth', 'verified'])->name('talento360.bars.index');

Route::get('/talento360/question-bank', function () {
    return Inertia::render('Talento360/QuestionBank/Index');
})->middleware(['auth', 'verified'])->name('talento360.qb.index');

Route::get('/talento360/comando', function () {
    return Inertia::render('Talento360/Comando');
})->middleware(['auth', 'verified', 'role:admin,hr_leader'])->name('talento360.comando');

Route::get('/people-experience', function () {
    return Inertia::render('PeopleExperience/Index');
})->middleware(['auth', 'verified'])->name('people-experience.index');

Route::get('/people-experience/comando', function () {
    return Inertia::render('PeopleExperience/ComandoPx');
})->middleware(['auth', 'verified', 'role:admin,hr_leader'])->name('people-experience.comando');

Route::get('/talent-agents', function () {
    return Inertia::render('TalentAgents/Index');
})->middleware(['auth', 'verified', 'role:admin,hr_leader'])->name('talent-agents.index');

Route::get('/mi-stratos', function () {
    return Inertia::render('MiStratos/Index');
})->middleware(['auth', 'verified'])->name('mi-stratos.index');

Route::get('/settings/rbac', function () {
    return Inertia::render('settings/RBAC');
})->middleware(['auth', 'verified', 'role:admin'])->name('settings.rbac');

Route::get('/candidate-portal/{id}', function ($id) {
    return Inertia::render('Selection/CandidatePortal', ['applicationId' => $id]);
})->name('candidate-portal');

Route::prefix('scenarios')->group(function () {
    Route::get('{id}/iq', [ScenarioController::class, 'getIQ']);
    Route::get('{id}/roles/{roleId}/competency-gaps', [ScenarioController::class, 'getCompetencyGaps']);
    Route::post('{id}/roles/{roleId}/derive-skills', [ScenarioController::class, 'deriveSkills']);
    Route::post('{id}/derive-all-skills', [ScenarioController::class, 'deriveAllSkills']);
    Route::post('{id}/simulate-growth', [ScenarioSimulationController::class, 'simulateGrowth']);
    Route::post('{id}/mitigate', [ScenarioSimulationController::class, 'getMitigationPlan']);
});

require __DIR__.'/settings.php';
