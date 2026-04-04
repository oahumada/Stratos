<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AnalyzeWorkforceOperationalSensitivityRequest;
use App\Http\Requests\CompareWorkforceBaselineImpactRequest;
use App\Http\Requests\StoreWorkforceActionPlanRequest;
use App\Http\Requests\UpdateWorkforceActionPlanRequest;
use App\Http\Requests\UpdateWorkforceScenarioStatusRequest;
use App\Http\Requests\UpdateWorkforceThresholdsRequest;
use App\Models\AuditLog;
use App\Models\IntelligenceMetricAggregate;
use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceActionPlan;
use App\Models\WorkforceDemandLine;
use App\Services\WorkforcePlanningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkforcePlanningController extends Controller
{
    private const WORKFORCE_ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id para Workforce Planning';

    private const SCENARIO_NOT_FOUND = 'Scenario not found';

    private const SCENARIO_STATUS_LOCKED_MESSAGE = 'El escenario no permite modificaciones en su estado actual';

    private const SCENARIO_STATUS_TRANSITION_INVALID_MESSAGE = 'Transición de estado no permitida para Workforce Planning';

    public function __construct(private WorkforcePlanningService $workforcePlanningService) {}

    public function thresholds(): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        return $this->successResponse($this->workforcePlanningService->getThresholds($organizationId));
    }

    public function updateThresholds(UpdateWorkforceThresholdsRequest $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $organization = Organization::query()->find($organizationId);
        if (! $organization) {
            return $this->notFoundResponse('Organization not found');
        }

        $previousThresholds = $this->workforcePlanningService->getThresholds($organizationId);

        $organization->workforce_thresholds = $request->validated();
        $organization->save();

        $updatedThresholds = $this->workforcePlanningService->getThresholds($organizationId);

        if ($previousThresholds !== $updatedThresholds) {
            try {
                AuditLog::query()->create([
                    'organization_id' => $organizationId,
                    'user_id' => $user->id,
                    'action' => 'updated',
                    'entity_type' => 'WorkforceThresholds',
                    'entity_id' => (string) $organizationId,
                    'changes' => [
                        'workforce_thresholds' => [$previousThresholds, $updatedThresholds],
                    ],
                    'metadata' => [
                        'context' => 'workforce_planning.thresholds',
                        'changed_at' => now()->toDateTimeString(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                    'triggered_by' => 'api',
                ]);
            } catch (\Throwable $exception) {
                Log::warning('Failed to create workforce thresholds audit log', [
                    'organization_id' => $organizationId,
                    'user_id' => $user->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $this->successResponse(
            $updatedThresholds,
            'Workforce thresholds updated successfully'
        );
    }

    public function monitoringSummary(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $periodDays = max(1, min((int) $request->integer('period_days', 7), 90));
        $windowStart = now()->subDays($periodDays);

        $scenarios = Scenario::query()->where('organization_id', $organizationId);
        $demandLines = WorkforceDemandLine::query()->where('organization_id', $organizationId);
        $actionPlans = WorkforceActionPlan::query()->where('organization_id', $organizationId);

        $thresholdUpdates = AuditLog::query()
            ->where('organization_id', $organizationId)
            ->where('entity_type', 'WorkforceThresholds')
            ->where('action', 'updated')
            ->where('created_at', '>=', $windowStart)
            ->count();

        $workforceWrites = AuditLog::query()
            ->where('organization_id', $organizationId)
            ->whereIn('entity_type', ['WorkforceThresholds', 'WorkforceDemandLine', 'WorkforceActionPlan'])
            ->where('created_at', '>=', $windowStart)
            ->count();

        $workforceTelemetry = IntelligenceMetricAggregate::query()
            ->where('organization_id', $organizationId)
            ->where('date_key', '>=', $windowStart->toDateString())
            ->where(function ($query): void {
                $query->where('source_type', 'like', 'workforce%');
            });

        $avgLatencyMs = (clone $workforceTelemetry)->avg('avg_duration_ms');
        $p95LatencyMs = (clone $workforceTelemetry)->avg('p95_duration_ms');
        $avgSuccessRate = (clone $workforceTelemetry)->avg('success_rate');

        $latencyAvailable = $avgLatencyMs !== null;
        $errorRateAvailable = $avgSuccessRate !== null;

        return $this->successResponse([
            'window_days' => $periodDays,
            'window_start' => $windowStart->toDateString(),
            'window_end' => now()->toDateString(),
            'usage_by_tenant' => [
                'organization_id' => $organizationId,
                'scenarios_total' => (clone $scenarios)->count(),
                'scenarios_by_status' => (clone $scenarios)
                    ->selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status'),
                'demand_lines_total' => (clone $demandLines)->count(),
                'action_plans_total' => (clone $actionPlans)->count(),
                'action_plans_in_progress' => (clone $actionPlans)->where('status', 'in_progress')->count(),
                'action_plans_completed' => (clone $actionPlans)->where('status', 'completed')->count(),
                'threshold_updates_in_window' => $thresholdUpdates,
                'workforce_writes_in_window' => $workforceWrites,
            ],
            'operational_health' => [
                'latency_ms' => [
                    'available' => $latencyAvailable,
                    'avg' => $latencyAvailable ? round((float) $avgLatencyMs, 2) : null,
                    'p95' => $latencyAvailable ? round((float) $p95LatencyMs, 2) : null,
                    'source' => 'intelligence_metric_aggregates',
                ],
                'error_rate_pct' => [
                    'available' => $errorRateAvailable,
                    'value' => $errorRateAvailable ? round((1 - (float) $avgSuccessRate) * 100, 2) : null,
                    'source' => 'intelligence_metric_aggregates',
                ],
                'api_status_code_breakdown' => [
                    'available' => false,
                    'reason' => 'No request-level status telemetry configured for workforce endpoints',
                ],
            ],
        ], 'Workforce monitoring summary fetched successfully');
    }

    public function enterpriseSummary(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $periodDays = max(1, min((int) $request->integer('period_days', 30), 90));
        $summary = $this->workforcePlanningService->getEnterprisePlanningSummary($organizationId, $periodDays);

        return $this->successResponse($summary, 'Workforce enterprise planning summary fetched successfully');
    }

    public function baselineSummary(): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $summary = $this->workforcePlanningService->getBaselineSummary($organizationId);

        return $this->successResponse($summary);
    }

    public function compareBaseline(int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        $comparison = $this->workforcePlanningService->compareScenarioWithBaseline($scenario, $organizationId);

        return $this->successResponse($comparison);
    }

    public function analyzeScenario(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $validated = $request->validate([
            'planning_context' => 'nullable|in:baseline,scenario',
        ]);

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        $planningContext = (string) ($validated['planning_context'] ?? 'scenario');

        $analysis = $this->workforcePlanningService->analyzeWithContext(
            $scenario,
            $organizationId,
            $planningContext
        );

        return $this->successResponse($analysis);
    }

    public function compareBaselineImpact(CompareWorkforceBaselineImpactRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        $comparison = $this->workforcePlanningService->compareScenarioImpactWithBaseline(
            $scenario,
            $organizationId,
            (array) $request->validated('impact_parameters', [])
        );

        return $this->successResponse($comparison);
    }

    public function operationalSensitivity(AnalyzeWorkforceOperationalSensitivityRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        $analysis = $this->workforcePlanningService->analyzeOperationalSensitivity(
            $scenario,
            (array) $request->validated('adjustments')
        );

        return $this->successResponse($analysis);
    }

    public function listActionPlan(int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        $actions = WorkforceActionPlan::query()
            ->where('organization_id', $organizationId)
            ->where('scenario_id', $scenario->id)
            ->with(['owner:id,name,email'])
            ->orderBy('due_date')
            ->orderByDesc('priority')
            ->get();

        return $this->successResponse([
            'scenario_id' => $scenario->id,
            'actions' => $actions,
            'total' => $actions->count(),
        ]);
    }

    public function updateScenarioStatus(UpdateWorkforceScenarioStatusRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        $targetStatus = (string) $request->validated('status');
        if (! $scenario->canTransitionWorkforceStatusTo($targetStatus)) {
            return $this->errorResponse(self::SCENARIO_STATUS_TRANSITION_INVALID_MESSAGE, 409, [
                'current_status' => $scenario->status,
                'target_status' => $targetStatus,
                'allowed_transitions' => Scenario::WORKFORCE_STATUS_TRANSITIONS[(string) $scenario->status] ?? [],
            ]);
        }

        $previousStatus = (string) $scenario->status;
        $scenario->status = $targetStatus;
        $scenario->save();

        return $this->successResponse([
            'scenario_id' => $scenario->id,
            'previous_status' => $previousStatus,
            'status' => $scenario->status,
        ], 'Estado del escenario actualizado correctamente');
    }

    public function storeActionPlan(StoreWorkforceActionPlanRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        if (! $scenario->canMutateWorkforceExecutionData()) {
            return $this->errorResponse(self::SCENARIO_STATUS_LOCKED_MESSAGE, 409, [
                'scenario_status' => $scenario->status,
            ]);
        }

        $payload = $request->validated();

        $action = WorkforceActionPlan::query()->create([
            'scenario_id' => $scenario->id,
            'organization_id' => $organizationId,
            'action_title' => $payload['action_title'],
            'description' => $payload['description'] ?? null,
            'owner_user_id' => $payload['owner_user_id'] ?? null,
            'status' => $payload['status'] ?? 'planned',
            'priority' => $payload['priority'] ?? 'medium',
            'due_date' => $payload['due_date'] ?? null,
            'progress_pct'        => $payload['progress_pct'] ?? 0,
            'budget'              => $payload['budget'] ?? null,
            'actual_cost'         => $payload['actual_cost'] ?? null,
            'unit_name'           => $payload['unit_name'] ?? null,
            'lever'               => $payload['lever'] ?? null,
            'hybrid_coverage_pct' => $payload['hybrid_coverage_pct'] ?? 0,
        ])->load('owner:id,name,email');

        return $this->successResponse([
            'scenario_id' => $scenario->id,
            'action' => $action,
        ], 'Acción de Workforce creada correctamente', 201);
    }

    public function updateActionPlan(UpdateWorkforceActionPlanRequest $request, int $id, int $actionId): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        if (! $scenario->canMutateWorkforceExecutionData()) {
            return $this->errorResponse(self::SCENARIO_STATUS_LOCKED_MESSAGE, 409, [
                'scenario_status' => $scenario->status,
            ]);
        }

        $action = WorkforceActionPlan::query()
            ->where('organization_id', $organizationId)
            ->where('scenario_id', $scenario->id)
            ->find($actionId);

        if (! $action) {
            return $this->notFoundResponse('Action plan not found');
        }

        $action->fill($request->validated());
        $action->save();

        return $this->successResponse([
            'scenario_id' => $scenario->id,
            'action' => $action->load('owner:id,name,email'),
        ], 'Acción de Workforce actualizada correctamente');
    }

    public function executionDashboard(int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::WORKFORCE_ORG_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        $actions = WorkforceActionPlan::query()
            ->where('organization_id', $organizationId)
            ->where('scenario_id', $scenario->id);

        $total = (clone $actions)->count();
        $completed = (clone $actions)->where('status', 'completed')->count();
        $inProgress = (clone $actions)->where('status', 'in_progress')->count();
        $blocked = (clone $actions)->where('status', 'blocked')->count();
        $avgProgress = round((float) ((clone $actions)->avg('progress_pct') ?? 0), 2);

        $overdue = (clone $actions)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        // Budget summary
        $totalBudget   = (float) ((clone $actions)->sum('budget') ?? 0);
        $totalActual   = (float) ((clone $actions)->sum('actual_cost') ?? 0);
        $budgetVariance = $totalBudget > 0 ? round((($totalActual - $totalBudget) / $totalBudget) * 100, 2) : 0;

        // Hybrid talent coverage (avg of hybrid_coverage_pct across all actions)
        $hybridCoverage = round((float) ((clone $actions)->avg('hybrid_coverage_pct') ?? 0), 1);

        // Risk semaphore: rojo/amarillo/verde
        $riskSemaphore = $this->calculateRiskSemaphore($total, $overdue, $blocked, $avgProgress);

        // Alerts list
        $alerts = $this->buildExecutionAlerts($overdue, $blocked, $budgetVariance, $avgProgress, $hybridCoverage);

        // Breakdown by lever
        $byLever = (clone $actions)
            ->selectRaw('lever, count(*) as total, avg(progress_pct) as avg_progress')
            ->whereNotNull('lever')
            ->groupBy('lever')
            ->get()
            ->keyBy('lever');

        // Breakdown by unit
        $byUnit = (clone $actions)
            ->selectRaw('unit_name, count(*) as total, avg(progress_pct) as avg_progress, sum(budget) as budget')
            ->whereNotNull('unit_name')
            ->groupBy('unit_name')
            ->get();

        return $this->successResponse([
            'scenario_id' => $scenario->id,
            'summary' => [
                'total_actions'      => $total,
                'completed_actions'  => $completed,
                'in_progress_actions' => $inProgress,
                'blocked_actions'    => $blocked,
                'overdue_actions'    => $overdue,
                'avg_progress_pct'   => $avgProgress,
            ],
            'budget' => [
                'total_budget'    => $totalBudget,
                'total_actual'    => $totalActual,
                'variance_pct'    => $budgetVariance,
                'over_budget'     => $budgetVariance > 0,
            ],
            'hybrid_coverage_pct' => $hybridCoverage,
            'risk_semaphore'      => $riskSemaphore,
            'alerts'              => $alerts,
            'by_lever'            => $byLever,
            'by_unit'             => $byUnit,
        ]);
    }

    /**
     * Calcula el semáforo de riesgo (verde/amarillo/rojo) para el dashboard de ejecución.
     */
    private function calculateRiskSemaphore(int $total, int $overdue, int $blocked, float $avgProgress): string
    {
        if ($total === 0) {
            return 'verde';
        }

        $overduePct  = ($overdue  / $total) * 100;
        $blockedPct  = ($blocked  / $total) * 100;

        if ($overduePct >= 30 || $blockedPct >= 25 || $avgProgress < 20) {
            return 'rojo';
        }

        if ($overduePct >= 15 || $blockedPct >= 10 || $avgProgress < 50) {
            return 'amarillo';
        }

        return 'verde';
    }

    /**
     * Construye la lista de alertas de ejecución para el dashboard.
     *
     * @return array<int, array{type: string, severity: string, message: string}>
     */
    private function buildExecutionAlerts(int $overdue, int $blocked, float $budgetVariancePct, float $avgProgress, float $hybridCoverage): array
    {
        $alerts = [];

        if ($overdue > 0) {
            $alerts[] = [
                'type'     => 'overdue',
                'severity' => $overdue >= 3 ? 'high' : 'medium',
                'message'  => "{$overdue} acción(es) vencida(s) sin completar. Revisar fechas límite.",
            ];
        }

        if ($blocked > 0) {
            $alerts[] = [
                'type'     => 'blocked',
                'severity' => 'high',
                'message'  => "{$blocked} acción(es) bloqueada(s). Escalar para desbloquear.",
            ];
        }

        if ($budgetVariancePct > 10) {
            $alerts[] = [
                'type'     => 'budget',
                'severity' => $budgetVariancePct > 25 ? 'high' : 'medium',
                'message'  => "Costo real supera el presupuesto en {$budgetVariancePct}%. Revisar asignación.",
            ];
        }

        if ($avgProgress < 30) {
            $alerts[] = [
                'type'     => 'progress',
                'severity' => 'medium',
                'message'  => "Progreso promedio del plan es bajo ({$avgProgress}%). Verificar bloqueos.",
            ];
        }

        if ($hybridCoverage >= 40) {
            $alerts[] = [
                'type'     => 'hybrid_talent',
                'severity' => 'info',
                'message'  => "{$hybridCoverage}% de la capacidad cubierta por talento híbrido (humano+IA). Monitorear calidad.",
            ];
        }

        return $alerts;
    }
}
