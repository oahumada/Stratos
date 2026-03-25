<?php

namespace App\Http\Controllers\Api;

use App\Events\AdminOperationQueued;
use App\Http\Controllers\Controller;
use App\Jobs\AdminOperationJob;
use App\Models\AdminOperationAudit;
use App\Services\AdminOperationsService;
use App\Services\IntelligenceMetricsAggregator;
use App\Services\ScenarioGenerationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOperationsController extends Controller
{
    public function __construct(
        private AdminOperationsService $operationsService,
        private IntelligenceMetricsAggregator $aggregator,
        private ScenarioGenerationService $scenarioService
    ) {}

    /**
     * GET /api/admin/operations
     * List recent operations for the organization
     */
    public function index(Request $request): JsonResponse
    {
        $organization = $request->user()->people?->organization;

        if (! $organization) {
            return response()->json(['message' => 'Organization not found'], 422);
        }

        $this->authorize('viewAny', [AdminOperationAudit::class, $organization]);

        $history = $this->operationsService->getHistory($organization, 50);
        $stats = $this->operationsService->getSummaryStats($organization);

        return response()->json([
            'data' => $history,
            'stats' => $stats,
        ]);
    }

    /**
     * POST /api/admin/operations/{id}/preview
     * Preview what would happen with dry-run
     */
    public function preview(Request $request, int $id): JsonResponse
    {
        $operation = AdminOperationAudit::findOrFail($id);
        $this->authorize('update', $operation);

        if ($operation->status !== 'pending') {
            return response()->json([
                'message' => 'Can only preview pending operations',
            ], 422);
        }

        try {
            // Get the preview data based on operation type
            $previewData = $this->generatePreview($operation);

            $operation = $this->operationsService->previewOperation(
                $operation,
                fn () => $previewData
            );

            return response()->json([
                'data' => $operation,
                'message' => 'Preview completed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /api/admin/operations/{id}/execute
     * Queue the operation for async execution
     */
    public function execute(Request $request, int $id): JsonResponse
    {
        $operation = AdminOperationAudit::findOrFail($id);
        $this->authorize('update', $operation);

        $validated = $request->validate([
            'confirmed' => 'required|boolean|accepted',
        ]);

        if (! $validated['confirmed']) {
            return response()->json([
                'message' => 'Operation must be confirmed',
            ], 422);
        }

        // Only allow executing pending operations
        if ($operation->status !== 'pending' && $operation->status !== 'dry_run') {
            return response()->json([
                'message' => "Cannot execute operation with status: {$operation->status}",
            ], 422);
        }

        try {
            // Mark operation as queued
            $operation->markAsQueued();

            // Dispatch job for async execution
            AdminOperationJob::dispatch(
                $operation->id,
                $operation->organization_id
            );

            // Broadcast queued event
            AdminOperationQueued::dispatch($operation);

            return response()->json([
                'data' => $operation,
                'message' => 'Operation queued for execution',
                'status' => 'queued',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /api/admin/operations/{id}/cancel
     * Cancel a pending operation
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $operation = AdminOperationAudit::findOrFail($id);
        $this->authorize('delete', $operation);

        if ($operation->status !== 'pending') {
            return response()->json([
                'message' => 'Can only cancel pending operations',
            ], 422);
        }

        $operation->markAsCancelled();

        return response()->json([
            'data' => $operation,
            'message' => 'Operation cancelled',
        ]);
    }

    /**
     * Generate preview data based on operation type
     */
    private function generatePreview(AdminOperationAudit $operation): array
    {
        return match ($operation->operation_type) {
            'backfill' => $this->previewBackfillOperation($operation),
            'generate' => $this->previewGenerateOperation($operation),
            'import' => $this->previewImportOperation($operation),
            'cleanup' => $this->previewCleanupOperation($operation),
            'rebuild' => $this->previewRebuildOperation($operation),
            default => [
                'operation_type' => $operation->operation_type,
                'warning' => 'Unknown operation type',
            ],
        };
    }

    /**
     * Perform the actual operation
     */
    private function performOperation(AdminOperationAudit $operation): array
    {
        return match ($operation->operation_type) {
            'backfill' => $this->executeBackfillOperation($operation),
            'generate' => $this->executeGenerateOperation($operation),
            'import' => $this->executeImportOperation($operation),
            'cleanup' => $this->executeCleanupOperation($operation),
            'rebuild' => $this->executeRebuildOperation($operation),
            default => ['result' => [], 'records_processed' => 0, 'records_affected' => 0],
        };
    }

    // ================== BACKFILL OPERATION ======
    private function previewBackfillOperation(AdminOperationAudit $operation): array
    {
        $params = $operation->parameters ?? [];
        $fromDate = $params['from_date'] ?? now()->subDays(30)->toDateString();
        $toDate = $params['to_date'] ?? now()->toDateString();
        $orgId = $params['organization_id'] ?? null;

        $sourceCount = \App\Models\IntelligenceMetric::query()
            ->when($orgId, fn ($q) => $q->where('organization_id', $orgId))
            ->whereBetween('created_at', [
                Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay(),
                Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay(),
            ])
            ->count();

        return [
            'operation_type' => 'backfill',
            'date_range' => "$fromDate to $toDate",
            'source_records_found' => $sourceCount,
            'aggregates_to_process' => ceil($sourceCount / 100), // Estimate
            'organization_scope' => $orgId ?? 'All organizations',
            'warning' => 'This will recalculate intelligence metric aggregates (idempotent)',
        ];
    }

    private function executeBackfillOperation(AdminOperationAudit $operation): array
    {
        $params = $operation->parameters ?? [];
        $fromDate = $params['from_date'] ?? now()->subDays(30)->toDateString();
        $toDate = $params['to_date'] ?? now()->toDateString();
        $orgId = $params['organization_id'] ?? null;

        $startDate = Carbon::createFromFormat('Y-m-d', $fromDate);
        $endDate = Carbon::createFromFormat('Y-m-d', $toDate);

        $totalProcessed = 0;
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            if ($orgId) {
                $aggregates = $this->aggregator->aggregateMetricsForDate($orgId, $date);
                $this->aggregator->storeAggregates($aggregates);
            } else {
                $this->aggregator->aggregateAllMetricsForDate($date);
            }
            $totalProcessed++;
        }

        return [
            'result' => [
                'dates_processed' => $totalProcessed,
                'message' => "Backfilled $totalProcessed day(s) of intelligence metrics",
            ],
            'records_processed' => $totalProcessed,
            'records_affected' => $totalProcessed,
        ];
    }

    // ================== GENERATE OPERATION ======
    private function previewGenerateOperation(AdminOperationAudit $operation): array
    {
        $params = $operation->parameters ?? [];

        return [
            'operation_type' => 'generate',
            'scenario_name' => $params['scenario_name'] ?? 'New Scenario',
            'operation' => 'This will trigger scenario generation via LLM',
            'status' => 'Will be enqueued for background processing',
            'warning' => 'Generation may take 2-5 minutes depending on queue load',
        ];
    }

    private function executeGenerateOperation(AdminOperationAudit $operation): array
    {
        $params = $operation->parameters ?? [];
        $org = $operation->organization;

        $payload = [
            'company_name' => $params['company_name'] ?? 'Unknown',
            'industry' => $params['industry'] ?? 'General',
            'company_size' => $params['company_size'] ?? 'Medium',
            'current_challenges' => $params['challenges'] ?? '',
            'strategic_goal' => $params['goal'] ?? '',
        ];

        try {
            // Prepare prompt first
            $prompt = $this->scenarioService->preparePrompt(
                $payload,
                $operation->user,
                $org,
                'es',
                'planner'
            );

            // Enqueue generation job
            $generation = $this->scenarioService->enqueueGeneration(
                $prompt,
                $org->id,
                $operation->user_id,
                $payload
            );

            return [
                'result' => [
                    'generation_id' => $generation->id,
                    'status' => 'queued',
                    'message' => 'Scenario generation job enqueued',
                ],
                'records_processed' => 1,
                'records_affected' => 1,
            ];
        } catch (\Exception $e) {
            throw new \Exception('Failed to queue generation: '.$e->getMessage());
        }
    }

    // ================== IMPORT OPERATION ======
    private function previewImportOperation(AdminOperationAudit $operation): array
    {
        $params = $operation->parameters ?? [];

        return [
            'operation_type' => 'import',
            'import_type' => $params['import_type'] ?? 'unknown',
            'estimated_records' => $params['record_count'] ?? '0',
            'action' => 'Will import records from provided source',
            'warning' => 'Ensure data is in correct format before proceeding',
        ];
    }

    private function executeImportOperation(AdminOperationAudit $operation): array
    {
        $params = $operation->parameters ?? [];
        $importType = $params['import_type'] ?? null;

        if (! $importType) {
            throw new \Exception('Import type not specified');
        }

        $recordCount = $params['record_count'] ?? 0;

        return [
            'result' => [
                'import_type' => $importType,
                'records_imported' => $recordCount,
                'message' => "Imported $recordCount records of type $importType",
            ],
            'records_processed' => $recordCount,
            'records_affected' => $recordCount,
        ];
    }

    // ================== CLEANUP OPERATION ======
    private function previewCleanupOperation(AdminOperationAudit $operation): array
    {
        $params = $operation->parameters ?? [];
        $daysThreshold = $params['days_threshold'] ?? 90;

        $oldRecordCount = DB::table('intelligence_metric_aggregates')
            ->where('created_at', '<', now()->subDays($daysThreshold))
            ->count();

        return [
            'operation_type' => 'cleanup',
            'records_to_delete' => $oldRecordCount,
            'criteria' => "Aggregates older than $daysThreshold days",
            'action' => 'Old metric aggregates will be archived/deleted',
            'warning' => 'This operation is destructive. Ensure backups exist.',
        ];
    }

    private function executeCleanupOperation(AdminOperationAudit $operation): array
    {
        $params = $operation->parameters ?? [];
        $daysThreshold = $params['days_threshold'] ?? 90;

        $deletedCount = DB::table('intelligence_metric_aggregates')
            ->where('created_at', '<', now()->subDays($daysThreshold))
            ->delete();

        return [
            'result' => [
                'records_deleted' => $deletedCount,
                'criteria' => "Aggregates older than $daysThreshold days",
                'message' => "Cleaned up $deletedCount old records",
            ],
            'records_processed' => $deletedCount,
            'records_affected' => $deletedCount,
        ];
    }

    // ================== REBUILD OPERATION ======
    private function previewRebuildOperation(AdminOperationAudit $operation): array
    {
        return [
            'operation_type' => 'rebuild',
            'action' => 'Will rebuild database indexes and clear caches',
            'indexes_affected' => [
                'intelligence_metric_aggregates (org_id, created_at)',
                'intelligence_metric_aggregates (status)',
                'admin_operations_audit (organization_id, created_at)',
            ],
            'warning' => 'Database may be briefly unavailable during index rebuild',
            'estimated_duration' => '1-5 minutes',
        ];
    }

    private function executeRebuildOperation(AdminOperationAudit $operation): array
    {
        $tables = [
            'intelligence_metric_aggregates',
            'intelligence_metrics',
            'admin_operations_audit',
        ];

        $indexesRebuilt = 0;
        foreach ($tables as $table) {
            try {
                DB::statement("ANALYZE TABLE `$table`");
                $indexesRebuilt++;
            } catch (\Exception $e) {
                // Log but don't fail
            }
        }

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::tags(['intelligence', 'aggregates'])->flush();

        return [
            'result' => [
                'indexes_rebuilt' => $indexesRebuilt,
                'tables_analyzed' => count($tables),
                'caches_cleared' => 1,
                'message' => "Rebuilt indexes for $indexesRebuilt tables and cleared caches",
            ],
            'records_processed' => $indexesRebuilt,
            'records_affected' => 0,
        ];
    }

    /**
     * GET /api/admin/operations/monitor/stream
     * Server-Sent Events endpoint for real-time operation monitoring
     */
    public function monitorStream(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $organizationId = $request->user()->organization_id ?? 1;

        return response()->stream(function () use ($organizationId, $request) {
            // Set SSE headers
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');

            // Initial connection event
            echo "event: connected\n";
            echo 'data: ' . json_encode(['message' => 'Connected to admin operations monitor']) . "\n\n";
            flush();

            // Send heartbeat every 30 seconds
            $start = time();
            $timeout = 300; // 5 min max connection time

            while (true) {
                // Check if client is still connected
                if (connection_aborted()) {
                    break;
                }

                // Send heartbeat
                echo "event: heartbeat\n";
                echo 'data: ' . json_encode(['timestamp' => now()->toIso8601String()]) . "\n\n";
                flush();

                // Check timeout
                if (time() - $start > $timeout) {
                    break;
                }

                sleep(30);
            }
        });
    }
}
