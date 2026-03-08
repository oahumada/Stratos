<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SentinelMonitorService;
use App\Services\StratosGuideService;
use App\Services\Talent\LearningBlueprintService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StratosIntelligenceController extends Controller
{
    use ApiResponses;

    public function __construct(
        protected LearningBlueprintService $blueprintService,
        protected SentinelMonitorService $sentinel,
        protected StratosGuideService $guide,
        protected \App\Services\Intelligence\RetentionDeepPredictorService $retentionService,
        protected \App\Services\Intelligence\NudgeOrchestratorService $nudgeOrchestrator
    ) {}

    /**
     * GET /api/intelligence/retention-forecast/{peopleId}
     */
    public function getRetentionForecast(int $peopleId): JsonResponse
    {
        try {
            $forecast = $this->retentionService->predict($peopleId);

            return $this->success($forecast, 'Pronóstico de retención generado.');
        } catch (\Exception $e) {
            return $this->error('Error: '.$e->getMessage(), 500);
        }
    }

    // ── Learning Blueprints ──────────────────────────────────

    /**
     * POST /api/learning-blueprints/{peopleId}
     */
    public function generateBlueprint(int $peopleId, Request $request): JsonResponse
    {
        $targetRoleId = $request->input('target_role_id');

        try {
            $blueprint = $this->blueprintService->generateBlueprint($peopleId, $targetRoleId);

            return $this->success($blueprint, 'Learning Blueprint generado.');
        } catch (\Exception $e) {
            return $this->error('Error al generar blueprint: '.$e->getMessage(), 500);
        }
    }

    /**
     * POST /api/learning-blueprints/{peopleId}/materialize
     */
    public function materializeBlueprint(int $peopleId, Request $request): JsonResponse
    {
        $blueprint = $request->input('blueprint', []);

        try {
            $path = $this->blueprintService->materializeBlueprint($peopleId, $blueprint);

            return $this->success($path->toArray(), 'Blueprint materializado como DevelopmentPath.');
        } catch (\Exception $e) {
            return $this->error('Error al materializar blueprint: '.$e->getMessage(), 500);
        }
    }

    // ── Stratos Sentinel ─────────────────────────────────────

    /**
     * GET /api/sentinel/scan
     */
    public function runSentinelScan(): JsonResponse
    {
        try {
            $results = $this->sentinel->runFullScan();

            return $this->success($results, 'Sentinel scan completado.');
        } catch (\Exception $e) {
            return $this->error('Error en Sentinel scan: '.$e->getMessage(), 500);
        }
    }

    /**
     * GET /api/sentinel/health
     */
    public function getSentinelHealth(): JsonResponse
    {
        try {
            $lastScan = $this->sentinel->getLastScan();
            $quickHealth = $this->sentinel->getQuickHealthScore();

            return $this->success([
                'health_score' => $quickHealth,
                'last_scan' => $lastScan,
            ], 'Health score obtenido.');
        } catch (\Exception $e) {
            return $this->error('Error: '.$e->getMessage(), 500);
        }
    }

    // ── Stratos Guide ────────────────────────────────────────

    /**
     * GET /api/guide/suggestions
     */
    public function getGuideSuggestions(Request $request): JsonResponse
    {
        $module = $request->input('module', 'dashboard');
        $userId = $request->user()?->id ?? auth()->id() ?? 0;

        try {
            $suggestions = $this->guide->getContextualSuggestions($module, $userId);

            return $this->success($suggestions, 'Sugerencias contextuales obtenidas.');
        } catch (\Exception $e) {
            return $this->error('Error: '.$e->getMessage(), 500);
        }
    }

    /**
     * POST /api/guide/ask
     */
    public function askGuide(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'module' => 'required|string',
        ]);

        $userId = $request->user()?->id ?? auth()->id() ?? 0;

        try {
            $answer = $this->guide->askGuide($validated['question'], $validated['module'], $userId);

            return $this->success($answer, 'Respuesta del Guide obtenida.');
        } catch (\Exception $e) {
            return $this->error('Error: '.$e->getMessage(), 500);
        }
    }

    /**
     * POST /api/guide/onboarding/complete
     */
    public function completeOnboardingStep(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'module' => 'required|string',
            'step' => 'required|string',
        ]);

        $userId = $request->user()?->id ?? auth()->id() ?? 0;

        try {
            $this->guide->completeOnboardingStep($userId, $validated['module'], $validated['step']);

            return $this->success(null, 'Step de onboarding completado.');
        } catch (\Exception $e) {
            return $this->error('Error: '.$e->getMessage(), 500);
        }
    }
    /**
     * POST /api/intelligence/nudges/run
     */
    public function runNudges(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id ?? $request->input('organization_id');

        if (! $orgId) {
            return $this->error('Falta ID de organización.', 400);
        }

        try {
            $result = $this->nudgeOrchestrator->orchestrate($orgId);

            return $this->success($result, 'Ciclo de Nudging proactivo completado.');
        } catch (\Exception $e) {
            return $this->error('Error al ejecutar nudges: '.$e->getMessage(), 500);
        }
    }
}
