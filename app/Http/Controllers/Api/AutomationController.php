<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\WebhookRegistry;
use App\Services\Automation\AutomationWorkflowService;
use App\Services\Automation\EventTriggerService;
use App\Services\Automation\RemediationService;
use App\Services\Automation\WebhookRegistryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AutomationController
 *
 * Manages automation workflows, triggers, and webhooks for Phase 10.
 * - Event-driven trigger evaluation
 * - n8n workflow invocation
 * - Custom webhook registration and delivery
 * - Automatic remediation actions
 *
 * All endpoints multi-tenant scoped by organization_id.
 */
class AutomationController extends Controller
{
    public function __construct(
        private EventTriggerService $triggerService,
        private AutomationWorkflowService $workflowService,
        private RemediationService $remediationService,
        private WebhookRegistryService $webhookService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/automation/evaluate
     * Evaluate organization state and trigger automations
     */
    public function evaluate(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $triggerType = $request->query('trigger_type', 'comprehensive');

        $results = $this->triggerService->evaluateAndTrigger(
            organizationId: $organizationId,
            triggerType: $triggerType
        );

        return response()->json([
            'organization_id' => $organizationId,
            'trigger_type' => $triggerType,
            'triggered_workflows' => $results,
            'count' => count($results),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * POST /api/automation/workflows/{code}/trigger
     * Manually trigger a specific workflow
     */
    public function triggerWorkflow(Request $request, string $code): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $validated = $request->validate([
            'trigger_data' => 'array',
            'async' => 'boolean|default:true',
        ]);

        $result = $this->workflowService->triggerWorkflow(
            organizationId: $organizationId,
            workflowCode: $code,
            triggerData: $validated['trigger_data'] ?? [],
            async: $validated['async'] ?? true
        );

        return response()->json($result, 202);  // 202 Accepted
    }

    /**
     * GET /api/automation/workflows/available
     * List available workflows in n8n
     */
    public function listAvailableWorkflows(Request $request): JsonResponse
    {
        $workflows = $this->workflowService->listAvailableWorkflows();

        return response()->json([
            'organization_id' => $request->user()->organization_id,
            'workflows' => $workflows['workflows'] ?? [],
            'count' => count($workflows['workflows'] ?? []),
        ]);
    }

    /**
     * GET /api/automation/executions/{executionId}
     * Get execution status
     */
    public function getExecutionStatus(Request $request, string $executionId): JsonResponse
    {
        $status = $this->workflowService->getExecutionStatus($executionId);

        return response()->json($status);
    }

    /**
     * DELETE /api/automation/executions/{executionId}
     * Cancel an execution
     */
    public function cancelExecution(Request $request, string $executionId): JsonResponse
    {
        $result = $this->workflowService->cancelExecution($executionId);

        return response()->json($result);
    }

    /**
     * POST /api/automation/executions/{executionId}/retry
     * Retry a failed execution
     */
    public function retryExecution(Request $request, string $executionId): JsonResponse
    {
        $validated = $request->validate([
            'updated_trigger_data' => 'array|nullable',
        ]);

        $result = $this->workflowService->retryExecution(
            executionId: $executionId,
            updatedTriggerData: $validated['updated_trigger_data'] ?? []
        );

        return response()->json($result, 202);
    }

    /**
     * GET /api/automation/webhooks
     * List registered webhooks
     */
    public function listWebhooks(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $webhooks = WebhookRegistry::where('organization_id', $organizationId)
            ->select(['id', 'webhook_url', 'is_active', 'last_triggered_at', 'failure_count', 'event_filters'])
            ->get()
            ->map(fn ($w) => array_merge($w->toArray(), ['health' => $w->health]));

        return response()->json([
            'organization_id' => $organizationId,
            'webhooks' => $webhooks,
            'count' => count($webhooks),
        ]);
    }

    /**
     * POST /api/automation/webhooks
     * Register a new webhook
     */
    public function registerWebhook(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $validated = $request->validate([
            'webhook_url' => 'required|url',
            'event_filters' => 'array|nullable',
            'active' => 'boolean|default:true',
        ]);

        $organization = Organizations::findOrFail($organizationId);

        $webhook = $this->webhookService->registerWebhook(
            organization: $organization,
            url: $validated['webhook_url'],
            eventFilters: $validated['event_filters'] ?? [],
            active: $validated['active'] ?? true
        );

        return response()->json([
            'webhook' => [
                'id' => $webhook->id,
                'webhook_url' => $webhook->webhook_url,
                'event_filters' => $webhook->event_filters,
                'is_active' => $webhook->is_active,
            ],
            'signing_secret' => $webhook->raw_secret,  // Only shown once
            'message' => 'Webhook registered. Save the signing_secret in a secure location.',
        ], 201);
    }

    /**
     * PATCH /api/automation/webhooks/{webhookId}
     * Update webhook configuration
     */
    public function updateWebhook(Request $request, int $webhookId): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $webhook = WebhookRegistry::where('organization_id', $organizationId)
            ->findOrFail($webhookId);

        $validated = $request->validate([
            'event_filters' => 'array|nullable',
            'is_active' => 'boolean|nullable',
        ]);

        $updated = $this->webhookService->updateWebhook($webhook, $validated);

        return response()->json([
            'webhook' => [
                'id' => $updated->id,
                'webhook_url' => $updated->webhook_url,
                'event_filters' => $updated->event_filters,
                'is_active' => $updated->is_active,
                'health' => $updated->health,
            ],
        ]);
    }

    /**
     * DELETE /api/automation/webhooks/{webhookId}
     * Delete webhook
     */
    public function deleteWebhook(Request $request, int $webhookId): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $webhook = WebhookRegistry::where('organization_id', $organizationId)
            ->findOrFail($webhookId);

        $this->webhookService->deleteWebhook($webhook);

        return response()->json(['message' => 'Webhook deleted'], 204);
    }

    /**
     * POST /api/automation/webhooks/{webhookId}/test
     * Test webhook delivery
     */
    public function testWebhook(Request $request, int $webhookId): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $webhook = WebhookRegistry::where('organization_id', $organizationId)
            ->findOrFail($webhookId);

        $result = $this->webhookService->testWebhook($webhook);

        return response()->json([
            'webhook_id' => $webhookId,
            'test_result' => $result,
            'status' => $result['status'] ?? 'unknown',
        ]);
    }

    /**
     * GET /api/automation/webhooks/{webhookId}/stats
     * Get webhook statistics
     */
    public function getWebhookStats(Request $request, int $webhookId): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $webhook = WebhookRegistry::where('organization_id', $organizationId)
            ->findOrFail($webhookId);

        $stats = $this->webhookService->getWebhookStats($webhook);

        return response()->json($stats);
    }

    /**
     * POST /api/automation/remediate
     * Evaluate anomaly and execute remediation
     */
    public function remediateAnomaly(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $validated = $request->validate([
            'anomaly' => 'required|array',
            'level' => 'in:automatic,manual,escalation|default:automatic',
        ]);

        $organization = Organizations::findOrFail($organizationId);

        $remediation = $this->remediationService->remediateAnomaly(
            organization: $organization,
            anomaly: $validated['anomaly'],
            level: $validated['level'] ?? 'automatic'
        );

        return response()->json($remediation, 202);
    }

    /**
     * GET /api/automation/remediation-history
     * Get remediation action history
     */
    public function getRemediationHistory(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $organization = Organizations::findOrFail($organizationId);

        $limit = $request->query('limit', 50);

        $history = $this->remediationService->getRemediationHistory($organization, $limit);

        return response()->json($history);
    }

    /**
     * GET /api/automation/status
     * Get automation system status
     */
    public function getAutomationStatus(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $enabled = $this->workflowService->isAutomationEnabled($organizationId);

        return response()->json([
            'organization_id' => $organizationId,
            'automation_enabled' => $enabled,
            'status' => $enabled ? 'active' : 'paused',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * POST /api/automation/status
     * Toggle automation on/off
     */
    public function toggleAutomationStatus(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);

        $result = $this->workflowService->toggleAutomationStatus(
            organizationId: $organizationId,
            enabled: $validated['enabled']
        );

        return response()->json($result);
    }
}
