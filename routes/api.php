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
Route::get('/approvals/{token}', [\App\Http\Controllers\Api\RoleDesignerController::class, 'showApprovalRequest']);
Route::post('/approvals/{token}/approve', [\App\Http\Controllers\Api\RoleDesignerController::class, 'approve']);

// Career Portal (Stratos Magnet - Public)
Route::get('/career/{tenantSlug}', [\App\Http\Controllers\Api\PublicJobController::class, 'index']);
Route::get('/career/{tenantSlug}/jobs/{jobSlug}', [\App\Http\Controllers\Api\PublicJobController::class, 'show']);
Route::post('/career/{tenantSlug}/jobs/{jobSlug}/apply', [\App\Http\Controllers\Api\PublicJobController::class, 'apply']);

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

    // Gamification (Fase 6 / D4)
    Route::get('/gamification/quests', [\App\Http\Controllers\Api\GamificationController::class, 'getAvailableQuests']);
    Route::get('/gamification/people/{peopleId}/quests', [\App\Http\Controllers\Api\GamificationController::class, 'getPersonQuests']);
    Route::post('/gamification/people/{peopleId}/quests/{questId}/start', [\App\Http\Controllers\Api\GamificationController::class, 'startQuest']);
    Route::post('/gamification/people/{peopleId}/quests/{questId}/progress', [\App\Http\Controllers\Api\GamificationController::class, 'progressQuest']);

    // Quality & Continuous Improvement (Tickets)
    Route::get('/support-tickets/metrics', [\App\Http\Controllers\Api\SupportTicketController::class, 'metrics']);
    Route::apiResource('support-tickets', \App\Http\Controllers\Api\SupportTicketController::class);

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

    // Social Learning & Mentorship Knowledge Transfer
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
    Route::get('scenario-templates', [\App\Http\Controllers\Api\ScenarioTemplateController::class, 'index']);
    Route::delete('scenarios/{id}', [\App\Http\Controllers\Api\ScenarioController::class, 'destroyScenario']);
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

// Catálogos dinámicos para selectores
require __DIR__.'/form-schema-complete.php';

// ── Automation & Hybrid Workflows (n8n) ──────────────────────────
Route::prefix('automation')->group(function () {
    Route::post('/webhooks/n8n', [\App\Http\Controllers\Api\Automation\N8nController::class, 'handleWebhook']);
});

// TODO: recordar que estas rutas están protegidas por el middleware 'auth' en RouteServiceProvider.php y son Multinenant deben filtrar el organization_id del usuario autenticado
