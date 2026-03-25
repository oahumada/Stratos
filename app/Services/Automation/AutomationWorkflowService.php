<?php

namespace App\Services\Automation;

use App\Services\HybridGatewayService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * AutomationWorkflowService
 *
 * Executes automation workflows via n8n (via HybridGatewayService).
 * Provides workflow orchestration, retry logic, and state management.
 */
class AutomationWorkflowService
{
    protected int $maxRetries = 3;

    protected int $retryDelaySeconds = 60;

    public function __construct(private HybridGatewayService $gatewayService) {}

    /**
     * Trigger a workflow execution
     */
    public function triggerWorkflow(
        string $organizationId,
        string $workflowCode,
        array $triggerData = [],
        bool $async = true
    ): array {
        $workflowId = Str::uuid();
        $executionId = "{$organizationId}:{$workflowCode}:{$workflowId}";

        try {
            // Prepare workflow payload
            $payload = [
                'execution_id' => $executionId,
                'organization_id' => $organizationId,
                'workflow_code' => $workflowCode,
                'trigger_data' => $triggerData,
                'timestamp' => now()->toIso8601String(),
                'async' => $async,
            ];

            // Send to n8n via HybridGatewayService
            $response = $this->gatewayService->sendToN8n(
                endpoint: "/{$workflowCode}/trigger",
                data: $payload,
                headers: [
                    'X-Execution-ID' => $executionId,
                    'X-Organization-ID' => $organizationId,
                ]
            );

            // Cache execution state
            Cache::put(
                "automation:execution:{$executionId}",
                [
                    'status' => 'pending',
                    'workflow_code' => $workflowCode,
                    'organization_id' => $organizationId,
                    'triggered_at' => now(),
                    'n8n_response' => $response,
                ],
                ttl: 86400  // 24 hours
            );

            Log::info("Workflow triggered: {$executionId}", [
                'workflow_code' => $workflowCode,
                'organization_id' => $organizationId,
            ]);

            return [
                'execution_id' => $executionId,
                'workflow_code' => $workflowCode,
                'status' => 'triggered',
                'async' => $async,
                'n8n_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error("Failed to trigger workflow: {$e->getMessage()}", [
                'execution_id' => $executionId,
                'workflow_code' => $workflowCode,
            ]);

            return [
                'execution_id' => $executionId,
                'workflow_code' => $workflowCode,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get execution status
     */
    public function getExecutionStatus(string $executionId): array
    {
        $cached = Cache::get("automation:execution:{$executionId}");

        if ($cached) {
            return [
                'execution_id' => $executionId,
                'status' => $cached['status'] ?? 'unknown',
                'workflow_code' => $cached['workflow_code'] ?? null,
                'triggered_at' => $cached['triggered_at'] ?? null,
                'n8n_response' => $cached['n8n_response'] ?? null,
            ];
        }

        return [
            'execution_id' => $executionId,
            'status' => 'not_found',
        ];
    }

    /**
     * Cancel an execution
     */
    public function cancelExecution(string $executionId): array
    {
        try {
            $response = $this->gatewayService->sendToN8n(
                endpoint: '/executions/cancel',
                data: ['execution_id' => $executionId]
            );

            // Update cache
            if ($cached = Cache::get("automation:execution:{$executionId}")) {
                $cached['status'] = 'cancelled';
                Cache::put("automation:execution:{$executionId}", $cached, ttl: 86400);
            }

            return [
                'execution_id' => $executionId,
                'status' => 'cancelled',
                'n8n_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error("Failed to cancel execution: {$e->getMessage()}");

            return [
                'execution_id' => $executionId,
                'status' => 'cancel_failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retry a failed execution
     */
    public function retryExecution(string $executionId, array $updatedTriggerData = []): array
    {
        $cached = Cache::get("automation:execution:{$executionId}");

        if (! $cached) {
            return [
                'execution_id' => $executionId,
                'status' => 'not_found',
                'error' => 'Execution not found',
            ];
        }

        $workflowCode = $cached['workflow_code'];
        $organizationId = $cached['organization_id'];

        // Merge updated trigger data with original
        $triggerData = array_merge(
            $cached['trigger_data'] ?? [],
            $updatedTriggerData,
            ['retry_from' => $executionId, 'retry_count' => ($cached['retry_count'] ?? 0) + 1]
        );

        return $this->triggerWorkflow(
            organizationId: $organizationId,
            workflowCode: $workflowCode,
            triggerData: $triggerData
        );
    }

    /**
     * List all workflows available in n8n
     */
    public function listAvailableWorkflows(): array
    {
        try {
            $response = $this->gatewayService->sendToN8n(
                endpoint: '/workflows/list',
                data: []
            );

            return [
                'status' => 'success',
                'workflows' => $response['workflows'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error("Failed to list workflows: {$e->getMessage()}");

            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Pause/resume automation for organization
     */
    public function toggleAutomationStatus(
        string $organizationId,
        bool $enabled
    ): array {
        Cache::put(
            "automation:status:{$organizationId}",
            ['enabled' => $enabled, 'updated_at' => now()],
            ttl: 86400 * 365
        );

        return [
            'organization_id' => $organizationId,
            'automation_enabled' => $enabled,
            'updated_at' => now(),
        ];
    }

    /**
     * Check if automation is enabled for organization
     */
    public function isAutomationEnabled(string $organizationId): bool
    {
        $cached = Cache::get("automation:status:{$organizationId}");

        return $cached['enabled'] ?? true;  // Default enabled
    }

    /**
     * Get execution history for organization
     */
    public function getExecutionHistory(
        string $organizationId,
        int $limit = 50
    ): array {
        // This would typically query from event_store or dedicated audit table
        // For now, return cache-based limited history

        $pattern = "automation:execution:{$organizationId}:*";
        $keys = Cache::many(array_keys(array_flip(
            collect(Cache::store('array')->getPrefix())
                ->filter(fn ($key) => str_contains($key, $pattern))
                ->toArray()
        )));

        return [
            'organization_id' => $organizationId,
            'executions' => array_slice($keys, 0, $limit),
            'total' => count($keys),
        ];
    }
}
