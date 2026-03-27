<?php

use App\Http\Controllers\Api\ScenarioController;
use App\Http\Controllers\Api\ScenarioSimulationController;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Landings de agrupamientos principales
Route::get('/core', function () {
    return Inertia::render('Core/Landing');
})->middleware(['auth', 'verified'])->name('core.landing');

Route::get('/radar', function () {
    return Inertia::render('Radar/Landing');
})->middleware(['auth', 'verified'])->name('radar.landing');

Route::get('/px', function () {
    return Inertia::render('PX/Landing');
})->middleware(['auth', 'verified'])->name('px.landing');

Route::get('/growth', function () {
    return Inertia::render('Growth/Landing');
})->middleware(['auth', 'verified'])->name('growth.landing');

Route::get('/magnet', function () {
    return Inertia::render('Magnet/Landing');
})->middleware(['auth', 'verified'])->name('magnet.landing');

Route::get('/controlcenter', function () {
    return Inertia::render('ControlCenter/Landing');
})->middleware(['auth', 'verified', 'role:admin'])->name('controlcenter.landing');

Route::get('/controlcenter/culture', function () {
    return Inertia::render('ControlCenter/CulturalBlueprint');
})->middleware(['auth', 'verified', 'role:admin'])->name('controlcenter.culture');

Route::get('/controlcenter/culture-analytics', function () {
    return Inertia::render('ControlCenter/CultureDashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('controlcenter.culture-analytics');

// Admin Operations Routes
Route::get('/admin/operations', function () {
    return Inertia::render('Admin/Operations');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.operations');

// Dev-only endpoint for E2E: logs in as admin (id from env or 2)
Route::get('/__e2e_login', function () {
    if (! (AppFacade::environment('local') || config('stratos.qa.e2e_bypass', false))) {
        return response()->json(['error' => 'not allowed'], 403);
    }
    $userId = config('stratos.qa.e2e_admin_id', 2);
    Auth::loginUsingId($userId);

    return response()->json(['success' => true, 'user_id' => $userId]);
});

// Magic Links Authentication
Route::post('/magic-link', [\App\Http\Controllers\Auth\MagicLinkController::class, 'requestLink'])->name('magic.request');
Route::get('/magic-login/{user}', [\App\Http\Controllers\Auth\MagicLinkController::class, 'authenticate'])->name('magic.login')->middleware('signed');

// SSO Authentication
Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\Auth\SsoController::class, 'redirect'])->name('sso.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\SsoController::class, 'callback'])->name('sso.callback');

Route::get('/scenario-demo', function () {
    return Inertia::render('ScenarioDemo');
});

// External Assessment Feedback Form (Public)
Route::get('/assessments/feedback/{token}', [\App\Http\Controllers\Api\AssessmentController::class, 'showExternalForm']);

// Approval Magic Links (Public)
Route::get('/approve/role/{token}', [\App\Http\Controllers\Api\RoleDesignerController::class, 'showApprovalRequest'])->name('role.approval');
Route::get('/approve/competency/{token}', [\App\Http\Controllers\Api\RoleDesignerController::class, 'showApprovalRequest'])->name('competency.approval');
// API backends for the views above
Route::get('/api/approvals/{token}', [\App\Http\Controllers\Api\RoleDesignerController::class, 'getApprovalDetails']);
Route::post('/api/approvals/{token}/approve', [\App\Http\Controllers\Api\RoleDesignerController::class, 'approve']);

// Public DID document for did:web issuer discovery
Route::get('/.well-known/did.json', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'didDocument'])
    ->name('compliance.did-document');

// Stratos Magnet - Public Career Portal
Route::get('/career/{tenant}', function ($tenant) {
    return Inertia::render('Careers/PublicPortal', [
        'tenant' => $tenant,
    ]);
})->name('public.careers');

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

Route::get('/departments/org-chart', function () {
    return Inertia::render('Departments/OrganizationChart');
})->middleware(['auth', 'verified', 'module:core'])->name('departments.org-chart');

Route::get('/roles', function () {
    return Inertia::render('Roles/Index');
})->middleware(['auth', 'verified'])->name('roles.index');

Route::get('/roles/{id}', function ($id) {
    return Inertia::render('Roles/Show', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('roles.show');

Route::get('/skills', function () {
    return Inertia::render('Skills/Index');
})->middleware(['auth', 'verified'])->name('skills.index');

Route::get('/competencies', function () {
    return Inertia::render('Competencies/Index');
})->middleware(['auth', 'verified'])->name('competencies.index');

Route::get('/gap-analysis', function () {
    return Inertia::render('GapAnalysis/Index');
})->middleware(['auth', 'verified', 'module:core'])->name('gap-analysis.index');

Route::get('/succession', function () {
    return Inertia::render('Succession/Index');
})->middleware(['auth', 'verified'])->name('succession.index');

Route::get('/learning-paths', function () {
    return Inertia::render('LearningPaths/StratosNavigator');
})->middleware(['auth', 'verified', 'module:st-grow'])->name('learning-paths.index');

Route::get('/mentoring', function () {
    return Inertia::render('Growth/Mentorship');
})->middleware(['auth', 'verified'])->name('mentoring.index');

Route::get('/marketplace', function () {
    return Inertia::render('Marketplace/Index');
})->middleware(['auth', 'verified', 'module:st-match'])->name('marketplace.index');

// Alias routes for new frontend naming: /scenario-planning
Route::get('/scenario-planning', function () {
    return Inertia::render('ScenarioPlanning/ScenarioList');
})->middleware(['auth', 'verified', 'module:st-radar'])->name('scenario-planning.index');

Route::get('/scenario-planning/{id}', function ($id) {
    return Inertia::render('ScenarioPlanning/ScenarioDetail', ['scenarioId' => $id]);
})->middleware(['auth', 'verified', 'module:st-radar'])->name('scenario-planning.show');

// Backwards-compatibility aliases: /strategic-planning -> /scenario-planning
Route::get('/strategic-planning', function () {
    return Inertia::render('ScenarioPlanning/ScenarioList');
})->middleware(['auth', 'verified'])->name('strategic-planning.index');

Route::get('/strategic-planning/{id}', function ($id) {
    return Inertia::render('ScenarioPlanning/ScenarioDetail', ['scenarioId' => $id]);
})->middleware(['auth', 'verified'])->name('strategic-planning.show');

Route::get('/talento360', function () {
    return Inertia::render('Talento360/Dashboard');
})->middleware(['auth', 'verified', 'module:st-360'])->name('talento360.index');

Route::get('/talento360/results/{id}', function ($id) {
    return Inertia::render('Talento360/AssessmentResults', ['sessionId' => $id]);
})->middleware(['auth', 'verified'])->name('talento360.results');

Route::get('/talento360/map', function () {
    return Inertia::render('Talento360/StratosMap');
})->middleware(['auth', 'verified', 'module:core'])->name('talento360.map');

Route::get('/talento360/triangulation/{id}', function ($id) {
    return Inertia::render('Talento360/TriangulationDashboard', ['peopleId' => $id]);
})->middleware(['auth', 'verified'])->name('talento360.triangulation');

Route::get('/talento360/relationships', function () {
    return Inertia::render('Talento360/RelationshipMap');
})->middleware(['auth', 'verified', 'module:st-map'])->name('talento360.relationships');

Route::get('/talento360/bars', function () {
    return Inertia::render('Talento360/BARS/Index');
})->middleware(['auth', 'verified'])->name('talento360.bars.index');

Route::get('/talento360/question-bank', function () {
    return Inertia::render('Talento360/QuestionBank/Index');
})->middleware(['auth', 'verified'])->name('talento360.qb.index');

Route::get('/talento360/comando', function () {
    return Inertia::render('Talento360/Comando');
})->middleware(['auth', 'verified', 'role:admin,hr_leader'])->name('talento360.comando');

Route::get('/talento360/war-room', function () {
    return Inertia::render('Talento360/MobilityWarRoom');
})->middleware(['auth', 'verified', 'role:admin,hr_leader'])->name('talento360.war-room');

Route::get('/people-experience', function () {
    return Inertia::render('PeopleExperience/Index');
})->name('people-experience.index');

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

Route::get('/workforce-planning', function () {
    return Inertia::render('WorkforcePlanning/Index');
})->middleware(['auth', 'verified'])->name('workforce-planning.index');

Route::get('/quality-hub', function () {
    return Inertia::render('Quality/QualityHub');
})->middleware(['auth', 'verified'])->name('quality.hub');

Route::get('/quality/ragas-metrics', function () {
    return Inertia::render('Quality/RAGASDashboard');
})->middleware(['auth', 'verified'])->name('quality.ragas-metrics');

Route::get('/quality/compliance-audit', function () {
    return Inertia::render('Quality/ComplianceAuditDashboard');
})->middleware(['auth', 'verified'])->name('quality.compliance-audit');

// ── Intelligence Module Routes ──
Route::prefix('intelligence')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/monitoring-hub', function () {
        return Inertia::render('Intelligence/MonitoringHub');
    })->name('intelligence.monitoring-hub');

    Route::get('/quality-dashboard', function () {
        return Inertia::render('Intelligence/QualityDashboard');
    })->name('intelligence.quality-dashboard');

    Route::get('/agent-metrics', function () {
        return Inertia::render('Intelligence/AgentMetricsDashboard');
    })->name('intelligence.agent-metrics');

    Route::get('/aggregates', function () {
        return Inertia::render('Intelligence/IntelligenceMetricsDashboard');
    })->name('intelligence.metrics-dashboard');
});

Route::prefix('scenarios')->group(function () {
    Route::get('{id}/iq', [ScenarioController::class, 'getIQ']);
    Route::get('{id}/roles/{roleId}/competency-gaps', [ScenarioController::class, 'getCompetencyGaps']);
    Route::post('{id}/roles/{roleId}/derive-skills', [ScenarioController::class, 'deriveSkills']);
    Route::post('{id}/derive-all-skills', [ScenarioController::class, 'deriveAllSkills']);
    Route::post('{id}/simulate-growth', [ScenarioSimulationController::class, 'simulateGrowth']);
    Route::post('{id}/mitigate', [ScenarioSimulationController::class, 'getMitigationPlan']);
});

// Verification Deployment Configuration Routes (Admin Only)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/deployment/verification-config', [\App\Http\Controllers\Deployment\VerificationConfigurationController::class, 'show'])
        ->name('deployment.verification-config');
    Route::post('/deployment/verification-config', [\App\Http\Controllers\Deployment\VerificationConfigurationController::class, 'store'])
        ->name('deployment.verification-config.store');
    Route::get('/api/deployment/verification-status', [\App\Http\Controllers\Deployment\VerificationConfigurationController::class, 'status'])
        ->name('deployment.verification-status');

    // Verification Metrics Dashboard (Admin Only)
    Route::inertia('/deployment/verification-metrics', 'Deployment/VerificationMetricsDashboard')
        ->name('deployment.verification-metrics');
    Route::get('/api/deployment/verification-metrics', [\App\Http\Controllers\Deployment\VerificationMetricsDashboardController::class, 'current'])
        ->name('deployment.verification-metrics.current');
    Route::get('/api/deployment/verification-metrics/by-type', [\App\Http\Controllers\Deployment\VerificationMetricsDashboardController::class, 'byType'])
        ->name('deployment.verification-metrics.byType');
    Route::get('/api/deployment/verification-metrics/export', [\App\Http\Controllers\Deployment\VerificationMetricsDashboardController::class, 'export'])
        ->name('deployment.verification-metrics.export');

    // Verification Hub (Phase 1 MVP) - Admin Only
    Route::inertia('/deployment/verification-hub', 'Deployment/VerificationHub')
        ->name('deployment.verification-hub');

    // Hub API Endpoints
    Route::get('/api/deployment/verification/scheduler-status', [\App\Http\Controllers\Deployment\VerificationHubController::class, 'schedulerStatus'])
        ->name('deployment.verification.scheduler-status');
    Route::get('/api/deployment/verification/transitions', [\App\Http\Controllers\Deployment\VerificationHubController::class, 'recentTransitions'])
        ->name('deployment.verification.transitions');
    Route::get('/api/deployment/verification/notifications', [\App\Http\Controllers\Deployment\VerificationHubController::class, 'notifications'])
        ->name('deployment.verification.notifications');
    Route::post('/api/deployment/verification/test-notification', [\App\Http\Controllers\Deployment\VerificationHubController::class, 'testNotification'])
        ->name('deployment.verification.test-notification');
    Route::get('/api/deployment/verification/configuration', [\App\Http\Controllers\Deployment\VerificationHubController::class, 'configuration'])
        ->name('deployment.verification.configuration');

    // Verification Hub Phase 2 (Advanced Features) - Admin Only
    Route::get('/api/deployment/verification/audit-logs', [\App\Http\Controllers\Deployment\VerificationHubController::class, 'auditLogs'])
        ->name('deployment.verification.audit-logs');
    Route::post('/api/deployment/verification/dry-run', [\App\Http\Controllers\Deployment\VerificationHubController::class, 'dryRunSimulation'])
        ->name('deployment.verification.dry-run');
    Route::get('/api/deployment/verification/compliance-report', [\App\Http\Controllers\Deployment\VerificationHubController::class, 'complianceReport'])
        ->name('deployment.verification.compliance-report');

    // Verification Dashboards (Phase 7) - Admin Only
    Route::inertia('/deployment/verification/dashboard/executive', 'Verification/ExecutiveDashboard')
        ->name('deployment.verification.dashboard.executive');
    Route::inertia('/deployment/verification/dashboard/operational', 'Verification/OperationalDashboard')
        ->name('deployment.verification.dashboard.operational');
    Route::inertia('/deployment/verification/dashboard/compliance', 'Verification/ComplianceDashboard')
        ->name('deployment.verification.dashboard.compliance');
    Route::inertia('/deployment/verification/dashboard/performance', 'Verification/PerformanceDashboard')
        ->name('deployment.verification.dashboard.performance');
    Route::inertia('/deployment/verification/dashboard/insights', 'Verification/InsightsDashboard')
        ->name('deployment.verification.dashboard.insights');
    Route::inertia('/deployment/verification/dashboard/realtime', 'Verification/RealtimeMonitor')
        ->name('deployment.verification.dashboard.realtime');

    // Talent Pass (CV 2.0) - Authenticated Routes
    Route::inertia('/talent-pass', 'TalentPass/Index')
        ->name('talent-pass.index');
    Route::inertia('/talent-pass/create', 'TalentPass/Create')
        ->name('talent-pass.create');
    Route::inertia('/talent-pass/{id}', 'TalentPass/Show')
        ->name('talent-pass.show');
    Route::inertia('/talent-pass/{id}/edit', 'TalentPass/Edit')
        ->name('talent-pass.edit');
});

// Talent Pass Public Route (No Auth Required)
Route::inertia('/public/talent-pass/{ulid}', 'Public/TalentPass')
    ->name('talent-pass.public-view');

require __DIR__.'/settings.php';
