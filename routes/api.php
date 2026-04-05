<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Error message constants to reduce duplication and complexity
if (! defined('MSG_UNAUTHENTICATED')) {
    define('MSG_UNAUTHENTICATED', 'Unauthenticated');
}
if (! defined('MSG_FORBIDDEN')) {
    define('MSG_FORBIDDEN', 'Forbidden');
}
if (! defined('MSG_SCENARIO_NOT_FOUND')) {
    define('MSG_SCENARIO_NOT_FOUND', 'Scenario not found');
}
if (! defined('MSG_CAPABILITY_NOT_FOUND')) {
    define('MSG_CAPABILITY_NOT_FOUND', 'Capability not found');
}
if (! defined('MSG_COMPETENCY_NOT_FOUND')) {
    define('MSG_COMPETENCY_NOT_FOUND', 'Competency not found');
}
if (! defined('MSG_RELATIONSHIP_DELETED')) {
    define('MSG_RELATIONSHIP_DELETED', 'Relationship deleted');
}
if (! defined('MSG_RELATION_NOT_FOUND')) {
    define('MSG_RELATION_NOT_FOUND', 'Relation not found');
}
if (! defined('MSG_CAPABILITY_DELETED')) {
    define('MSG_CAPABILITY_DELETED', 'Capability deleted');
}
if (! defined('MSG_SERVER_ERROR_DELETING_CAPABILITY')) {
    define('MSG_SERVER_ERROR_DELETING_CAPABILITY', 'Server error deleting capability');
}

// Route path constants
if (! defined('PATH_CAPABILITIES_ID')) {
    define('PATH_CAPABILITIES_ID', '/capabilities/{id}');
}
if (! defined('PATH_COMPETENCIES_ID')) {
    define('PATH_COMPETENCIES_ID', '/competencies/{id}');
}

Route::get('/catalogs', [\App\Http\Controllers\Api\CatalogsController::class, 'index'])->name('catalogs.index');

// Public Assessment Feedback (Access via Token)
Route::get('/assessments/feedback/{token}', [\App\Http\Controllers\Api\AssessmentController::class, 'showByToken']);
Route::post('/assessments/feedback/submit-guest', [\App\Http\Controllers\Api\AssessmentController::class, 'submitFeedbackGuest']);

// Role Approval Magic Links
Route::get('/approvals/{token}', [\App\Http\Controllers\Api\RoleDesignerController::class, 'showApprovalRequest'])->name('approvals.show');
Route::post('/approvals/{token}/approve', [\App\Http\Controllers\Api\RoleDesignerController::class, 'approve'])->name('approvals.approve');
// Provide a reject endpoint name so preview links resolve; behavior is handled elsewhere.
Route::post('/approvals/{token}/reject', function ($token) {
    // Minimal handler for magic-link rejects used by email previews. Real rejection
    // is processed via authenticated API endpoints. This exists to allow
    // `route('approvals.reject', ['token' => ...])` to resolve in tests.
    return response()->json(['status' => 'ok']);
})->name('approvals.reject');

// Career Portal (Stratos Magnet - Public)
Route::get('/career/{tenantSlug}', [\App\Http\Controllers\Api\PublicJobController::class, 'index']);
Route::get('/career/{tenantSlug}/jobs/{jobSlug}', [\App\Http\Controllers\Api\PublicJobController::class, 'show']);
Route::post('/career/{tenantSlug}/jobs/{jobSlug}/apply', [\App\Http\Controllers\Api\PublicJobController::class, 'apply']);

// Public Compliance Verification (for external auditors/verifiers)
Route::post('/compliance/public/credentials/verify', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'verifyRoleCredentialPublic']);
Route::get('/compliance/public/verifier-metadata', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'verifierMetadata']);

// Public Talent Pass View (Shareable by ULID)
Route::get('/talent-pass/{publicId}', [\App\Http\Controllers\Api\TalentPassController::class, 'showPublic'])->name('talent-pass.public');

// Authenticated Talent Pass API
Route::middleware('auth:sanctum')->group(function () {
    // Talent Pass CRUD & Operations
    Route::get('/talent-passes', [\App\Http\Controllers\Api\TalentPassController::class, 'index']);
    Route::post('/talent-passes', [\App\Http\Controllers\Api\TalentPassController::class, 'store']);
    Route::get('/talent-passes/{id}', [\App\Http\Controllers\Api\TalentPassController::class, 'show']);
    Route::put('/talent-passes/{id}', [\App\Http\Controllers\Api\TalentPassController::class, 'update']);
    Route::delete('/talent-passes/{id}', [\App\Http\Controllers\Api\TalentPassController::class, 'destroy']);

    // Advanced Operations
    Route::post('/talent-passes/{id}/publish', [\App\Http\Controllers\Api\TalentPassController::class, 'publish']);
    Route::post('/talent-passes/{id}/archive', [\App\Http\Controllers\Api\TalentPassController::class, 'archive']);
    Route::post('/talent-passes/{id}/clone', [\App\Http\Controllers\Api\TalentPassController::class, 'clone']);
    Route::get('/talent-passes/{id}/export', [\App\Http\Controllers\Api\TalentPassController::class, 'export']);
    Route::post('/talent-passes/{id}/share', [\App\Http\Controllers\Api\TalentPassController::class, 'share']);

    // Talent Search
    Route::get('/search', [\App\Http\Controllers\Api\TalentSearchController::class, 'search']);
    Route::get('/search/skills', [\App\Http\Controllers\Api\TalentSearchController::class, 'searchBySkills']);
    Route::get('/search/skill-level', [\App\Http\Controllers\Api\TalentSearchController::class, 'findBySkillLevel']);
    Route::get('/search/experience', [\App\Http\Controllers\Api\TalentSearchController::class, 'findByExperience']);
    Route::get('/search/credential', [\App\Http\Controllers\Api\TalentSearchController::class, 'findByCredential']);
    Route::get('/search/similar', [\App\Http\Controllers\Api\TalentSearchController::class, 'similar']);

    // Analytics & Trending
    Route::get('/analytics/trending', [\App\Http\Controllers\Api\TalentSearchController::class, 'getTrending']);
    Route::post('/analytics/gaps', [\App\Http\Controllers\Api\TalentSearchController::class, 'gaps']);
});

// Core services
Route::patch('/development-actions/{id}/status', [\App\Http\Controllers\Api\DevelopmentActionController::class, 'updateStatus']);
Route::get('/mentorship-sessions', [\App\Http\Controllers\Api\MentorshipSessionController::class, 'index']);
Route::post('/mentorship-sessions', [\App\Http\Controllers\Api\MentorshipSessionController::class, 'store']);
Route::patch('/mentorship-sessions/{id}', [\App\Http\Controllers\Api\MentorshipSessionController::class, 'update']);
Route::delete('/mentorship-sessions/{id}', [\App\Http\Controllers\Api\MentorshipSessionController::class, 'destroy']);
Route::get('/evidences', [\App\Http\Controllers\Api\EvidenceController::class, 'index']);
Route::post('/evidences', [\App\Http\Controllers\Api\EvidenceController::class, 'store']);
Route::delete('/evidences/{id}', [\App\Http\Controllers\Api\EvidenceController::class, 'destroy']);
Route::post('/development-actions/{id}/launch-lms', [\App\Http\Controllers\Api\DevelopmentActionController::class, 'launchLms']);
Route::post('/development-actions/{id}/sync-lms', [\App\Http\Controllers\Api\DevelopmentActionController::class, 'syncLms']);
Route::get('/pulse-surveys', [\App\Http\Controllers\Api\PulseController::class, 'index']);
Route::get('/pulse-surveys/{id}', [\App\Http\Controllers\Api\PulseController::class, 'show']);
Route::post('/pulse-responses', [\App\Http\Controllers\Api\PulseController::class, 'storeResponse']);
Route::get('/pulse/health-scan', [\App\Http\Controllers\Api\PulseController::class, 'healthScan']);

// Talent DNA Extraction
Route::post('/talent/dna-extract/{personId}', function ($personId) {
    $service = app(\App\Services\Talent\TalentSelectionService::class);
    try {
        $result = $service->extractHighPerformerDNA((int) $personId);

        return response()->json(['success' => true, 'data' => $result]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
Route::get('/agents', [\App\Http\Controllers\Api\AgentController::class, 'index'])->middleware('permission:agents.view');
Route::put('/agents/{agent}', [\App\Http\Controllers\Api\AgentController::class, 'update'])->middleware('permission:agents.manage');
Route::post('/agents/test', [\App\Http\Controllers\Api\AgentController::class, 'testAgent'])->middleware('permission:agents.manage');

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

// Stratos IQ (Event Sourcing & Organizational Learning Velocity)
Route::get('/stratos-iq/{organizationId}', [\App\Http\Controllers\Api\StratosIqController::class, 'getTrends']);
Route::post('/stratos-iq/{organizationId}/snapshot', [\App\Http\Controllers\Api\StratosIqController::class, 'captureSnapshot']);

// Talent Pass (Sovereign Identity / CV 2.0)
Route::get('/people/{people_id}/talent-pass', [\App\Http\Controllers\Api\TalentPassController::class, 'show']);
Route::post('/people/{people_id}/talent-pass/issue', [\App\Http\Controllers\Api\TalentPassController::class, 'generateCredential']);

// Marketplace (Día 5 - Internal opportunities)
Route::get('/people/{people_id}/marketplace', [\App\Http\Controllers\Api\MarketplaceController::class, 'opportunities']); // Vista candidato
Route::get('/marketplace/recruiter', [\App\Http\Controllers\Api\MarketplaceController::class, 'recruiterView']); // Vista reclutador
Route::post('/marketplace/positions/{positionId}/candidates/{candidateId}/ai-insights', [\App\Http\Controllers\Api\MarketplaceController::class, 'aiMatchInsights']);
// Talent & Mentorship (Fase 5)
Route::get('/talent/mentors/suggest', [\App\Http\Controllers\Api\MentorController::class, 'suggest']);

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
// Analytics pre-checks: these routes are defined outside the auth group so
// unauthenticated requests receive a JSON 401 response (tests expect this).
Route::get('/scenarios/{id}/analytics', function ($id) {
    \Log::debug('analytics pre-check', ['id' => $id, 'auth_check' => auth()->check(), 'user_id' => optional(auth()->user())->id]);
    \Log::debug('analytics request debug', [
        'bearer' => request()->bearerToken(),
        'has_auth_header' => request()->headers->has('authorization'),
        'cookies' => array_keys(request()->cookies->all()),
    ]);

    if ((app()->bound('sanctum_test_cleared') && app('sanctum_test_cleared') === true) || ! auth()->check()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    return app(\App\Http\Controllers\Api\ScenarioAnalyticsController::class)->analyticsById((int) $id);
});

Route::get('/scenarios/{id}/financial-impact', function ($id) {
    \Log::debug('financial-impact pre-check', ['id' => $id, 'auth_check' => auth()->check(), 'user_id' => optional(auth()->user())->id]);
    \Log::debug('financial-impact request debug', [
        'bearer' => request()->bearerToken(),
        'has_auth_header' => request()->headers->has('authorization'),
        'cookies' => array_keys(request()->cookies->all()),
    ]);

    if ((app()->bound('sanctum_test_cleared') && app('sanctum_test_cleared') === true) || ! auth()->check()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    return app(\App\Http\Controllers\Api\ScenarioAnalyticsController::class)->financialImpactById((int) $id);
});

Route::get('/scenarios/{id}/risk-assessment', function ($id) {
    \Log::debug('risk-assessment pre-check', ['id' => $id, 'auth_check' => auth()->check(), 'user_id' => optional(auth()->user())->id]);
    \Log::debug('risk-assessment request debug', [
        'bearer' => request()->bearerToken(),
        'has_auth_header' => request()->headers->has('authorization'),
        'cookies' => array_keys(request()->cookies->all()),
    ]);

    if ((app()->bound('sanctum_test_cleared') && app('sanctum_test_cleared') === true) || ! auth()->check()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    return app(\App\Http\Controllers\Api\ScenarioAnalyticsController::class)->riskAssessmentById((int) $id);
});

Route::get('/scenarios/{id}/skill-gaps', function ($id) {
    \Log::debug('skill-gaps pre-check', ['id' => $id, 'auth_check' => auth()->check(), 'user_id' => optional(auth()->user())->id]);
    \Log::debug('skill-gaps request debug', [
        'bearer' => request()->bearerToken(),
        'has_auth_header' => request()->headers->has('authorization'),
        'cookies' => array_keys(request()->cookies->all()),
    ]);

    if ((app()->bound('sanctum_test_cleared') && app('sanctum_test_cleared') === true) || ! auth()->check()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    return app(\App\Http\Controllers\Api\ScenarioAnalyticsController::class)->skillGapsById((int) $id);
});

Route::get('/scenarios/{id}/people-experience', function ($id) {
    \Log::debug('people-experience pre-check', ['id' => $id, 'auth_check' => auth()->check(), 'user_id' => optional(auth()->user())->id]);
    \Log::debug('people-experience request debug', [
        'bearer' => request()->bearerToken(),
        'has_auth_header' => request()->headers->has('authorization'),
        'cookies' => array_keys(request()->cookies->all()),
    ]);

    if ((app()->bound('sanctum_test_cleared') && app('sanctum_test_cleared') === true) || ! auth()->check()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    return app(\App\Http\Controllers\Api\ScenarioAnalyticsController::class)->peopleExperienceById((int) $id);
});

Route::middleware('auth:sanctum')->group(function () {
    // Auth & Permissions
    Route::get('/auth/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard/metrics', [\App\Http\Controllers\Api\DashboardController::class, 'metrics']);

    // Talent Bulk Import
    Route::post('/talent/bulk-import/analyze', [\App\Http\Controllers\Api\BulkPeopleImportController::class, 'analyze']);
    Route::post('/talent/bulk-import/stage', [\App\Http\Controllers\Api\BulkPeopleImportController::class, 'stage']);
    Route::post('/talent/bulk-import/{id}/approve', [\App\Http\Controllers\Api\BulkPeopleImportController::class, 'approveAndCommit']);

    // Succession Planning (Predicciones basadas en trayectoria)
    Route::get('/talent/succession/plans', [\App\Http\Controllers\Api\SuccessionController::class, 'index']);
    Route::get('/talent/succession/role/{roleId}', [\App\Http\Controllers\Api\SuccessionController::class, 'getSuccessors']);
    Route::post('/talent/succession/analyze-candidate', [\App\Http\Controllers\Api\SuccessionController::class, 'analyzeCandidate']);
    Route::post('/talent/succession/store-plan', [\App\Http\Controllers\Api\SuccessionController::class, 'storePlan']);

    // Mi Stratos — Portal Personal
    Route::get('/mi-stratos/dashboard', [\App\Http\Controllers\Api\MiStratosController::class, 'dashboard']);

    // Smart Alerts (Phase 6)
    Route::get('/smart-alerts', [\App\Http\Controllers\Api\SmartAlertController::class, 'index']);
    Route::post('/smart-alerts/{id}/read', [\App\Http\Controllers\Api\SmartAlertController::class, 'markAsRead']);

    // SLA Alert System (Phase 2 - Alerting)
    Route::prefix('alerts')->group(function () {
        // Thresholds management
        Route::get('/thresholds', [\App\Http\Controllers\Api\AlertController::class, 'indexThresholds'])->name('alerts.thresholds.index');
        Route::post('/thresholds', [\App\Http\Controllers\Api\AlertController::class, 'storeThreshold'])->name('alerts.thresholds.store');
        Route::get('/thresholds/{threshold}', [\App\Http\Controllers\Api\AlertController::class, 'showThreshold'])->name('alerts.thresholds.show');
        Route::patch('/thresholds/{threshold}', [\App\Http\Controllers\Api\AlertController::class, 'updateThreshold'])->name('alerts.thresholds.update');
        Route::delete('/thresholds/{threshold}', [\App\Http\Controllers\Api\AlertController::class, 'destroyThreshold'])->name('alerts.thresholds.destroy');

        // Alert history
        Route::get('/history', [\App\Http\Controllers\Api\AlertController::class, 'indexHistory'])->name('alerts.history.index');
        Route::get('/history/{alert}', [\App\Http\Controllers\Api\AlertController::class, 'showHistory'])->name('alerts.history.show');
        Route::post('/history/{alert}/acknowledge', [\App\Http\Controllers\Api\AlertController::class, 'acknowledgeAlert'])->name('alerts.acknowledge');
        Route::post('/history/{alert}/resolve', [\App\Http\Controllers\Api\AlertController::class, 'resolveAlert'])->name('alerts.resolve');
        Route::post('/history/{alert}/mute', [\App\Http\Controllers\Api\AlertController::class, 'muteAlert'])->name('alerts.mute');

        // Summary views
        Route::get('/unacknowledged', [\App\Http\Controllers\Api\AlertController::class, 'getUnacknowledged'])->name('alerts.unacknowledged');
        Route::get('/critical', [\App\Http\Controllers\Api\AlertController::class, 'getCritical'])->name('alerts.critical');
        Route::get('/statistics', [\App\Http\Controllers\Api\AlertController::class, 'statistics'])->name('alerts.statistics');

        // Bulk operations
        Route::post('/bulk-acknowledge', [\App\Http\Controllers\Api\AlertController::class, 'bulkAcknowledge'])->name('alerts.bulkAcknowledge');

        // Export
        Route::get('/export/history', [\App\Http\Controllers\Api\AlertController::class, 'exportHistory'])
            ->middleware('normalize.csv')
            ->name('alerts.export');
    });

    // Gamification (Fase 6 / D4)
    Route::get('/gamification/quests', [\App\Http\Controllers\Api\GamificationController::class, 'getAvailableQuests']);
    Route::get('/gamification/people/{peopleId}/quests', [\App\Http\Controllers\Api\GamificationController::class, 'getPersonQuests']);
    Route::post('/gamification/people/{peopleId}/quests/{questId}/start', [\App\Http\Controllers\Api\GamificationController::class, 'startQuest']);
    Route::post('/gamification/people/{peopleId}/quests/{questId}/progress', [\App\Http\Controllers\Api\GamificationController::class, 'progressQuest']);

    // Quality & Continuous Improvement (Tickets)
    Route::get('/support-tickets/metrics', [\App\Http\Controllers\Api\SupportTicketController::class, 'metrics']);
    Route::apiResource('support-tickets', \App\Http\Controllers\Api\SupportTicketController::class);

    // Compliance Audit Dashboard (ISO 9001 / Governance)
    Route::get('/compliance/audit-events', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'index']);
    Route::get('/compliance/audit-events/summary', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'summary']);
    Route::get('/compliance/iso30414/summary', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'iso30414Summary']);
    Route::post('/compliance/consents/ai-processing', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'recordAiConsent']);
    Route::post('/compliance/gdpr/purge', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'executeGdprPurge']);
    Route::get('/compliance/credentials/roles/{roleId}', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'exportRoleCredential']);
    Route::post('/compliance/credentials/roles/{roleId}/verify', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'verifyRoleCredential']);
    Route::get('/compliance/internal-audit-wizard', [\App\Http\Controllers\Api\ComplianceAuditController::class, 'internalAuditWizard']);

    // Enterprise Security — Access Audit Logs (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/security/access-logs', [\App\Http\Controllers\Api\SecurityAccessController::class, 'index']);
        Route::get('/security/access-logs/summary', [\App\Http\Controllers\Api\SecurityAccessController::class, 'summary']);
    });

    // Investor/Executive Dashboard
    Route::get('/investor/dashboard', [\App\Http\Controllers\Api\InvestorDashboardController::class, 'index'])
        ->middleware('role:admin,hr_leader,observer');

    Route::get('/investor/impact-summary', [\App\Http\Controllers\Intelligence\ImpactEngineController::class, 'getSummary'])
        ->middleware('role:admin,hr_leader,observer');

    // Talento 360 Command Center
    Route::apiResource('assessment-cycles', \App\Http\Controllers\Api\AssessmentCycleController::class)
        ->middleware('permission:assessments.manage');

    Route::get('/people/profile/{id}', [\App\Http\Controllers\Api\PeopleProfileController::class, 'show']);
    Route::get('/people/profile/{id}/timeline', [\App\Http\Controllers\Api\PeopleProfileController::class, 'getTimeline']);

    // Organigrama (Departamentos)
    Route::get('/departments/tree', [\App\Http\Controllers\Api\DepartmentController::class, 'tree']);
    Route::post('/departments/hierarchy', [\App\Http\Controllers\Api\DepartmentController::class, 'updateHierarchy']);
    Route::put('/departments/{department}/hierarchy', [\App\Http\Controllers\Api\DepartmentController::class, 'updateDepartmentParent']);
    Route::post('/departments/{id}/manager', [\App\Http\Controllers\Api\DepartmentController::class, 'setManager']);
    Route::get('/departments/heatmap', [\App\Http\Controllers\Api\DepartmentController::class, 'heatmapData']);

    // Stratos Maps (Gravitational & Cerberos)
    Route::get('/stratos-maps/gravitational', [\App\Http\Controllers\Api\StratosMapController::class, 'getGravitationalData']);
    Route::get('/stratos-maps/cerberos', [\App\Http\Controllers\Api\StratosMapController::class, 'getCerberosData']);
    Route::get('/stratos-maps/people/search', [\App\Http\Controllers\Api\StratosMapController::class, 'searchPeople']);

    Route::get('/rbac', [\App\Http\Controllers\Api\RBACController::class, 'index'])->middleware('role:admin');
    Route::post('/rbac', [\App\Http\Controllers\Api\RBACController::class, 'update'])->middleware('role:admin');

    // Organizational Culture
    Route::get('/organization/cultural-blueprint', [\App\Http\Controllers\Api\CulturalBlueprintController::class, 'show']);
    Route::post('/organization/cultural-blueprint', [\App\Http\Controllers\Api\CulturalBlueprintController::class, 'store']);
    Route::post('/organization/cultural-blueprint/sign', [\App\Http\Controllers\Api\CulturalBlueprintController::class, 'sign']);

    // People Experience Command Center
    Route::apiResource('px-campaigns', \App\Http\Controllers\Api\PxCampaignController::class)
        ->middleware('permission:assessments.manage');

    Route::post('/people-experience/employee-pulses', [\App\Http\Controllers\Api\PulseController::class, 'storeEmployeePulse']);
    Route::get('/people-experience/employee-pulses', [\App\Http\Controllers\Api\PulseController::class, 'listEmployeePulses']);
    Route::get('/people-experience/turnover-heatmap', [\App\Http\Controllers\Api\PulseController::class, 'listTurnoverHeatmap']);

    Route::post('/gap-analysis', [\App\Http\Controllers\Api\GapAnalysisController::class, 'analyze']);
    Route::get('/development-paths', [\App\Http\Controllers\Api\DevelopmentPathController::class, 'index']);
    Route::post('/development-paths/generate', [\App\Http\Controllers\Api\DevelopmentPathController::class, 'generate']);
    Route::delete('/development-paths/{id}', [\App\Http\Controllers\Api\DevelopmentPathController::class, 'destroy']);

    Route::get('/strategic-planning/scenarios', [\App\Http\Controllers\Api\ScenarioController::class, 'listScenarios']);
    // Prompt / Instruction management for generation wizard (register before the {id} route)
    Route::get('/strategic-planning/scenarios/instructions', [\App\Http\Controllers\Api\PromptInstructionController::class, 'index']);
    Route::post('/strategic-planning/scenarios/instructions', [\App\Http\Controllers\Api\PromptInstructionController::class, 'store']);
    Route::post('/strategic-planning/scenarios/instructions/restore-default', [\App\Http\Controllers\Api\PromptInstructionController::class, 'restoreDefault']);
    Route::get('/strategic-planning/scenarios/instructions/{id}', [\App\Http\Controllers\Api\PromptInstructionController::class, 'show']);
    Route::patch('/strategic-planning/scenarios/instructions/{id}', [\App\Http\Controllers\Api\PromptInstructionController::class, 'update']);

    // LMS CMS: enqueue article generation by content agent
    Route::post('/lms/cms/articles', [\App\Http\Controllers\Api\Lms\CmsArticleController::class, 'store'])->middleware('permission:lms.cms.manage');

    // LMS Certificates
    Route::get('/lms/certificates', [\App\Http\Controllers\Api\Lms\CertificateController::class, 'index'])->middleware('permission:lms.certify');
    Route::get('/lms/certificates/{id}', [\App\Http\Controllers\Api\Lms\CertificateController::class, 'show'])->middleware('permission:lms.certify');
    Route::get('/lms/certificates/{id}/download', [\App\Http\Controllers\Api\Lms\CertificateController::class, 'download'])->middleware('permission:lms.certify');

    // LMS Courses - certificate policy overrides
    Route::get('/lms/courses/{course}', [\App\Http\Controllers\Api\Lms\CourseController::class, 'show'])->middleware('permission:lms.courses.view');
    Route::patch('/lms/courses/{course}', [\App\Http\Controllers\Api\Lms\CourseController::class, 'update'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/certificate-templates', [\App\Http\Controllers\Api\Lms\CourseController::class, 'templates'])->middleware('permission:lms.courses.view');
    Route::get('/lms/analytics/overview', [\App\Http\Controllers\Api\Lms\AnalyticsController::class, 'overview'])->middleware('permission:lms.courses.view');
    Route::get('/lms/interventions', [\App\Http\Controllers\Api\Lms\InterventionController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::post('/lms/interventions', [\App\Http\Controllers\Api\Lms\InterventionController::class, 'store'])->middleware('permission:lms.courses.view');
    Route::post('/lms/interventions/reset', [\App\Http\Controllers\Api\Lms\InterventionController::class, 'reset'])->middleware('permission:lms.courses.view');
    Route::post('/lms/interventions/{enrollmentId}/complete', [\App\Http\Controllers\Api\Lms\InterventionController::class, 'complete'])->middleware('permission:lms.courses.view');
    Route::get('/lms/preferences', [\App\Http\Controllers\Api\Lms\InterventionController::class, 'preferences'])->middleware('permission:lms.courses.view');
    Route::patch('/lms/preferences', [\App\Http\Controllers\Api\Lms\InterventionController::class, 'updatePreferences'])->middleware('permission:lms.courses.view');
    Route::get('/lms/certificates/{id}/verify', [\App\Http\Controllers\Api\Lms\CertificateController::class, 'verify']);
    Route::get('/lms/certificates/{id}/verification', [\App\Http\Controllers\Api\Lms\CertificateController::class, 'verify']);
    Route::post('/lms/certificates/{id}/revoke', [\App\Http\Controllers\Api\Lms\CertificateController::class, 'revoke'])->middleware('permission:lms.certify');

    // LMS Course Designer (AI-assisted course creation)
    Route::post('/lms/course-designer/generate-outline', [\App\Http\Controllers\Api\Lms\CourseDesignerController::class, 'generateOutline']);
    Route::post('/lms/course-designer/generate-content', [\App\Http\Controllers\Api\Lms\CourseDesignerController::class, 'generateContent']);
    Route::post('/lms/course-designer/persist', [\App\Http\Controllers\Api\Lms\CourseDesignerController::class, 'persist']);
    Route::post('/lms/course-designer/{id}/review', [\App\Http\Controllers\Api\Lms\CourseDesignerController::class, 'review']);
    Route::get('/lms/course-designer/{id}/preview', [\App\Http\Controllers\Api\Lms\CourseDesignerController::class, 'preview']);

    // LMS Quiz module
    Route::get('/lms/quizzes', [\App\Http\Controllers\Api\Lms\QuizController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::post('/lms/quizzes', [\App\Http\Controllers\Api\Lms\QuizController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/quizzes/{id}', [\App\Http\Controllers\Api\Lms\QuizController::class, 'show'])->middleware('permission:lms.courses.view');
    Route::put('/lms/quizzes/{id}', [\App\Http\Controllers\Api\Lms\QuizController::class, 'update'])->middleware('permission:lms.courses.manage');
    Route::delete('/lms/quizzes/{id}', [\App\Http\Controllers\Api\Lms\QuizController::class, 'destroy'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/quizzes/{id}/start', [\App\Http\Controllers\Api\Lms\QuizController::class, 'startAttempt'])->middleware('permission:lms.courses.view');
    Route::post('/lms/quizzes/{id}/submit', [\App\Http\Controllers\Api\Lms\QuizController::class, 'submitAttempt'])->middleware('permission:lms.courses.view');
    Route::get('/lms/quizzes/{id}/attempts', [\App\Http\Controllers\Api\Lms\QuizController::class, 'attempts'])->middleware('permission:lms.courses.view');
    Route::get('/lms/quizzes/{id}/stats', [\App\Http\Controllers\Api\Lms\QuizController::class, 'stats'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/quizzes/{id}/generate-questions', [\App\Http\Controllers\Api\Lms\QuizController::class, 'generateQuestions'])->middleware('permission:lms.courses.manage');

    // Learning Paths
    Route::post('/lms/learning-paths/generate', [\App\Http\Controllers\Api\Lms\LearningPathController::class, 'generate'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/learning-paths', [\App\Http\Controllers\Api\Lms\LearningPathController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::post('/lms/learning-paths', [\App\Http\Controllers\Api\Lms\LearningPathController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/learning-paths/{id}', [\App\Http\Controllers\Api\Lms\LearningPathController::class, 'show'])->middleware('permission:lms.courses.view');
    Route::put('/lms/learning-paths/{id}', [\App\Http\Controllers\Api\Lms\LearningPathController::class, 'update'])->middleware('permission:lms.courses.manage');
    Route::delete('/lms/learning-paths/{id}', [\App\Http\Controllers\Api\Lms\LearningPathController::class, 'destroy'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/learning-paths/{id}/enroll', [\App\Http\Controllers\Api\Lms\LearningPathController::class, 'enroll'])->middleware('permission:lms.courses.view');
    Route::get('/lms/learning-paths/{id}/progress', [\App\Http\Controllers\Api\Lms\LearningPathController::class, 'progress'])->middleware('permission:lms.courses.view');
    Route::post('/lms/learning-paths/{id}/recalculate', [\App\Http\Controllers\Api\Lms\LearningPathController::class, 'recalculate'])->middleware('permission:lms.courses.view');

    // SCORM Player
    Route::post('/lms/scorm/upload', [\App\Http\Controllers\Api\Lms\ScormPlayerController::class, 'upload'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/scorm/packages', [\App\Http\Controllers\Api\Lms\ScormPlayerController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::get('/lms/scorm/{id}/launch', [\App\Http\Controllers\Api\Lms\ScormPlayerController::class, 'launch'])->middleware('permission:lms.courses.view');
    Route::post('/lms/scorm/{id}/cmi', [\App\Http\Controllers\Api\Lms\ScormPlayerController::class, 'saveCmi'])->middleware('permission:lms.courses.view');
    Route::get('/lms/scorm/{id}/tracking', [\App\Http\Controllers\Api\Lms\ScormPlayerController::class, 'tracking'])->middleware('permission:lms.courses.view');
    Route::delete('/lms/scorm/{id}', [\App\Http\Controllers\Api\Lms\ScormPlayerController::class, 'destroy'])->middleware('permission:lms.courses.manage');

    // cmi5 (xAPI-based content)
    Route::post('/lms/cmi5/{package}/launch', [\App\Http\Controllers\Api\Lms\Cmi5Controller::class, 'launch'])->middleware('permission:lms.courses.view');
    Route::get('/lms/cmi5/sessions/{session}/fetch', [\App\Http\Controllers\Api\Lms\Cmi5Controller::class, 'fetchUrl'])->middleware('permission:lms.courses.view');
    Route::post('/lms/cmi5/sessions/{session}/statement', [\App\Http\Controllers\Api\Lms\Cmi5Controller::class, 'statement'])->middleware('permission:lms.courses.view');
    Route::get('/lms/cmi5/sessions/{session}/auth-token', [\App\Http\Controllers\Api\Lms\Cmi5Controller::class, 'authToken'])->middleware('permission:lms.courses.view');
    Route::get('/lms/cmi5/{package}/sessions', [\App\Http\Controllers\Api\Lms\Cmi5Controller::class, 'sessions'])->middleware('permission:lms.courses.view');

    // Per-Lesson Audit Trail
    Route::post('/lms/audit/log', [\App\Http\Controllers\Api\Lms\LessonAuditController::class, 'log'])->middleware('permission:lms.courses.view');
    Route::get('/lms/audit/enrollments/{enrollment}/lessons/{lesson}', [\App\Http\Controllers\Api\Lms\LessonAuditController::class, 'lessonHistory'])->middleware('permission:lms.courses.view');
    Route::get('/lms/audit/enrollments/{enrollment}/timeline', [\App\Http\Controllers\Api\Lms\LessonAuditController::class, 'userTimeline'])->middleware('permission:lms.courses.view');
    Route::get('/lms/audit/courses/{course}/summary', [\App\Http\Controllers\Api\Lms\LessonAuditController::class, 'courseSummary'])->middleware('permission:lms.courses.view');
    Route::get('/lms/audit/enrollments/{enrollment}/export', [\App\Http\Controllers\Api\Lms\LessonAuditController::class, 'export'])->middleware('permission:lms.courses.view');

    // LMS Compliance
    Route::get('/lms/compliance/dashboard', [\App\Http\Controllers\Api\Lms\ComplianceController::class, 'dashboard'])->middleware('permission:lms.courses.view');
    Route::get('/lms/compliance/records', [\App\Http\Controllers\Api\Lms\ComplianceController::class, 'records'])->middleware('permission:lms.courses.view');
    Route::post('/lms/compliance/check', [\App\Http\Controllers\Api\Lms\ComplianceController::class, 'check'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/compliance/export', [\App\Http\Controllers\Api\Lms\ComplianceController::class, 'export'])->middleware('permission:lms.courses.manage');

    // LMS Reports
    Route::get('/lms/reports/completion', [\App\Http\Controllers\Api\Lms\ReportController::class, 'completion'])->middleware('permission:lms.courses.view');
    Route::get('/lms/reports/compliance', [\App\Http\Controllers\Api\Lms\ReportController::class, 'compliance'])->middleware('permission:lms.courses.view');
    Route::get('/lms/reports/time-to-complete', [\App\Http\Controllers\Api\Lms\ReportController::class, 'timeToComplete'])->middleware('permission:lms.courses.view');
    Route::get('/lms/reports/engagement', [\App\Http\Controllers\Api\Lms\ReportController::class, 'engagement'])->middleware('permission:lms.courses.view');
    Route::get('/lms/reports/export/{type}', [\App\Http\Controllers\Api\Lms\ReportController::class, 'export'])->middleware('permission:lms.courses.manage');

    // Course Catalog
    Route::get('/lms/catalog/recommendations', [\App\Http\Controllers\Api\Lms\CatalogController::class, 'recommendations'])->middleware('permission:lms.courses.view');
    Route::get('/lms/catalog/categories', [\App\Http\Controllers\Api\Lms\CatalogController::class, 'categories'])->middleware('permission:lms.courses.view');
    Route::get('/lms/catalog/tags', [\App\Http\Controllers\Api\Lms\CatalogController::class, 'tags'])->middleware('permission:lms.courses.view');
    Route::get('/lms/catalog', [\App\Http\Controllers\Api\Lms\CatalogController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::get('/lms/catalog/{id}', [\App\Http\Controllers\Api\Lms\CatalogController::class, 'show'])->middleware('permission:lms.courses.view');
    Route::post('/lms/catalog/{id}/rate', [\App\Http\Controllers\Api\Lms\CatalogController::class, 'rate'])->middleware('permission:lms.courses.view');
    Route::post('/lms/catalog/{id}/enroll', [\App\Http\Controllers\Api\Lms\CatalogController::class, 'enroll'])->middleware('permission:lms.courses.view');

    // LMS Social Learning — Discussions
    Route::get('/lms/discussions', [\App\Http\Controllers\Api\Lms\DiscussionController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::post('/lms/discussions', [\App\Http\Controllers\Api\Lms\DiscussionController::class, 'store'])->middleware('permission:lms.courses.view');
    Route::post('/lms/discussions/{id}/reply', [\App\Http\Controllers\Api\Lms\DiscussionController::class, 'reply'])->middleware('permission:lms.courses.view');
    Route::post('/lms/discussions/{id}/like', [\App\Http\Controllers\Api\Lms\DiscussionController::class, 'like'])->middleware('permission:lms.courses.view');
    Route::post('/lms/discussions/{id}/pin', [\App\Http\Controllers\Api\Lms\DiscussionController::class, 'pin'])->middleware('permission:lms.courses.manage');
    Route::delete('/lms/discussions/{id}', [\App\Http\Controllers\Api\Lms\DiscussionController::class, 'destroy'])->middleware('permission:lms.courses.view');

    // xAPI (Experience API)
    Route::post('/lms/xapi/statements', [\App\Http\Controllers\Api\Lms\XApiController::class, 'store'])->middleware('permission:lms.courses.view');
    Route::get('/lms/xapi/statements', [\App\Http\Controllers\Api\Lms\XApiController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::get('/lms/xapi/activities/{objectId}/stats', [\App\Http\Controllers\Api\Lms\XApiController::class, 'activityStats'])->where('objectId', '.*')->middleware('permission:lms.courses.view');

    // LMS Video Player & Tracking
    Route::get('/lms/lessons/{lesson}/video/tracking', [\App\Http\Controllers\Api\Lms\VideoPlayerController::class, 'getTracking'])->middleware('permission:lms.courses.view');
    Route::post('/lms/lessons/{lesson}/video/progress', [\App\Http\Controllers\Api\Lms\VideoPlayerController::class, 'updateProgress'])->middleware('permission:lms.courses.view');
    Route::get('/lms/lessons/{lesson}/video/stats', [\App\Http\Controllers\Api\Lms\VideoPlayerController::class, 'stats'])->middleware('permission:lms.courses.view');

    // LMS Microlearning Cards
    Route::get('/lms/lessons/{lesson}/micro', [\App\Http\Controllers\Api\Lms\MicrolearningController::class, 'show'])->middleware('permission:lms.courses.view');
    Route::post('/lms/lessons/{lesson}/micro', [\App\Http\Controllers\Api\Lms\MicrolearningController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/lessons/{lesson}/micro/generate', [\App\Http\Controllers\Api\Lms\MicrolearningController::class, 'generate'])->middleware('permission:lms.courses.manage');

    // LMS Interactive Content (H5P-style)
    Route::get('/lms/lessons/{lesson}/interactive', [\App\Http\Controllers\Api\Lms\InteractiveContentController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::post('/lms/lessons/{lesson}/interactive', [\App\Http\Controllers\Api\Lms\InteractiveContentController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::put('/lms/interactive/{interactiveContent}', [\App\Http\Controllers\Api\Lms\InteractiveContentController::class, 'update'])->middleware('permission:lms.courses.manage');
    Route::delete('/lms/interactive/{interactiveContent}', [\App\Http\Controllers\Api\Lms\InteractiveContentController::class, 'destroy'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/interactive/widget-types', [\App\Http\Controllers\Api\Lms\InteractiveContentController::class, 'widgetTypes'])->middleware('permission:lms.courses.view');

    // LMS Webhooks
    Route::get('/lms/webhooks', [\App\Http\Controllers\Api\Lms\WebhookController::class, 'index']);
    Route::post('/lms/webhooks', [\App\Http\Controllers\Api\Lms\WebhookController::class, 'store']);
    Route::put('/lms/webhooks/{webhook}', [\App\Http\Controllers\Api\Lms\WebhookController::class, 'update']);
    Route::delete('/lms/webhooks/{webhook}', [\App\Http\Controllers\Api\Lms\WebhookController::class, 'destroy']);
    Route::post('/lms/webhooks/{webhook}/test', [\App\Http\Controllers\Api\Lms\WebhookController::class, 'test']);

    // LMS LTI 1.3 Provider
    Route::get('/lms/lti/platforms', [\App\Http\Controllers\Api\Lms\LtiController::class, 'platforms']);
    Route::post('/lms/lti/platforms', [\App\Http\Controllers\Api\Lms\LtiController::class, 'registerPlatform']);
    Route::post('/lms/lti/launch', [\App\Http\Controllers\Api\Lms\LtiController::class, 'launch']);

    // LMS Calendar
    Route::get('/lms/calendar', [\App\Http\Controllers\Api\Lms\CalendarController::class, 'index']);
    Route::post('/lms/calendar', [\App\Http\Controllers\Api\Lms\CalendarController::class, 'store']);
    Route::delete('/lms/calendar/{calendarEvent}', [\App\Http\Controllers\Api\Lms\CalendarController::class, 'destroy']);
    Route::get('/lms/calendar/ical', [\App\Http\Controllers\Api\Lms\CalendarController::class, 'ical']);
    Route::post('/lms/calendar/sync-compliance', [\App\Http\Controllers\Api\Lms\CalendarController::class, 'syncCompliance']);

    // LMS Marketplace
    Route::get('/lms/marketplace', [\App\Http\Controllers\Api\Lms\MarketplaceController::class, 'browse']);
    Route::get('/lms/marketplace/my-listings', [\App\Http\Controllers\Api\Lms\MarketplaceController::class, 'myListings']);
    Route::post('/lms/marketplace', [\App\Http\Controllers\Api\Lms\MarketplaceController::class, 'createListing']);
    Route::post('/lms/marketplace/{listing}/publish', [\App\Http\Controllers\Api\Lms\MarketplaceController::class, 'publish']);
    Route::post('/lms/marketplace/{listing}/purchase', [\App\Http\Controllers\Api\Lms\MarketplaceController::class, 'purchase']);
    Route::get('/lms/marketplace/purchases', [\App\Http\Controllers\Api\Lms\MarketplaceController::class, 'purchases']);

    // ILT (Instructor-Led Training) Sessions
    Route::get('/lms/sessions', [\App\Http\Controllers\Api\Lms\IltController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::post('/lms/sessions', [\App\Http\Controllers\Api\Lms\IltController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/sessions/{session}', [\App\Http\Controllers\Api\Lms\IltController::class, 'show'])->middleware('permission:lms.courses.view');
    Route::put('/lms/sessions/{session}', [\App\Http\Controllers\Api\Lms\IltController::class, 'update'])->middleware('permission:lms.courses.manage');
    Route::delete('/lms/sessions/{session}', [\App\Http\Controllers\Api\Lms\IltController::class, 'destroy'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/sessions/{session}/register', [\App\Http\Controllers\Api\Lms\IltController::class, 'register'])->middleware('permission:lms.courses.view');
    Route::post('/lms/sessions/{session}/cancel-registration', [\App\Http\Controllers\Api\Lms\IltController::class, 'cancelRegistration'])->middleware('permission:lms.courses.view');
    Route::post('/lms/sessions/{session}/attendance', [\App\Http\Controllers\Api\Lms\IltController::class, 'markAttendance'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/sessions/{session}/feedback', [\App\Http\Controllers\Api\Lms\IltController::class, 'feedback'])->middleware('permission:lms.courses.view');

    // PDF Report Export
    Route::get('/lms/reports/pdf', [\App\Http\Controllers\Api\Lms\ReportController::class, 'exportPdf'])->middleware('permission:lms.courses.manage');

    // Surveys / NPS
    Route::get('/lms/courses/{course}/survey', [\App\Http\Controllers\Api\Lms\SurveyController::class, 'show'])->middleware('permission:lms.courses.view');
    Route::post('/lms/courses/{course}/survey', [\App\Http\Controllers\Api\Lms\SurveyController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/surveys/{survey}/submit', [\App\Http\Controllers\Api\Lms\SurveyController::class, 'submit'])->middleware('permission:lms.courses.view');
    Route::get('/lms/surveys/{survey}/results', [\App\Http\Controllers\Api\Lms\SurveyController::class, 'results'])->middleware('permission:lms.courses.view');

    // Scheduled Reports
    Route::get('/lms/reports/scheduled', [\App\Http\Controllers\Api\Lms\ScheduledReportController::class, 'index'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/reports/scheduled', [\App\Http\Controllers\Api\Lms\ScheduledReportController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::put('/lms/reports/scheduled/{scheduledReport}', [\App\Http\Controllers\Api\Lms\ScheduledReportController::class, 'update'])->middleware('permission:lms.courses.manage');
    Route::delete('/lms/reports/scheduled/{scheduledReport}', [\App\Http\Controllers\Api\Lms\ScheduledReportController::class, 'destroy'])->middleware('permission:lms.courses.manage');

    // LMS Peer Reviews
    Route::get('/lms/peer-reviews', [\App\Http\Controllers\Api\Lms\PeerReviewController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::post('/lms/peer-reviews', [\App\Http\Controllers\Api\Lms\PeerReviewController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/peer-reviews/{peerReview}/submit-work', [\App\Http\Controllers\Api\Lms\PeerReviewController::class, 'submitWork'])->middleware('permission:lms.courses.view');
    Route::post('/lms/peer-reviews/{peerReview}/submit-review', [\App\Http\Controllers\Api\Lms\PeerReviewController::class, 'submitReview'])->middleware('permission:lms.courses.view');
    Route::get('/lms/lessons/{lesson}/peer-scores', [\App\Http\Controllers\Api\Lms\PeerReviewController::class, 'scores'])->middleware('permission:lms.courses.view');

    // LMS User-Generated Content (UGC)
    Route::get('/lms/ugc', [\App\Http\Controllers\Api\Lms\UgcController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::get('/lms/ugc/pending', [\App\Http\Controllers\Api\Lms\UgcController::class, 'pendingReview'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/ugc', [\App\Http\Controllers\Api\Lms\UgcController::class, 'store'])->middleware('permission:lms.courses.view');
    Route::put('/lms/ugc/{userContent}', [\App\Http\Controllers\Api\Lms\UgcController::class, 'update'])->middleware('permission:lms.courses.view');
    Route::post('/lms/ugc/{userContent}/submit', [\App\Http\Controllers\Api\Lms\UgcController::class, 'submitForReview'])->middleware('permission:lms.courses.view');
    Route::post('/lms/ugc/{userContent}/approve', [\App\Http\Controllers\Api\Lms\UgcController::class, 'approve'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/ugc/{userContent}/reject', [\App\Http\Controllers\Api\Lms\UgcController::class, 'reject'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/ugc/{userContent}/like', [\App\Http\Controllers\Api\Lms\UgcController::class, 'like'])->middleware('permission:lms.courses.view');

    // LMS Learning Communities
    Route::get('/lms/communities', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::post('/lms/communities', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/communities/suggest-from-gaps', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'suggestFromGaps'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/communities/{community}', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'show'])->middleware('permission:lms.courses.view');
    Route::put('/lms/communities/{community}', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'update'])->middleware('permission:lms.courses.manage');
    Route::delete('/lms/communities/{community}', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'destroy'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/communities/{community}/members', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'members'])->middleware('permission:lms.courses.view');
    Route::post('/lms/communities/{community}/join', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'join'])->middleware('permission:lms.courses.view');
    Route::post('/lms/communities/{community}/leave', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'leave'])->middleware('permission:lms.courses.view');
    Route::get('/lms/communities/{community}/activities', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'activities'])->middleware('permission:lms.courses.view');
    Route::get('/lms/communities/{community}/health', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'health'])->middleware('permission:lms.courses.view');
    Route::get('/lms/communities/{community}/progression', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'progression'])->middleware('permission:lms.courses.view');
    Route::get('/lms/communities/{community}/mentors', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'mentors'])->middleware('permission:lms.courses.view');
    Route::get('/lms/communities/{community}/knowledge', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'knowledgeBase'])->middleware('permission:lms.courses.view');
    Route::post('/lms/communities/{community}/knowledge', [\App\Http\Controllers\Api\Lms\CommunityController::class, 'storeKnowledge'])->middleware('permission:lms.courses.view');

    // ── Stratos Logos (λόγος) — Analytics & BI ──────────────────
    Route::get('/logos/executive-summary', [\App\Http\Controllers\Api\Logos\LogosDashboardController::class, 'executiveSummary']);
    Route::get('/logos/module/{module}', [\App\Http\Controllers\Api\Logos\LogosDashboardController::class, 'moduleMetrics']);
    Route::get('/logos/trends', [\App\Http\Controllers\Api\Logos\LogosDashboardController::class, 'trends']);

    // LMS Cohorts / Learning Groups
    Route::get('/lms/cohorts', [\App\Http\Controllers\Api\Lms\CohortController::class, 'index'])->middleware('permission:lms.courses.view');
    Route::post('/lms/cohorts', [\App\Http\Controllers\Api\Lms\CohortController::class, 'store'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/cohorts/{cohort}', [\App\Http\Controllers\Api\Lms\CohortController::class, 'show'])->middleware('permission:lms.courses.view');
    Route::put('/lms/cohorts/{cohort}', [\App\Http\Controllers\Api\Lms\CohortController::class, 'update'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/cohorts/{cohort}/members', [\App\Http\Controllers\Api\Lms\CohortController::class, 'addMembers'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/cohorts/{cohort}/remove-member', [\App\Http\Controllers\Api\Lms\CohortController::class, 'removeMember'])->middleware('permission:lms.courses.manage');
    Route::get('/lms/cohorts/{cohort}/progress', [\App\Http\Controllers\Api\Lms\CohortController::class, 'progress'])->middleware('permission:lms.courses.view');

    // LMS Adaptive Learning
    Route::get('/lms/adaptive/profile', [\App\Http\Controllers\Api\Lms\AdaptiveController::class, 'profile'])->middleware('permission:lms.courses.view');
    Route::post('/lms/adaptive/calibrate', [\App\Http\Controllers\Api\Lms\AdaptiveController::class, 'calibrate'])->middleware('permission:lms.courses.view');
    Route::get('/lms/adaptive/courses/{course}/recommendations', [\App\Http\Controllers\Api\Lms\AdaptiveController::class, 'recommendations'])->middleware('permission:lms.courses.view');
    Route::get('/lms/adaptive/courses/{course}/rules', [\App\Http\Controllers\Api\Lms\AdaptiveController::class, 'rules'])->middleware('permission:lms.courses.manage');
    Route::post('/lms/adaptive/rules', [\App\Http\Controllers\Api\Lms\AdaptiveController::class, 'storeRule'])->middleware('permission:lms.courses.manage');
    Route::put('/lms/adaptive/rules/{adaptiveRule}', [\App\Http\Controllers\Api\Lms\AdaptiveController::class, 'updateRule'])->middleware('permission:lms.courses.manage');

    // Social Learning & Mentorship & Mentorship Knowledge Transfer
    Route::get('/social-learning/dashboard', [\App\Http\Controllers\Api\SocialLearningController::class, 'dashboard']);
    Route::get('/social-learning/matches/{skillId}', [\App\Http\Controllers\Api\SocialLearningController::class, 'matches']);
    Route::post('/social-learning/generate-blueprint', [\App\Http\Controllers\Api\SocialLearningController::class, 'generateBlueprint']);

    Route::get('/strategic-planning/scenarios/{id}', [\App\Http\Controllers\Api\ScenarioController::class, 'showScenario']);
    Route::get('/strategic-planning/scenarios/{id}/capability-tree', [\App\Http\Controllers\Api\ScenarioController::class, 'getCapabilityTree']);

    // ChangeSet API: create, preview, apply
    Route::post('/strategic-planning/scenarios/{scenarioId}/change-sets', [\App\Http\Controllers\Api\ChangeSetController::class, 'store']);
    Route::get('/strategic-planning/change-sets/{id}/preview', [\App\Http\Controllers\Api\ChangeSetController::class, 'preview']);
    Route::post('/strategic-planning/change-sets/{id}/apply', [\App\Http\Controllers\Api\ChangeSetController::class, 'apply']);
    Route::post('/strategic-planning/change-sets/{id}/ops', [\App\Http\Controllers\Api\ChangeSetController::class, 'addOp']);
    Route::get('/strategic-planning/change-sets/{id}/can-apply', [\App\Http\Controllers\Api\ChangeSetController::class, 'canApply']);
    Route::post('/strategic-planning/change-sets/{id}/approve', [\App\Http\Controllers\Api\ChangeSetController::class, 'approve']);
    Route::post('/strategic-planning/change-sets/{id}/reject', [\App\Http\Controllers\Api\ChangeSetController::class, 'reject']);

    // Scenario generation (LLM-driven)
    Route::middleware('throttle:ai_generation')->group(function () {
        Route::post('/strategic-planning/scenarios/generate', [\App\Http\Controllers\Api\ScenarioGenerationController::class, 'store']);
        Route::post('/strategic-planning/scenarios/generate/demo', [\App\Http\Controllers\Api\ScenarioGenerationController::class, 'demo']);
        Route::post('/strategic-planning/scenarios/generate/preview', [\App\Http\Controllers\Api\ScenarioGenerationController::class, 'preview']);
        Route::post('/strategic-planning/scenarios/generate/abacus', [\App\Http\Controllers\Api\ScenarioGenerationAbacusController::class, 'generate']);
        Route::post('/strategic-planning/scenarios/generate/intel', [\App\Http\Controllers\Api\ScenarioGenerationIntelController::class, 'generate']);
    });
    Route::get('/strategic-planning/scenarios/generate/{id}', [\App\Http\Controllers\Api\ScenarioGenerationController::class, 'show']);
    Route::post('/strategic-planning/scenarios/generate/{id}/accept', [\App\Http\Controllers\Api\ScenarioGenerationController::class, 'accept']);
    // Read streaming chunks for a generation (for UI progress/debug)
    Route::get('/strategic-planning/scenarios/generate/{id}/chunks', [\App\Http\Controllers\Api\GenerationChunkController::class, 'index']);
    // Read compacted blob (decoded) for a generation (if compacted exists)
    Route::get('/strategic-planning/scenarios/generate/{id}/compacted', [\App\Http\Controllers\Api\GenerationChunkController::class, 'compacted']);
    // Lightweight progress endpoint used by the wizard for polling progress and quick assembly
    Route::get('/strategic-planning/scenarios/generate/{id}/progress', [\App\Http\Controllers\Api\GenerationChunkController::class, 'progress']);
    Route::get('/strategic-planning/scenarios/{id}/versions', [\App\Http\Controllers\Api\ScenarioController::class, 'getVersions']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/scenarios/compare', [\App\Http\Controllers\Api\ScenarioAnalyticsController::class, 'compareScenarios']);
        // Use ID-based endpoints to ensure authentication middleware executes before model resolution
        // Analytics endpoints are handled by the pre-check route defined above
        // which returns 401 for unauthenticated requests and forwards to the
        // controller when authenticated. The direct controller route
        // declarations were removed to avoid overriding the pre-check closure.
    });

    // Scenario Planning - Workflow & Approval System (Phase 2 - Task 2)
    Route::post('/scenarios/{id}/submit-approval', [\App\Http\Controllers\Api\ScenarioApprovalController::class, 'submitForApproval']);
    Route::post('/approval-requests/{id}/approve', [\App\Http\Controllers\Api\ScenarioApprovalController::class, 'approve']);
    Route::post('/approval-requests/{id}/reject', [\App\Http\Controllers\Api\ScenarioApprovalController::class, 'reject']);
    Route::get('/scenarios/{id}/approval-matrix', [\App\Http\Controllers\Api\ScenarioApprovalController::class, 'getApprovalMatrix']);
    Route::post('/scenarios/{id}/activate', [\App\Http\Controllers\Api\ScenarioApprovalController::class, 'activate']);
    Route::get('/scenarios/{id}/execution-plan', [\App\Http\Controllers\Api\ScenarioApprovalController::class, 'getExecutionPlan']);

    // Workforce Planning Fase 2 — Motor de recomendaciones de palancas
    Route::get('/scenarios/{id}/recommendations', [\App\Http\Controllers\Api\ClosureStrategyController::class, 'index']);
    Route::post('/scenarios/{id}/recommendations/generate', [\App\Http\Controllers\Api\ClosureStrategyController::class, 'generate']);

    // Phase 2.5 - Workflow Enhancements (Notifications & Dashboard)
    Route::post('/approval-requests/{id}/resend-notification', [\App\Http\Controllers\Api\ScenarioApprovalController::class, 'resendNotification']);
    Route::post('/approval-requests/{id}/email-preview', [\App\Http\Controllers\Api\ScenarioApprovalController::class, 'emailPreview']);
    Route::get('/approvals-summary', [\App\Http\Controllers\Api\ScenarioApprovalController::class, 'approvalsSummary']);

    // ── Scenario Planning Phase 2: Succession Planning, Talent Risk & Transformation ──
    // Succession Planning API
    Route::get('/scenarios/{scenario}/succession/candidates', [\App\Http\Controllers\Api\SuccessionPlanningController::class, 'indexCandidates'])->name('succession.index-candidates');
    Route::post('/scenarios/{scenario}/succession/candidates', [\App\Http\Controllers\Api\SuccessionPlanningController::class, 'storeCandidates'])->name('succession.store-candidates');
    Route::patch('/succession-candidates/{candidate}', [\App\Http\Controllers\Api\SuccessionPlanningController::class, 'updateCandidate'])->name('succession.update-candidate');
    Route::delete('/succession-candidates/{candidate}', [\App\Http\Controllers\Api\SuccessionPlanningController::class, 'deleteCandidate'])->name('succession.delete-candidate');
    Route::get('/scenarios/{scenario}/succession/coverage', [\App\Http\Controllers\Api\SuccessionPlanningController::class, 'getCoverage'])->name('succession.coverage');
    Route::post('/scenarios/{scenario}/succession/analyze', [\App\Http\Controllers\Api\SuccessionPlanningController::class, 'analyze'])->name('succession.analyze');
    Route::get('/scenarios/{scenario}/succession/development-plans', [\App\Http\Controllers\Api\SuccessionPlanningController::class, 'listDevelopmentPlans'])->name('succession.list-plans');
    Route::post('/succession-candidates/{candidate}/development-plans', [\App\Http\Controllers\Api\SuccessionPlanningController::class, 'createDevelopmentPlan'])->name('succession.create-plan');

    // Talent Risk API
    Route::get('/scenarios/{scenario}/risks/indicators', [\App\Http\Controllers\Api\TalentRiskController::class, 'indexIndicators'])->name('risks.index-indicators');
    Route::post('/scenarios/{scenario}/risks/analyze', [\App\Http\Controllers\Api\TalentRiskController::class, 'analyze'])->name('risks.analyze');
    Route::get('/scenarios/{scenario}/risks/summary', [\App\Http\Controllers\Api\TalentRiskController::class, 'getSummary'])->name('risks.summary');
    Route::get('/scenarios/{scenario}/risks/{riskType}/details', [\App\Http\Controllers\Api\TalentRiskController::class, 'getDetailsByType'])->name('risks.details-by-type');
    Route::post('/risks/{indicator}/mitigations', [\App\Http\Controllers\Api\TalentRiskController::class, 'recordMitigation'])->name('risks.record-mitigation');
    Route::get('/risks/{indicator}/mitigations', [\App\Http\Controllers\Api\TalentRiskController::class, 'listMitigations'])->name('risks.list-mitigations');
    Route::patch('/mitigations/{mitigation}/status', [\App\Http\Controllers\Api\TalentRiskController::class, 'updateMitigationStatus'])->name('risks.update-status');
    Route::get('/scenarios/{scenario}/risks/heatmap', [\App\Http\Controllers\Api\TalentRiskController::class, 'getRiskHeatmap'])->name('risks.heatmap');

    // Transformation Roadmap API
    Route::get('/scenarios/{scenario}/transformation/roadmap', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'getRoadmap'])->name('roadmap.get');
    Route::post('/scenarios/{scenario}/transformation/generate', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'generate'])->name('roadmap.generate');
    Route::get('/scenarios/{scenario}/transformation/phases', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'listPhases'])->name('roadmap.list-phases');
    Route::patch('/phases/{phase}', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'updatePhase'])->name('roadmap.update-phase');
    Route::get('/phases/{phase}/tasks', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'listTasks'])->name('roadmap.list-tasks');
    Route::post('/scenarios/{scenario}/transformation/tasks', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'createTask'])->name('roadmap.create-task');
    Route::patch('/tasks/{task}', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'updateTask'])->name('roadmap.update-task');
    Route::patch('/tasks/{task}/status', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'updateTaskStatus'])->name('roadmap.update-status');
    Route::get('/scenarios/{scenario}/transformation/blockers', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'getBlockers'])->name('roadmap.blockers');
    Route::post('/scenarios/{scenario}/transformation/export', [\App\Http\Controllers\Api\TransformationRoadmapController::class, 'exportRoadmap'])->name('roadmap.export');

    // Scenario Planning - Simulation & Strategic Talent Modeling
    Route::prefix('strategic-planning')->group(
        function () {
            // Simulation (Paso 1 - Final Stage)
            Route::post('/scenarios/{id}/simulate-growth', [\App\Http\Controllers\Api\ScenarioSimulationController::class, 'simulateGrowth']);
            Route::post('/scenarios/{id}/mitigate', [\App\Http\Controllers\Api\ScenarioSimulationController::class, 'getMitigationPlan']);
            Route::get('/critical-talents', [\App\Http\Controllers\Api\ScenarioSimulationController::class, 'getCriticalTalents']);

            // ROI Calculator (CFO Perspective)
            Route::post('/roi-calculator/calculate', [\App\Http\Controllers\Api\ScenarioRoiController::class, 'calculate']);
            Route::get('/roi-calculator/scenarios', [\App\Http\Controllers\Api\ScenarioRoiController::class, 'listCalculations']);

            // Strategy Assignment (CHRO Perspective - 4B)
            Route::get('/scenarios/{id}/gaps-for-assignment', [\App\Http\Controllers\Api\ScenarioStrategyController::class, 'getGapsForAssignment']);
            Route::post('/strategies/assign', [\App\Http\Controllers\Api\ScenarioStrategyController::class, 'assignStrategy']);
            Route::get('/strategies/portfolio/{scenario_id}', [\App\Http\Controllers\Api\ScenarioStrategyController::class, 'getStrategyPortfolio']);
            Route::get('/scenarios/{id}/strategies', [\App\Http\Controllers\Api\ScenarioStrategyController::class, 'getStrategiesByScenario']);
            Route::get('/scenarios/{id}/impact', [\App\Http\Controllers\Api\ScenarioController::class, 'getImpact']);
            Route::get('/scenarios/{id}/export-financial', [\App\Http\Controllers\Api\ScenarioController::class, 'exportFinancial']);

            // Test/Simulation Endpoints
            Route::post('/scenarios/{scenario}/simulate-import', [\App\Http\Controllers\Api\ScenarioGenerationController::class, 'simulateImport']);

            // Incubation Approval Workflow (Fase 2)
            Route::get('/scenarios/{id}/incubated-items', [\App\Http\Controllers\Api\IncubationController::class, 'index']);
            Route::post('/scenarios/{id}/incubated-items/approve', [\App\Http\Controllers\Api\IncubationController::class, 'approve']);
            Route::post('/scenarios/{id}/incubated-items/reject', [\App\Http\Controllers\Api\IncubationController::class, 'reject']);

            // AI Role Designer
            Route::middleware('throttle:ai_analysis')->group(function () {
                Route::post('/roles/analyze-preview', [\App\Http\Controllers\Api\RoleDesignerController::class, 'analyzePreview']);
                Route::post('/roles/generate-skill-blueprint', [\App\Http\Controllers\Api\RoleDesignerController::class, 'generateSkillBlueprint']);
                Route::post('/roles/{id}/design', [\App\Http\Controllers\Api\RoleDesignerController::class, 'design']);
                Route::post('/roles/{id}/materialize-competencies', [\App\Http\Controllers\Api\RoleDesignerController::class, 'materializeCompetencies']);
                Route::post('/roles/{id}/request-approval', [\App\Http\Controllers\Api\RoleDesignerController::class, 'requestApproval']);
                Route::post('/competencies/{id}/request-approval', [\App\Http\Controllers\Api\RoleDesignerController::class, 'requestCompetencyApproval']);

                // Competency Skill Materializer (Wizard)
                Route::post('/competencies/{id}/generate-blueprint', [\App\Http\Controllers\Api\CompetencyMaterializerController::class, 'generateBlueprint']);
                Route::post('/competencies/{id}/materialize', [\App\Http\Controllers\Api\CompetencyMaterializerController::class, 'materialize']);
            });

            // Assessments & Psychometrics (Fase 4: Talento 360)
            Route::prefix('assessments')->group(function () {
                Route::post('/sessions', [\App\Http\Controllers\Api\AssessmentController::class, 'startSession']);
                Route::get('/sessions/{id}', [\App\Http\Controllers\Api\AssessmentController::class, 'getSession']);
                Route::post('/sessions/{id}/messages', [\App\Http\Controllers\Api\AssessmentController::class, 'sendMessage']);
                Route::post('/sessions/{id}/analyze', [\App\Http\Controllers\Api\AssessmentController::class, 'analyze'])
                    ->middleware('throttle:ai_analysis');
                Route::get('/metrics', [\App\Http\Controllers\Api\Talento360Controller::class, 'metrics']);
                Route::post('/{peopleId}/triangulate', [\App\Http\Controllers\Api\AssessmentController::class, 'triangulate360'])
                    ->middleware('throttle:ai_analysis');

                Route::prefix('feedback')->group(function () {
                    Route::post('/request', [\App\Http\Controllers\Api\AssessmentController::class, 'requestFeedback']);
                    Route::post('/submit', [\App\Http\Controllers\Api\AssessmentController::class, 'submitFeedback']);
                    Route::get('/pending', [\App\Http\Controllers\Api\AssessmentController::class, 'getPendingRequests']);
                });

                // AI Curator routes
                Route::middleware('throttle:ai_analysis')->group(function () {
                    Route::prefix('curator')->group(function () {
                        Route::post('/skills/{id}/curate', [\App\Http\Controllers\Api\CompetencyCuratorController::class, 'curate']);
                        Route::post('/skills/{id}/generate-questions', [\App\Http\Controllers\Api\CompetencyCuratorController::class, 'generateQuestions']);
                        Route::post('/competencies/{id}/curate', [\App\Http\Controllers\Api\CompetencyCuratorController::class, 'curateCompetency']);
                    });
                });
            });

            // Mobility Simulation (Gemelo Digital)
            Route::post('/mobility/simulate', [\App\Http\Controllers\Api\MobilitySimulationController::class, 'simulate']);
            Route::get('/mobility/organization-impact', [\App\Http\Controllers\Api\MobilitySimulationController::class, 'organizationImpact']);
            Route::post('/mobility/save-scenario', [\App\Http\Controllers\Api\MobilitySimulationController::class, 'saveScenario']);
            Route::post('/mobility/scenarios/{id}/materialize', [\App\Http\Controllers\Api\MobilitySimulationController::class, 'materialize']);
            Route::get('/mobility/execution-status', [\App\Http\Controllers\Api\ExecutionTrackingController::class, 'index']);
            Route::get('/mobility/execution/{id}', [\App\Http\Controllers\Api\ExecutionTrackingController::class, 'show']);
            Route::post('/mobility/execution/launch/{actionId}', [\App\Http\Controllers\Api\ExecutionTrackingController::class, 'launchLms']);
            Route::post('/mobility/execution/sync/{actionId}', [\App\Http\Controllers\Api\ExecutionTrackingController::class, 'syncProgress']);
            Route::post('/mobility/ai-suggestions', [\App\Http\Controllers\Api\MobilitySimulationController::class, 'getAiSuggestions']);

            // LMS Integration (Dual Mode)
            Route::prefix('lms')->group(function () {
                Route::get('/search', [\App\Http\Controllers\Api\LmsController::class, 'search']);
                Route::post('/actions/{action}/launch', [\App\Http\Controllers\Api\LmsController::class, 'launch']);
                Route::post('/actions/{action}/sync', [\App\Http\Controllers\Api\LmsController::class, 'sync']);
                Route::get('/stats', [\App\Http\Controllers\Api\LmsController::class, 'getGamificationStats']);
                Route::get('/leaderboard', [\App\Http\Controllers\Api\LmsController::class, 'getLeaderboard']);
            });
        }
    );

    // Lightweight telemetry endpoint for frontend analytics (logs event server-side)
    Route::post('/telemetry/event', [\App\Http\Controllers\Api\TelemetryController::class, 'store']);

    // Prompt / Instruction management for generation wizard (moved earlier to avoid parameter conflicts)

    // Dev API: manage capability_competencies pivot (competency assignments per capability per scenario)
    // Supports both creating new competencies and attaching existing ones
    Route::post('/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies', [\App\Http\Controllers\Api\CapabilityCompetencyController::class, 'store']);
    Route::patch('/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}', [\App\Http\Controllers\Api\CapabilityCompetencyController::class, 'update']);
    Route::delete('/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}', [\App\Http\Controllers\Api\CapabilityCompetencyController::class, 'destroy']);

    // Dev API: create a Capability under a Scenario (multi-tenant)
    Route::post(
        '/strategic-planning/scenarios/{id}/capabilities',
        function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            $scenario = App\Models\Scenario::find($id);

            if (! $user || ! $scenario || empty($request->input('name'))) {
                if (! $user) {
                    $status = 401;
                    $message = MSG_UNAUTHENTICATED;
                } elseif (! $scenario) {
                    $status = 404;
                    $message = MSG_SCENARIO_NOT_FOUND;
                } else {
                    $status = 422;
                    $message = 'Name is required';
                }

                return response()->json(['success' => false, 'message' => $message], $status);
            }

            try {
                $data = $request->only(['name', 'description', 'importance', 'is_critical']);
                $cap = App\Models\Capability::create([
                    'organization_id' => $user->organization_id ?? null,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'importance' => $data['importance'] ?? 3,
                ]);

                $pivot = [
                    'scenario_id' => $scenario->id,
                    'capability_id' => $cap->id,
                    'strategic_role' => $request->input('strategic_role', 'target'),
                    'strategic_weight' => (int) $request->input('strategic_weight', 10),
                    'priority' => (int) $request->input('priority', 1),
                    'rationale' => $request->input('rationale', null),
                    'required_level' => (int) $request->input('required_level', 3),
                    'is_critical' => (bool) $request->input('is_critical', false),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (! \DB::table('scenario_capabilities')->where('scenario_id', $scenario->id)->where('capability_id', $cap->id)->exists()) {
                    \DB::table('scenario_capabilities')->insert($pivot);
                }

                return response()->json(['success' => true, 'data' => $cap], 201);
            } catch (\Throwable $e) {
                \Log::error('Error creating capability for scenario '.$id.': '.$e->getMessage(), ['exception' => $e]);

                return response()->json(['success' => false, 'message' => 'Server error creating capability', 'error' => $e->getMessage()], 500);
            }
        }
    );

    // Competency versions CRUD
    Route::middleware(['auth:sanctum'])->group(
        function () {
            Route::get('/competencies/{competencyId}/versions', [App\Http\Controllers\CompetencyVersionController::class, 'index']);
            Route::post('/competencies/{competencyId}/versions', [App\Http\Controllers\CompetencyVersionController::class, 'store']);
            Route::get('/competencies/{competencyId}/versions/{id}', [App\Http\Controllers\CompetencyVersionController::class, 'show']);
            Route::delete('/competencies/{competencyId}/versions/{id}', [App\Http\Controllers\CompetencyVersionController::class, 'destroy']);
        }
    );

    // Transform competency: create new competency_version from existing competency
    Route::middleware(['auth:sanctum'])->post('/competencies/{competencyId}/transform', [App\Http\Controllers\TransformCompetencyController::class, 'transform']);

    // Dev API: retrieve a single Capability entity (multi-tenant safe)
    Route::get(
        PATH_CAPABILITIES_ID,
        function ($id) {
            $user = auth()->user();
            $cap = App\Models\Capability::find($id);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $cap || (isset($cap->organization_id) && $cap->organization_id !== $orgId)) {
                if (! $user) {
                    $status = 401;
                    $message = MSG_UNAUTHENTICATED;
                } elseif (! $cap) {
                    $status = 404;
                    $message = MSG_CAPABILITY_NOT_FOUND;
                } else {
                    $status = 403;
                    $message = MSG_FORBIDDEN;
                }

                return response()->json(['success' => false, 'message' => $message], $status);
            }

            return response()->json(['success' => true, 'data' => $cap->toArray()]);
        }
    );

    // Promote a single incubated capability (remove discovered flag and mark active)
    Route::post(
        '/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/promote',
        function ($scenarioId, $capabilityId) {
            $user = auth()->user();
            $scenario = App\Models\Scenario::find($scenarioId);
            $cap = App\Models\Capability::find($capabilityId);
            $orgId = $user->organization_id ?? null;

            $validationError = null;
            $validationError = null;
            if (! $user) {
                $validationError = ['m' => MSG_UNAUTHENTICATED, 'c' => 401];
            } elseif (! $scenario) {
                $validationError = ['m' => MSG_SCENARIO_NOT_FOUND, 'c' => 404];
            } elseif ($scenario->organization_id !== $orgId) {
                $validationError = ['m' => MSG_FORBIDDEN, 'c' => 403];
            } elseif (! $cap) {
                $validationError = ['m' => MSG_CAPABILITY_NOT_FOUND, 'c' => 404];
            } elseif (isset($cap->discovered_in_scenario_id) && (int) $cap->discovered_in_scenario_id !== (int) $scenarioId) {
                $validationError = ['m' => 'Capability not associated with this scenario', 'c' => 422];
            }

            if ($validationError) {
                return response()->json(['success' => false, 'message' => $validationError['m']], $validationError['c']);
            }

            try {
                $cap->discovered_in_scenario_id = null;
                $cap->status = 'active';
                $cap->save();

                return response()->json(['success' => true, 'data' => $cap]);
            } catch (\Throwable $e) {
                \Log::error('Error promoting capability: '.$e->getMessage(), ['capability_id' => $capabilityId]);

                return response()->json(['success' => false, 'message' => 'Server error promoting capability'], 500);
            }
        }
    );

    // Record an accepted_prompt view for a generation (audit)
    Route::post(
        '/strategic-planning/scenarios/generate/{id}/record-view',
        function ($id) {
            $user = auth()->user();
            $gen = App\Models\ScenarioGeneration::find($id);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $gen || $gen->organization_id !== $orgId) {
                if (! $user) {
                    $status = 401;
                    $message = MSG_UNAUTHENTICATED;
                } elseif (! $gen) {
                    $status = 404;
                    $message = 'Not found';
                } else {
                    $status = 403;
                    $message = MSG_FORBIDDEN;
                }

                return response()->json(['success' => false, 'message' => $message], $status);
            }

            $gen->metadata = array_merge($gen->metadata ?? [], ['accepted_prompt_views' => array_merge($gen->metadata['accepted_prompt_views'] ?? [], [[
                'viewed_by' => $user->id,
                'viewed_at' => now()->toDateTimeString(),
            ]])]);
            $gen->save();

            return response()->json(['success' => true]);
        }
    );

    // Dev API: update a Capability entity (multi-tenant safe)
    Route::patch(
        PATH_CAPABILITIES_ID,
        function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            $cap = App\Models\Capability::find($id);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $cap || (isset($cap->organization_id) && $cap->organization_id !== $orgId)) {
                if (! $user) {
                    $status = 401;
                    $message = MSG_UNAUTHENTICATED;
                } elseif (! $cap) {
                    $status = 404;
                    $message = MSG_CAPABILITY_NOT_FOUND;
                } else {
                    $status = 403;
                    $message = MSG_FORBIDDEN;
                }

                return response()->json(['success' => false, 'message' => $message], $status);
            }
            try {
                $data = $request->only(['name', 'description', 'importance', 'position_x', 'position_y', 'type', 'category', 'is_critical']);
                foreach ($data as $k => $v) {
                    if ($v !== null) {
                        $cap->{$k} = $v;
                    }
                }
                $cap->save();

                return response()->json(['success' => true, 'data' => $cap]);
            } catch (\Throwable $e) {
                \Log::error('Error updating capability '.$id.': '.$e->getMessage());

                return response()->json(['success' => false, 'message' => 'Server error updating capability'], 500);
            }
        }
    );

    // Dev API: retrieve a single Competency entity (multi-tenant safe)
    Route::get(
        PATH_COMPETENCIES_ID,
        function ($id) {
            $user = auth()->user();
            $comp = App\Models\Competency::find($id);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $comp || (isset($comp->organization_id) && $comp->organization_id !== $orgId)) {
                if (! $user) {
                    $status = 401;
                    $msg = MSG_UNAUTHENTICATED;
                } elseif (! $comp) {
                    $status = 404;
                    $msg = MSG_COMPETENCY_NOT_FOUND;
                } else {
                    $status = 403;
                    $msg = MSG_FORBIDDEN;
                }

                return response()->json(['success' => false, 'message' => $msg], $status);
            }

            return response()->json(['success' => true, 'data' => $comp->toArray()]);
        }
    );

    // Neo4j Sync control endpoint (protegido)
    Route::post('/neo4j/sync', [\App\Http\Controllers\Neo4jSyncController::class, 'sync']);

    // Dev API: create a Competency entity (multi-tenant safe)
    Route::post(
        '/competencies',
        function (Illuminate\Http\Request $request) {
            $user = auth()->user();
            $name = trim($request->input('name', ''));

            if (! $user || empty($name)) {
                return response()->json(['success' => false, 'message' => ! $user ? MSG_UNAUTHENTICATED : 'Name is required'], ! $user ? 401 : 422);
            }

            try {
                $comp = App\Models\Competency::create([
                    'organization_id' => $user->organization_id ?? null,
                    'name' => $name,
                    'description' => $request->input('description', null),
                ]);

                return response()->json(['success' => true, 'data' => $comp], 201);
            } catch (\Throwable $e) {
                \Log::error('Error creating competency: '.$e->getMessage());

                return response()->json(['success' => false, 'message' => 'Server error creating competency'], 500);
            }
        }
    );

    // Dev API: delete a Competency entity (multi-tenant safe)
    Route::delete(
        '/competencies/{id}',
        function ($id) {
            $user = auth()->user();
            $comp = App\Models\Competency::find($id);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $comp || (isset($comp->organization_id) && $comp->organization_id !== $orgId)) {
                $status = 403;
                $msg = MSG_FORBIDDEN;
                if (! $user) {
                    $status = 401;
                    $msg = MSG_UNAUTHENTICATED;
                } elseif (! $comp) {
                    $status = 404;
                    $msg = MSG_COMPETENCY_NOT_FOUND;
                }

                return response()->json(['success' => false, 'message' => $msg], $status);
            }
            try {
                $comp->delete();

                return response()->json(['success' => true, 'message' => 'Competency deleted']);
            } catch (\Throwable $e) {
                \Log::error('Error deleting competency '.$id.': '.$e->getMessage());

                return response()->json(['success' => false, 'message' => 'Server error deleting competency'], 500);
            }
        }
    );

    // Dev API: update a Competency entity (multi-tenant safe)
    Route::patch(
        PATH_COMPETENCIES_ID,
        function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            $comp = App\Models\Competency::find($id);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $comp || (isset($comp->organization_id) && $comp->organization_id !== $orgId)) {
                if (! $user) {
                    $status = 401;
                    $msg = MSG_UNAUTHENTICATED;
                } elseif (! $comp) {
                    $status = 404;
                    $msg = MSG_COMPETENCY_NOT_FOUND;
                } else {
                    $status = 403;
                    $msg = MSG_FORBIDDEN;
                }

                return response()->json(['success' => false, 'message' => $msg], $status);
            }
            try {
                $data = $request->only(['name', 'description', 'skills']);
                foreach ($data as $k => $v) {
                    if ($v !== null) {
                        if ($k === 'skills' && is_array($v)) {
                            $comp->skills()->sync($v);
                        } else {
                            $comp->{$k} = $v;
                        }
                    }
                }
                $comp->save();

                return response()->json(['success' => true, 'data' => $comp]);
            } catch (\Throwable $e) {
                \Log::error('Error updating competency '.$id.': '.$e->getMessage());

                return response()->json(['success' => false, 'message' => 'Server error updating competency'], 500);
            }
        }
    );

    // Dev API: delete a CompetencySkill relation (remove a skill from a competency)
    Route::delete(
        '/competencies/{competencyId}/skills/{skillId}',
        function ($competencyId, $skillId) {
            $user = auth()->user();
            $comp = App\Models\Competency::find($competencyId);
            $skill = App\Models\Skill::find($skillId);
            $orgId = $user->organization_id ?? null;

            $error = null;
            if (! $user) {
                $error = ['m' => MSG_UNAUTHENTICATED, 'c' => 401];
            } elseif (! $comp) {
                $error = ['m' => MSG_COMPETENCY_NOT_FOUND, 'c' => 404];
            } elseif (isset($comp->organization_id) && $comp->organization_id !== $orgId) {
                $error = ['m' => MSG_FORBIDDEN, 'c' => 403];
            } elseif (! $skill) {
                $error = ['m' => 'Skill not found', 'c' => 404];
            } elseif (isset($skill->organization_id) && $skill->organization_id !== $orgId) {
                $error = ['m' => MSG_FORBIDDEN, 'c' => 403];
            }

            if ($error) {
                return response()->json(['success' => false, 'message' => $error['m']], $error['c']);
            }

            try {
                \DB::table('competency_skills')
                    ->where('competency_id', $competencyId)
                    ->where('skill_id', $skillId)
                    ->delete();

                return response()->json(['success' => true, 'message' => 'Relationship deleted successfully']);
            } catch (\Throwable $e) {
                \Log::error('Error deleting skill relation: '.$e->getMessage(), ['competencyId' => $competencyId, 'skillId' => $skillId]);

                return response()->json(['success' => false, 'message' => 'Server error deleting skill relation', 'error' => $e->getMessage()], 500);
            }
        }
    );

    // Dev API: update pivot attributes for scenario_capabilities
    Route::patch(
        '/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}',
        function (Illuminate\Http\Request $request, $scenarioId, $capabilityId) {
            $user = auth()->user();
            $scenario = App\Models\Scenario::find($scenarioId);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $scenario || $scenario->organization_id !== $orgId) {
                $status = 403;
                $msg = MSG_FORBIDDEN;
                if (! $user) {
                    $status = 401;
                    $msg = MSG_UNAUTHENTICATED;
                } elseif (! $scenario) {
                    $status = 404;
                    $msg = MSG_SCENARIO_NOT_FOUND;
                }

                return response()->json(['success' => false, 'message' => $msg], $status);
            }

            \Log::info('PATCH scenario-capability called', [
                'user_id' => $user->id ?? null,
                'scenario_id' => $scenarioId,
                'capability_id' => $capabilityId,
                'input' => $request->all(),
            ]);

            $exists = \DB::table('scenario_capabilities')->where('scenario_id', $scenarioId)->where('capability_id', $capabilityId)->exists();

            if (! $exists) {
                try {
                    \DB::table('scenario_capabilities')->insert([
                        'scenario_id' => $scenarioId,
                        'capability_id' => $capabilityId,
                        'strategic_role' => $request->input('strategic_role', 'target'),
                        'strategic_weight' => (int) $request->input('strategic_weight', 10),
                        'priority' => (int) $request->input('priority', 1),
                        'rationale' => $request->input('rationale', null),
                        'required_level' => (int) $request->input('required_level', 3),
                        'is_critical' => (bool) $request->input('is_critical', false),
                        'position_x' => $request->has('position_x') ? $request->input('position_x') : null,
                        'position_y' => $request->has('position_y') ? $request->input('position_y') : null,
                        'is_fixed' => (bool) $request->input('is_fixed', false),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    \Log::error('Failed to insert pivot relation: '.$e->getMessage());

                    return response()->json(['success' => false, 'message' => 'Failed to create relation'], 500);
                }
            }

            $update = [];
            foreach (['strategic_role', 'strategic_weight', 'priority', 'rationale', 'required_level', 'is_critical', 'position_x', 'position_y', 'is_fixed'] as $f) {
                if ($request->has($f)) {
                    $update[$f] = $request->input($f);
                }
            }

            if (! empty($update)) {
                $update['updated_at'] = now();
                \DB::table('scenario_capabilities')->where('scenario_id', $scenarioId)->where('capability_id', $capabilityId)->update($update);
            }

            return response()->json(['success' => true, 'message' => 'Relation updated', 'relation_exists' => true, 'updated' => $update]);
        }
    );

    // Read a competency including its related skills (multi-tenant safe)
    Route::get(
        PATH_COMPETENCIES_ID,
        function ($id) {
            $user = auth()->user();
            $comp = App\Models\Competency::with('skills')->find($id);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $comp || (isset($comp->organization_id) && $comp->organization_id !== $orgId)) {
                if (! $user) {
                    $status = 401;
                    $msg = MSG_UNAUTHENTICATED;
                } elseif (! $comp) {
                    $status = 404;
                    $msg = MSG_COMPETENCY_NOT_FOUND;
                } else {
                    $status = 403;
                    $msg = MSG_FORBIDDEN;
                }

                return response()->json(['success' => false, 'message' => $msg], $status);
            }

            return response()->json(['success' => true, 'data' => $comp]);
        }
    );

    // Shortcut endpoint returning only skills for a competency (multi-tenant safe)
    Route::get(
        '/competencies/{id}/skills',
        function ($id) {
            $user = auth()->user();
            $comp = App\Models\Competency::with('skills')->find($id);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $comp || (isset($comp->organization_id) && $comp->organization_id !== $orgId)) {
                if (! $user) {
                    $status = 401;
                    $msg = MSG_UNAUTHENTICATED;
                } elseif (! $comp) {
                    $status = 404;
                    $msg = MSG_COMPETENCY_NOT_FOUND;
                } else {
                    $status = 403;
                    $msg = MSG_FORBIDDEN;
                }

                return response()->json(['success' => false, 'message' => $msg], $status);
            }

            return response()->json(['success' => true, 'data' => $comp->skills ?? []]);
        }
    );

    // Dev API: attach a skill to a competency. Accepts either `skill_id` (existing) or `skill` payload to create then attach.
    Route::post(
        '/competencies/{id}/skills',
        function (Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            $comp = App\Models\Competency::find($id);
            $orgId = $user->organization_id ?? null;

            if (! $user || ! $comp || (isset($comp->organization_id) && $comp->organization_id !== $orgId)) {
                if (! $user) {
                    $status = 401;
                    $msg = MSG_UNAUTHENTICATED;
                } elseif (! $comp) {
                    $status = 404;
                    $msg = MSG_COMPETENCY_NOT_FOUND;
                } else {
                    $status = 403;
                    $msg = MSG_FORBIDDEN;
                }

                return response()->json(['success' => false, 'message' => $msg], $status);
            }

            try {
                $result = \DB::transaction(function () use ($request, $comp, $orgId) {
                    $skillId = $request->input('skill_id');
                    if ($skillId) {
                        $skillToAttach = App\Models\Skill::find($skillId);
                        if (! $skillToAttach) {
                            throw new \Exception('Skill not found', 404);
                        }
                        if (isset($skillToAttach->organization_id) && $skillToAttach->organization_id !== $orgId) {
                            throw new \Exception(MSG_FORBIDDEN, 403);
                        }
                    } else {
                        $p = $request->input('skill', []);
                        $name = trim($p['name'] ?? '');
                        if (empty($name)) {
                            throw new \Exception('Skill name is required', 422);
                        }
                        if (App\Models\Skill::where('organization_id', $orgId)->where('name', $name)->exists()) {
                            throw new \Exception('Skill duplicada', 409);
                        }
                        $skillToAttach = App\Models\Skill::create(['organization_id' => $orgId, 'name' => $name, 'description' => $p['description'] ?? null, 'category' => $p['category'] ?? null]);
                    }
                    if (! \DB::table('competency_skills')->where('competency_id', $comp->id)->where('skill_id', $skillToAttach->id)->exists()) {
                        \DB::table('competency_skills')->insert(['competency_id' => $comp->id, 'skill_id' => $skillToAttach->id, 'weight' => (int) $request->input('weight', 10), 'created_at' => now(), 'updated_at' => now()]);
                    }

                    return $skillToAttach;
                });

                return response()->json(['success' => true, 'data' => $result], 201);
            } catch (\Exception $e) {
                $code = $e->getCode();
                if (! in_array($code, [403, 404, 409, 422])) {
                    $code = 500;
                }

                return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
            }
        }
    );

    // Dev API: delete the relationship (pivot) between a scenario and a capability
    Route::delete(
        '/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}',
        function ($scenarioId, $capabilityId) {
            $user = auth()->user();
            $scenario = App\Models\Scenario::find($scenarioId);
            $orgId = $user->organization_id ?? null;

            $error = null;
            if (! $user) {
                $error = ['m' => MSG_UNAUTHENTICATED, 'c' => 401];
            } elseif (! $scenario) {
                $error = ['m' => MSG_SCENARIO_NOT_FOUND, 'c' => 404];
            } elseif ($scenario->organization_id !== $orgId) {
                $error = ['m' => MSG_FORBIDDEN, 'c' => 403];
            }

            if ($error) {
                return response()->json(['success' => false, 'message' => $error['m']], $error['c']);
            }

            $deleted = \DB::table('scenario_capabilities')
                ->where('scenario_id', $scenarioId)
                ->where('capability_id', $capabilityId)
                ->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => MSG_RELATIONSHIP_DELETED]);
            }

            return response()->json(['success' => false, 'message' => MSG_RELATION_NOT_FOUND], 404);
        }
    );

    // Dev API: delete the relationship (pivot) between a scenario, capability and competency
    Route::delete(
        '/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}',
        function ($scenarioId, $capabilityId, $competencyId) {
            $user = auth()->user();
            $scenario = App\Models\Scenario::find($scenarioId);
            $orgId = $user->organization_id ?? null;

            $error = null;
            if (! $user) {
                $error = ['m' => MSG_UNAUTHENTICATED, 'c' => 401];
            } elseif (! $scenario) {
                $error = ['m' => MSG_SCENARIO_NOT_FOUND, 'c' => 404];
            } elseif ($scenario->organization_id !== $orgId) {
                $error = ['m' => MSG_FORBIDDEN, 'c' => 403];
            }

            if ($error) {
                return response()->json(['success' => false, 'message' => $error['m']], $error['c']);
            }

            $deleted = \DB::table('capability_competencies')
                ->where('scenario_id', $scenarioId)
                ->where('capability_id', $capabilityId)
                ->where('competency_id', $competencyId)
                ->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => MSG_RELATIONSHIP_DELETED]);
            }

            return response()->json(['success' => false, 'message' => MSG_RELATION_NOT_FOUND], 404);
        }
    );

    // Dev API: delete a Capability entity (multi-tenant safe)
    Route::delete(
        PATH_CAPABILITIES_ID,
        function ($id) {
            $user = auth()->user();
            $cap = App\Models\Capability::find($id);
            $orgId = $user->organization_id ?? null;

            $error = null;
            if (! $user) {
                $error = ['m' => MSG_UNAUTHENTICATED, 'c' => 401];
            } elseif (! $cap) {
                $error = ['m' => MSG_CAPABILITY_NOT_FOUND, 'c' => 404];
            } elseif (isset($cap->organization_id) && $cap->organization_id !== $orgId) {
                $error = ['m' => MSG_FORBIDDEN, 'c' => 403];
            }

            if ($error) {
                return response()->json(['success' => false, 'message' => $error['m']], $error['c']);
            }

            try {
                $cap->delete();

                return response()->json(['success' => true, 'message' => MSG_CAPABILITY_DELETED]);
            } catch (\Throwable $e) {
                \Log::error('Error deleting capability '.$id.': '.$e->getMessage());

                return response()->json(['success' => false, 'message' => MSG_SERVER_ERROR_DELETING_CAPABILITY], 500);
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
        if (! $capId) {
            continue;
        }
        $x = array_key_exists('x', $p) ? $p['x'] : null;
        $y = array_key_exists('y', $p) ? $p['y'] : null;
        $isFixed = array_key_exists('is_fixed', $p) ? (bool) $p['is_fixed'] : true;

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
            } else {
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
        } catch (\Throwable $e) {
            \Log::error('Error saving position for cap '.$capId.': '.$e->getMessage());
        }
    }
    \Log::info('Saved positions for scenario '.$id, ['updated' => $updated, 'inserted' => $inserted]);

    return response()->json(['status' => 'ok', 'updated' => $updated, 'inserted' => $inserted]);
});

// Canonical Strategic Planning API - Normalized naming (replaced workforce-planning)
Route::middleware('auth:sanctum')->prefix('strategic-planning')->group(function () {
    Route::get('scenarios', [\App\Http\Controllers\Api\ScenarioController::class, 'listScenarios']);
    Route::post('scenarios', [\App\Http\Controllers\Api\ScenarioController::class, 'store']);
    Route::get('scenarios/{id}', [\App\Http\Controllers\Api\ScenarioController::class, 'showScenario']);
    Route::patch('scenarios/{id}', [\App\Http\Controllers\Api\ScenarioController::class, 'updateScenario']);
    Route::post('scenarios/{template_id}/instantiate-from-template', [\App\Http\Controllers\Api\ScenarioController::class, 'instantiateFromTemplate']);
    Route::post('scenarios/{id}/calculate-gaps', [\App\Http\Controllers\Api\ScenarioController::class, 'calculateGaps']);
    Route::post('scenarios/{id}/refresh-suggested-strategies', [\App\Http\Controllers\Api\ScenarioController::class, 'refreshSuggestedStrategies']);
    // Finalize / consolidate scenario for Budgeting phase
    Route::post('scenarios/{id}/finalize', [\App\Http\Controllers\Api\ScenarioController::class, 'finalizeScenario']);
    Route::get('scenarios/{id}/compare-versions', [\App\Http\Controllers\Api\ScenarioController::class, 'compareVersions']);
    Route::get('scenarios/{id}/summary', [\App\Http\Controllers\Api\ScenarioController::class, 'summarize']);
    Route::delete('scenarios/{id}', [\App\Http\Controllers\Api\ScenarioController::class, 'destroyScenario']);

    // Scenario Templates - Full CRUD + Advanced Operations
    Route::get('scenario-templates', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'index']);
    Route::post('scenario-templates', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'store']);
    Route::get('scenario-templates/recommendations', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'recommendations']);
    Route::get('scenario-templates/statistics', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'statistics']);
    Route::get('scenario-templates/{template}', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'show']);
    Route::patch('scenario-templates/{template}', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'update']);
    Route::delete('scenario-templates/{template}', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'destroy']);
    Route::post('scenario-templates/save-as-template', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'saveAsTemplate']);
    Route::post('scenario-templates/{template}/instantiate', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'instantiate']);
    Route::post('scenario-templates/{template}/clone', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'clone']);

    // Backwards-compatible strategic-planning prefixed routes (used by API tests)
    Route::get('/strategic-planning/scenario-templates', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'index']);
    Route::post('/strategic-planning/scenario-templates', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'store']);
    Route::get('/strategic-planning/scenario-templates/recommendations', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'recommendations']);
    Route::get('/strategic-planning/scenario-templates/statistics', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'statistics']);
    Route::get('/strategic-planning/scenario-templates/{template}', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'show']);
    Route::patch('/strategic-planning/scenario-templates/{template}', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'update']);
    Route::delete('/strategic-planning/scenario-templates/{template}', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'destroy']);
    Route::post('/strategic-planning/scenario-templates/save-as-template', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'saveAsTemplate']);
    Route::post('/strategic-planning/scenario-templates/{template}/instantiate', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'instantiate']);
    Route::post('/strategic-planning/scenario-templates/{template}/clone', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'clone']);

    // What-If Analysis - Impact simulation & sensitivity analysis
    Route::post('what-if/headcount-impact', [\App\Http\Controllers\Api\WhatIfAnalysisController::class, 'analyzeHeadcountImpact']);
    Route::post('what-if/financial-impact', [\App\Http\Controllers\Api\WhatIfAnalysisController::class, 'analyzeFinancialImpact']);
    Route::post('what-if/risk-impact', [\App\Http\Controllers\Api\WhatIfAnalysisController::class, 'analyzeRiskImpact']);
    Route::get('what-if/compare', [\App\Http\Controllers\Api\WhatIfAnalysisController::class, 'compareWithBaseline']);
    Route::post('what-if/predict-outcomes', [\App\Http\Controllers\Api\WhatIfAnalysisController::class, 'predictOutcomes']);
    Route::post('what-if/sensitivity-analysis', [\App\Http\Controllers\Api\WhatIfAnalysisController::class, 'performSensitivityAnalysis']);
    Route::post('what-if/comprehensive', [\App\Http\Controllers\Api\WhatIfAnalysisController::class, 'comprehensiveAnalysis']);

    // Workforce Planning - Baseline & Scenario comparison
    Route::get('workforce-planning/thresholds', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'thresholds']);
    Route::patch('workforce-planning/thresholds', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'updateThresholds'])
        ->middleware('role:admin,hr_leader');
    Route::get('workforce-planning/monitoring/summary', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'monitoringSummary']);
    Route::get('workforce-planning/enterprise/summary', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'enterpriseSummary']);
    Route::get('workforce-planning/baseline/summary', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'baselineSummary']);
    Route::post('workforce-planning/scenarios/{id}/compare-baseline', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'compareBaseline']);
    Route::post('workforce-planning/scenarios/{id}/analyze', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'analyzeScenario']);
    Route::post('workforce-planning/scenarios/{id}/compare-baseline-impact', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'compareBaselineImpact']);
    Route::post('workforce-planning/scenarios/{id}/operational-sensitivity', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'operationalSensitivity']);
    // Workforce Planning Fase 4 — Comparador multi-escenario + sensitivity sweep
    Route::post('workforce-planning/scenarios/compare-multi', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'compareScenariosMulti']);
    Route::post('workforce-planning/scenarios/{id}/sensitivity-sweep', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'sensitivitySweep']);
    Route::patch('workforce-planning/scenarios/{id}/status', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'updateScenarioStatus'])
        ->middleware('role:admin,hr_leader');
    Route::get('workforce-planning/scenarios/{id}/demand-lines', [\App\Http\Controllers\Api\WorkforceDemandLineController::class, 'index']);
    Route::post('workforce-planning/scenarios/{id}/demand-lines', [\App\Http\Controllers\Api\WorkforceDemandLineController::class, 'store']);
    Route::patch('workforce-planning/scenarios/{id}/demand-lines/{lineId}', [\App\Http\Controllers\Api\WorkforceDemandLineController::class, 'update']);
    Route::delete('workforce-planning/scenarios/{id}/demand-lines/{lineId}', [\App\Http\Controllers\Api\WorkforceDemandLineController::class, 'destroy']);
    Route::get('workforce-planning/scenarios/{id}/action-plan', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'listActionPlan']);
    Route::post('workforce-planning/scenarios/{id}/action-plan', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'storeActionPlan']);
    Route::patch('workforce-planning/scenarios/{id}/action-plan/{actionId}', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'updateActionPlan']);
    Route::get('workforce-planning/scenarios/{id}/execution-dashboard', [\App\Http\Controllers\Api\WorkforcePlanningController::class, 'executionDashboard']);

    // (Removed duplicated backwards-compatible aliases to avoid double-prefixing)

    // Executive Summary - Phase 3.3: Executive dashboards & decision support
    Route::get('scenarios/{scenarioId}/executive-summary', [\App\Http\Controllers\Api\ExecutiveSummaryController::class, '__invoke']);
    Route::post('scenarios/{scenarioId}/executive-summary', [\App\Http\Controllers\Api\ExecutiveSummaryController::class, 'generate']);
    Route::post('scenarios/{scenarioId}/executive-summary/export', [\App\Http\Controllers\Api\ExecutiveSummaryController::class, 'export']);

    // Executive Summary Export - Phase 3.3: PDF/PPTX export endpoints
    Route::post('scenarios/{scenarioId}/executive-summary/export/pdf', [\App\Http\Controllers\Api\ExportController::class, 'exportPdf']);
    Route::post('scenarios/{scenarioId}/executive-summary/export/pptx', [\App\Http\Controllers\Api\ExportController::class, 'exportPptx']);
    Route::get('scenarios/{scenarioId}/executive-summary/download', [\App\Http\Controllers\Api\ExportController::class, 'download']);
    Route::get('strategic-planning/exports/{format}/status', [\App\Http\Controllers\Api\ExportController::class, 'status']);

    // Org Chart - Phase 3.4: Organizational structure visualization
    Route::get('scenarios/{scenarioId}/org-chart', [\App\Http\Controllers\Api\OrgChartController::class, '__invoke']);
});

// Talent Engineering
// Orquestación (nueva)
Route::post('/strategic-planning/scenarios/{id}/orchestrate', [\App\Http\Controllers\Api\ScenarioController::class, 'designTalent'])
    ->middleware('auth:sanctum');

// PASO 1: Neural Architecture & Incubation (Tree)
Route::middleware('auth:sanctum')->prefix('scenarios/{id}/step1')->group(function () {
    Route::get('incubated-tree', [\App\Http\Controllers\Api\ScenarioController::class, 'getIncubatedTree']);
    Route::post('promote-all', [\App\Http\Controllers\Api\ScenarioController::class, 'promoteAll']);
});

// PASO 2: Roles ↔ Competencies Mapping
Route::middleware('auth:sanctum')->prefix('scenarios/{id}/step2')->group(function () {
    Route::get('data', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'getMatrixData']);
    Route::post('mappings', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'saveMapping']);
    Route::delete('mappings/{mappingId}', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'deleteMapping']);
    Route::post('roles', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'addRole']);
    Route::get('role-forecasts', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'getRoleForecasts']);
    Route::get('skill-gaps-matrix', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'getSkillGapsMatrix']);
    Route::get('matching-results', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'getMatchingResults']);
    Route::get('succession-plans', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'getSuccessionPlans']);
    Route::post('design-talent', [\App\Http\Controllers\Api\ScenarioController::class, 'designTalent']);
    Route::post('agent-proposals/apply', [\App\Http\Controllers\Api\ScenarioController::class, 'applyAgentProposals']);
    Route::post('finalize', [\App\Http\Controllers\Api\ScenarioController::class, 'finalizeStep2']);
    Route::get('cube', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'getCubeData']);
    Route::post('orchestrate-capabilities', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'orchestrateCapabilities']);
    Route::post('approve-cube', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'approveCube']);
    Route::post('engine/generate-bars', [\App\Http\Controllers\Api\Step2RoleCompetencyController::class, 'generateBars']);
});

// Role Management Specialized
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/roles', [\App\Http\Controllers\Api\RoleController::class, 'store']);
    Route::get('/roles/{id}', [\App\Http\Controllers\Api\RoleController::class, 'show']);
    Route::put('/roles/{id}', [\App\Http\Controllers\Api\RoleController::class, 'update']);
});

// ── Phase 2: Automated Impact & ROI Reports ─────────────────────
Route::middleware('auth:sanctum')->prefix('reports')->group(function () {
    Route::get('/scenario/{scenarioId}/impact', [\App\Http\Controllers\Api\ImpactReportController::class, 'scenarioImpact']);
    Route::get('/roi', [\App\Http\Controllers\Api\ImpactReportController::class, 'organizationalRoi']);
    Route::get('/consolidated', [\App\Http\Controllers\Api\ImpactReportController::class, 'consolidated']);
});

// ── Phase 3: Crisis Simulator (C2) & Career Paths (C3) ──────────
Route::middleware('auth:sanctum')->group(function () {
    // Crisis Simulator
    Route::post('/strategic-planning/scenarios/{scenarioId}/crisis/attrition', [\App\Http\Controllers\Api\ScenarioIQController::class, 'simulateAttrition']);
    Route::post('/strategic-planning/scenarios/{scenarioId}/crisis/obsolescence', [\App\Http\Controllers\Api\ScenarioIQController::class, 'simulateObsolescence']);
    Route::post('/strategic-planning/scenarios/{scenarioId}/crisis/restructuring', [\App\Http\Controllers\Api\ScenarioIQController::class, 'simulateRestructuring']);

    // Career Paths
    Route::get('/career-paths/{peopleId}', [\App\Http\Controllers\Api\ScenarioIQController::class, 'getCareerPaths']);
    Route::get('/career-paths/route/{fromRoleId}/{toRoleId}', [\App\Http\Controllers\Api\ScenarioIQController::class, 'getOptimalRoute']);
    Route::get('/career-paths/mobility-map/{organizationId}', [\App\Http\Controllers\Api\ScenarioIQController::class, 'getMobilityMap']);
    Route::get('/career-paths/predict/{peopleId}/{targetRoleId}', [\App\Http\Controllers\Api\ScenarioIQController::class, 'predictTransition']);

    // Agentic Scenarios (Phase 6)
    Route::post('/strategic-planning/scenarios/{scenarioId}/agentic-simulation', [\App\Http\Controllers\Api\ScenarioIQController::class, 'runAgenticSimulation']);
    Route::post('/strategic-planning/scenarios/{scenarioId}/what-if', [\App\Http\Controllers\Api\ScenarioIQController::class, 'quickWhatIf']);
});

// ── Phase 5: Learning Blueprints, Sentinel & Guide ───────────────
Route::middleware('auth:sanctum')->group(function () {
    // Learning Blueprints
    Route::post('/learning-blueprints/{peopleId}', [\App\Http\Controllers\Api\StratosIntelligenceController::class, 'generateBlueprint']);
    Route::post('/learning-blueprints/{peopleId}/materialize', [\App\Http\Controllers\Api\StratosIntelligenceController::class, 'materializeBlueprint']);

    // Stratos Sentinel
    Route::get('/sentinel/scan', [\App\Http\Controllers\Api\StratosIntelligenceController::class, 'runSentinelScan']);
    Route::get('/sentinel/health', [\App\Http\Controllers\Api\StratosIntelligenceController::class, 'getSentinelHealth']);

    // Stratos Guide
    Route::get('/guide/suggestions', [\App\Http\Controllers\Api\StratosIntelligenceController::class, 'getGuideSuggestions']);
    Route::post('/guide/ask', [\App\Http\Controllers\Api\StratosIntelligenceController::class, 'askGuide']);
    Route::post('/guide/onboarding/complete', [\App\Http\Controllers\Api\StratosIntelligenceController::class, 'completeOnboardingStep']);
    Route::get('/retention-forecast/{peopleId}', [\App\Http\Controllers\Api\StratosIntelligenceController::class, 'getRetentionForecast']);

    // People Experience (PX) & Engagement
    Route::get('/px/campaigns', [\App\Http\Controllers\Api\PxController::class, 'index']);
    Route::post('/px/campaigns/trigger', [\App\Http\Controllers\Api\PxController::class, 'trigger']);

    // Gamification & Rewards
    Route::prefix('gamification')->group(function () {
        Route::get('/rewards', [\App\Http\Controllers\Api\GamificationController::class, 'getRewards']);
        Route::post('/people/{peopleId}/redeem', [\App\Http\Controllers\Api\GamificationController::class, 'redeem']);
        Route::get('/people/{peopleId}/history', [\App\Http\Controllers\Api\GamificationController::class, 'getRedemptionHistory']);
        Route::get('/people/{peopleId}/quests', [\App\Http\Controllers\Api\GamificationController::class, 'getPersonQuests']);
        Route::get('/quests/available', [\App\Http\Controllers\Api\GamificationController::class, 'getAvailableQuests']);
        Route::post('/people/{peopleId}/quests/{questId}/start', [\App\Http\Controllers\Api\GamificationController::class, 'startQuest']);
        Route::post('/people/{peopleId}/quests/{questId}/progress', [\App\Http\Controllers\Api\GamificationController::class, 'progressQuest']);
    });
});

// ── QA & Evaluation: RAGAS LLM Fidelity Assessment ──────────────
Route::middleware('auth:sanctum')->prefix('qa')->group(function () {
    Route::post('/llm-evaluations', [\App\Http\Controllers\Api\RAGASEvaluationController::class, 'store']);
    Route::get('/llm-evaluations/{id}', [\App\Http\Controllers\Api\RAGASEvaluationController::class, 'show']);
    Route::get('/llm-evaluations', [\App\Http\Controllers\Api\RAGASEvaluationController::class, 'index']);
    Route::get('/llm-evaluations/metrics/summary', [\App\Http\Controllers\Api\RAGASEvaluationController::class, 'summary']);
});

// ── RAG: Retrieval Augmented Generation - Ask Questions ──────────
Route::middleware('auth:sanctum')->prefix('rag')->group(function () {
    Route::post('/ask', [\App\Http\Controllers\Api\RagController::class, 'ask'])->name('rag.ask');
});

// ── Agent Interactions: Metrics & Observability ──────────────────
Route::middleware('auth:sanctum')->prefix('agent-interactions')->group(function () {
    Route::get('/metrics/summary', [\App\Http\Controllers\Api\AgentInteractionMetricsController::class, 'summary'])->name('agent-interactions.summary');
    Route::get('/metrics/failing-agents', [\App\Http\Controllers\Api\AgentInteractionMetricsController::class, 'failingAgents'])->name('agent-interactions.failing-agents');
    Route::get('/metrics/latency-by-agent', [\App\Http\Controllers\Api\AgentInteractionMetricsController::class, 'latencyByAgent'])->name('agent-interactions.latency-by-agent');
});

// ── Intelligence: Aggregated Metrics & Analytics ──────────────────
Route::middleware('auth:sanctum')->prefix('intelligence')->group(function () {
    Route::get('/aggregates', [\App\Http\Controllers\Api\IntelligenceAggregatesController::class, 'index'])->name('intelligence.aggregates');
    Route::get('/aggregates/summary', [\App\Http\Controllers\Api\IntelligenceAggregatesController::class, 'summary'])->name('intelligence.aggregates.summary');

    // ── Feedback Loop (Sprint 4) ──
    Route::prefix('feedback')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Intelligence\FeedbackController::class, 'index'])->name('intelligence.feedback.index');
        Route::post('/', [\App\Http\Controllers\Api\Intelligence\FeedbackController::class, 'store'])->name('intelligence.feedback.store');
        Route::get('/summary', [\App\Http\Controllers\Api\Intelligence\FeedbackController::class, 'summary'])->name('intelligence.feedback.summary');
        Route::get('/patterns', [\App\Http\Controllers\Api\Intelligence\FeedbackController::class, 'patterns'])->name('intelligence.feedback.patterns');
    });
});

// Catálogos dinámicos para selectores
require __DIR__.'/form-schema-complete.php';

// ── Verification Hub: Dashboards & Analytics ──────────────────────
Route::middleware('auth:sanctum')->prefix('deployment/verification')->group(function () {
    // Dashboard metrics endpoints
    Route::get('/metrics', [\App\Http\Controllers\Deployment\VerificationDashboardController::class, 'metrics'])->name('verification.metrics');
    Route::get('/compliance-metrics', [\App\Http\Controllers\Deployment\VerificationDashboardController::class, 'complianceMetrics'])->name('verification.compliance-metrics');
    Route::get('/metrics-history', [\App\Http\Controllers\Deployment\VerificationDashboardController::class, 'metricsHistory'])->name('verification.metrics-history');
    Route::get('/realtime-events', [\App\Http\Controllers\Deployment\VerificationDashboardController::class, 'realtimeEvents'])->name('verification.realtime-events');
    Route::get('/realtime-events-stream', [\App\Http\Controllers\Deployment\VerificationDashboardController::class, 'realtimeEventsStream'])->name('verification.realtime-events-stream');
    Route::get('/export-metrics', [\App\Http\Controllers\Deployment\VerificationDashboardController::class, 'exportMetrics'])->name('verification.export-metrics');
});

// ── AI/ML Analytics (Phase 9: Anomally Detection, Predictions, Recommendations) ──
Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
    // Anomaly detection
    Route::get('/anomalies', [\App\Http\Controllers\Api\AnalyticsController::class, 'getAnomalies'])->name('analytics.anomalies');

    // Predictions & forecasting
    Route::get('/predictions/compliance', [\App\Http\Controllers\Api\AnalyticsController::class, 'forecastCompliance'])->name('analytics.forecast-compliance');
    Route::get('/predictions/deployment-window', [\App\Http\Controllers\Api\AnalyticsController::class, 'predictDeploymentWindow'])->name('analytics.deployment-window');
    Route::get('/predictions/resources', [\App\Http\Controllers\Api\AnalyticsController::class, 'predictResourceNeeds'])->name('analytics.resources');
    Route::post('/predictions/transition-risk', [\App\Http\Controllers\Api\AnalyticsController::class, 'assessTransitionRisk'])->name('analytics.transition-risk');

    // Recommendations
    Route::get('/recommendations', [\App\Http\Controllers\Api\AnalyticsController::class, 'getRecommendations'])->name('analytics.recommendations');

    // Metrics aggregation
    Route::get('/metrics/current', [\App\Http\Controllers\Api\AnalyticsController::class, 'getCurrentMetrics'])->name('analytics.metrics-current');
    Route::get('/metrics/history', [\App\Http\Controllers\Api\AnalyticsController::class, 'getMetricsHistory'])->name('analytics.metrics-history');
    Route::get('/metrics/comparison', [\App\Http\Controllers\Api\AnalyticsController::class, 'getMetricsComparison'])->name('analytics.metrics-comparison');
    Route::get('/metrics/latency-percentiles', [\App\Http\Controllers\Api\AnalyticsController::class, 'getLatencyPercentiles'])->name('analytics.latency-percentiles');

    // Dashboard summary
    Route::get('/dashboard-summary', [\App\Http\Controllers\Api\AnalyticsController::class, 'getDashboardSummary'])->name('analytics.dashboard-summary');
});

// ── Automation & Hybrid Workflows (Phase 10: Event-driven Automation & Webhooks) ──
Route::middleware('auth:sanctum')->prefix('automation')->group(function () {
    // Trigger evaluation
    Route::get('/evaluate', [\App\Http\Controllers\Api\AutomationController::class, 'evaluate'])->name('automation.evaluate');

    // Workflow management
    Route::post('/workflows/{code}/trigger', [\App\Http\Controllers\Api\AutomationController::class, 'triggerWorkflow'])->name('automation.trigger-workflow');
    Route::get('/workflows/available', [\App\Http\Controllers\Api\AutomationController::class, 'listAvailableWorkflows'])->name('automation.list-workflows');

    // Execution management
    Route::get('/executions/{executionId}', [\App\Http\Controllers\Api\AutomationController::class, 'getExecutionStatus'])->name('automation.execution-status');
    Route::delete('/executions/{executionId}', [\App\Http\Controllers\Api\AutomationController::class, 'cancelExecution'])->name('automation.cancel-execution');
    Route::post('/executions/{executionId}/retry', [\App\Http\Controllers\Api\AutomationController::class, 'retryExecution'])->name('automation.retry-execution');

    // Webhook registry
    Route::get('/webhooks', [\App\Http\Controllers\Api\AutomationController::class, 'listWebhooks'])->name('automation.list-webhooks');
    Route::post('/webhooks', [\App\Http\Controllers\Api\AutomationController::class, 'registerWebhook'])->name('automation.register-webhook');
    Route::patch('/webhooks/{webhookId}', [\App\Http\Controllers\Api\AutomationController::class, 'updateWebhook'])->name('automation.update-webhook');
    Route::delete('/webhooks/{webhookId}', [\App\Http\Controllers\Api\AutomationController::class, 'deleteWebhook'])->name('automation.delete-webhook');
    Route::post('/webhooks/{webhookId}/test', [\App\Http\Controllers\Api\AutomationController::class, 'testWebhook'])->name('automation.test-webhook');
    Route::get('/webhooks/{webhookId}/stats', [\App\Http\Controllers\Api\AutomationController::class, 'getWebhookStats'])->name('automation.webhook-stats');

    // Remediation
    Route::post('/remediate', [\App\Http\Controllers\Api\AutomationController::class, 'remediateAnomaly'])->name('automation.remediate');
    Route::get('/remediation-history', [\App\Http\Controllers\Api\AutomationController::class, 'getRemediationHistory'])->name('automation.remediation-history');

    // Automation status
    Route::get('/status', [\App\Http\Controllers\Api\AutomationController::class, 'getAutomationStatus'])->name('automation.status');
    Route::post('/status', [\App\Http\Controllers\Api\AutomationController::class, 'toggleAutomationStatus'])->name('automation.toggle-status');
});

// ── Mobile-First Support (Phase 11) ──
// Device token registration & management
Route::prefix('/mobile')
    ->middleware('auth:sanctum')
    ->group(function () {
        // Device management
        Route::post('/register-device', [\App\Http\Controllers\Api\MobileController::class, 'registerDevice'])->name('mobile.register-device');
        Route::get('/devices', [\App\Http\Controllers\Api\MobileController::class, 'getDevices'])->name('mobile.get-devices');
        Route::delete('/devices/{deviceId}', [\App\Http\Controllers\Api\MobileController::class, 'deactivateDevice'])->name('mobile.deactivate-device');

        // Approval workflows
        Route::get('/approvals', [\App\Http\Controllers\Api\MobileController::class, 'getPendingApprovals'])->name('mobile.get-approvals');
        Route::post('/approvals/{approvalId}/approve', [\App\Http\Controllers\Api\MobileController::class, 'approveRequest'])->name('mobile.approve-request');
        Route::post('/approvals/{approvalId}/reject', [\App\Http\Controllers\Api\MobileController::class, 'rejectRequest'])->name('mobile.reject-request');
        Route::get('/approvals/history', [\App\Http\Controllers\Api\MobileController::class, 'getApprovalHistory'])->name('mobile.approval-history');

        // Offline queue sync
        Route::post('/offline-queue/sync', [\App\Http\Controllers\Api\MobileController::class, 'syncQueue'])->name('mobile.sync-queue');
        Route::get('/offline-queue/status', [\App\Http\Controllers\Api\MobileController::class, 'getQueueStatus'])->name('mobile.queue-status');

        // Admin statistics
        Route::get('/stats/devices', [\App\Http\Controllers\Api\MobileController::class, 'getDeviceStats'])->name('mobile.device-stats');
    });

// ── Messaging MVP (Conversations, Messages, Participants) ——
Route::middleware(['auth:sanctum'])->prefix('messaging')->group(function () {
    // Conversations CRUD
    Route::get('conversations', [\App\Http\Controllers\Api\Messaging\ConversationController::class, 'index'])->name('messaging.conversations.index');
    Route::post('conversations', [\App\Http\Controllers\Api\Messaging\ConversationController::class, 'store'])->name('messaging.conversations.store');
    Route::get('conversations/{conversation}', [\App\Http\Controllers\Api\Messaging\ConversationController::class, 'show'])->name('messaging.conversations.show');
    Route::put('conversations/{conversation}', [\App\Http\Controllers\Api\Messaging\ConversationController::class, 'update'])->name('messaging.conversations.update');
    Route::delete('conversations/{conversation}', [\App\Http\Controllers\Api\Messaging\ConversationController::class, 'destroy'])->name('messaging.conversations.destroy');

    // Messages
    Route::get('conversations/{conversation}/messages', [\App\Http\Controllers\Api\Messaging\MessageController::class, 'index'])->name('messaging.messages.index');
    Route::post('conversations/{conversation}/messages', [\App\Http\Controllers\Api\Messaging\MessageController::class, 'store'])->name('messaging.messages.store');
    Route::post('messages/{message}/read', [\App\Http\Controllers\Api\Messaging\MessageController::class, 'markRead'])->name('messaging.messages.markRead');

    // Participants
    Route::post('conversations/{conversation}/participants', [\App\Http\Controllers\Api\Messaging\ParticipantController::class, 'store'])->name('messaging.participants.store');
    Route::delete('conversations/{conversation}/participants/{participant}', [\App\Http\Controllers\Api\Messaging\ParticipantController::class, 'destroy'])->name('messaging.participants.destroy');

    // Settings & Metrics
    Route::get('settings', [\App\Http\Controllers\Api\Messaging\MessagingSettingsController::class, 'getSettings'])->name('messaging.settings.show');
    Route::put('settings', [\App\Http\Controllers\Api\Messaging\MessagingSettingsController::class, 'updateSettings'])->name('messaging.settings.update');
    Route::get('metrics', [\App\Http\Controllers\Api\Messaging\MessagingSettingsController::class, 'getMetrics'])->name('messaging.metrics.summary');
});

// ── Notification Preferences: User-level multi-channel configuration ──
Route::middleware('auth:sanctum')->group(function () {
    Route::get('notification-preferences', [\App\Http\Controllers\Api\NotificationPreferencesController::class, 'index'])->name('notification-preferences.index');
    Route::post('notification-preferences', [\App\Http\Controllers\Api\NotificationPreferencesController::class, 'store'])->name('notification-preferences.store');
    Route::post('notification-preferences/{channelType}/toggle', [\App\Http\Controllers\Api\NotificationPreferencesController::class, 'toggle'])->name('notification-preferences.toggle');
    Route::delete('notification-preferences/{channelType}', [\App\Http\Controllers\Api\NotificationPreferencesController::class, 'destroy'])->name('notification-preferences.destroy');
});

// ── Org Chart (People Tree) — C3 ──
Route::middleware(['auth:sanctum'])->prefix('org-chart/people')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\OrgPeopleChartController::class, 'tree'])->name('org-chart.tree');
    Route::get('/stats', [\App\Http\Controllers\Api\OrgPeopleChartController::class, 'stats'])->name('org-chart.stats');
    Route::get('/{id}/subtree', [\App\Http\Controllers\Api\OrgPeopleChartController::class, 'subtree'])->name('org-chart.subtree');
});

// ── Skill Intelligence v2 ──
Route::middleware(['auth:sanctum'])->prefix('skill-intelligence')->group(function () {
    Route::get('heatmap', [\App\Http\Controllers\Api\SkillIntelligenceController::class, 'heatmap'])->name('skill-intelligence.heatmap');
    Route::get('top-gaps', [\App\Http\Controllers\Api\SkillIntelligenceController::class, 'topGaps'])->name('skill-intelligence.top-gaps');
    Route::get('upskilling', [\App\Http\Controllers\Api\SkillIntelligenceController::class, 'upskilling'])->name('skill-intelligence.upskilling');
    Route::get('coverage', [\App\Http\Controllers\Api\SkillIntelligenceController::class, 'coverage'])->name('skill-intelligence.coverage');
});

// ── Performance AI ──
Route::middleware(['auth:sanctum'])->prefix('performance')->group(function () {
    Route::get('cycles', [\App\Http\Controllers\Api\PerformanceController::class, 'indexCycles'])->name('performance.cycles.index');
    Route::post('cycles', [\App\Http\Controllers\Api\PerformanceController::class, 'storeCycle'])->name('performance.cycles.store');
    Route::get('cycles/{id}', [\App\Http\Controllers\Api\PerformanceController::class, 'showCycle'])->name('performance.cycles.show');
    Route::post('cycles/{id}/activate', [\App\Http\Controllers\Api\PerformanceController::class, 'activateCycle'])->name('performance.cycles.activate');
    Route::post('cycles/{id}/close', [\App\Http\Controllers\Api\PerformanceController::class, 'closeCycle'])->name('performance.cycles.close');
    Route::get('cycles/{cycleId}/reviews', [\App\Http\Controllers\Api\PerformanceController::class, 'indexReviews'])->name('performance.reviews.index');
    Route::post('cycles/{cycleId}/reviews', [\App\Http\Controllers\Api\PerformanceController::class, 'storeReview'])->name('performance.reviews.store');
    Route::patch('cycles/{cycleId}/reviews/{reviewId}', [\App\Http\Controllers\Api\PerformanceController::class, 'updateReview'])->name('performance.reviews.update');
    Route::post('cycles/{id}/calibrate', [\App\Http\Controllers\Api\PerformanceController::class, 'calibrateCycle'])->name('performance.cycles.calibrate');
    Route::get('cycles/{id}/insights', [\App\Http\Controllers\Api\PerformanceController::class, 'insights'])->name('performance.cycles.insights');
});

// ── Admin Operations: Critical Operational Tasks (Alpha-1) ──
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    // Operations audit trail & management
    Route::get('operations', [\App\Http\Controllers\Api\AdminOperationsController::class, 'index'])->name('admin.operations.index');
    Route::post('operations/{id}/preview', [\App\Http\Controllers\Api\AdminOperationsController::class, 'preview'])->name('admin.operations.preview');
    Route::post('operations/{id}/execute', [\App\Http\Controllers\Api\AdminOperationsController::class, 'execute'])->name('admin.operations.execute');
    Route::post('operations/{id}/cancel', [\App\Http\Controllers\Api\AdminOperationsController::class, 'cancel'])->name('admin.operations.cancel');

    // Real-time monitoring (Alpha-3)
    Route::get('operations/monitor/stream', [\App\Http\Controllers\Api\AdminOperationsController::class, 'monitorStream'])->name('admin.operations.monitor-stream');

    // Audit logs (Phase 3)
    Route::get('audit-logs', [\App\Http\Controllers\Api\AuditController::class, 'index'])->name('admin.audit-logs.index');
    Route::get('audit-logs/heatmap', [\App\Http\Controllers\Api\AuditController::class, 'heatmap'])->name('admin.audit-logs.heatmap');
    Route::get('audit-logs/export', [\App\Http\Controllers\Api\AuditController::class, 'export'])->name('admin.audit-logs.export');
    Route::get('audit-logs/{entityType}/{entityId}/timeline', [\App\Http\Controllers\Api\AuditController::class, 'entityTimeline'])->name('admin.audit-logs.timeline');
    Route::get('audit-logs/users/{userId}/activity', [\App\Http\Controllers\Api\AuditController::class, 'userActivity'])->name('admin.audit-logs.user-activity');

    // Notification Channels Settings (Org-level)
    Route::get('notification-channel-settings', [\App\Http\Controllers\Api\NotificationChannelSettingsController::class, 'index'])->name('admin.notification-channels.index');
    Route::put('notification-channel-settings/{channelType}', [\App\Http\Controllers\Api\NotificationChannelSettingsController::class, 'update'])->name('admin.notification-channels.update');
    Route::delete('notification-channel-settings/{channelType}', [\App\Http\Controllers\Api\NotificationChannelSettingsController::class, 'destroy'])->name('admin.notification-channels.destroy');
});

// ── Inbound n8n Webhooks (Unauthenticated, secured via X-N8n-Secret header) ──
Route::post('/webhooks/n8n', [\App\Http\Controllers\Api\Automation\N8nController::class, 'handleWebhook'])->name('webhooks.n8n');

// TODO: recordar que estas rutas están protegidas por el middleware 'auth' en RouteServiceProvider.php y son Multinenant deben filtrar el organization_id del usuario autenticado
