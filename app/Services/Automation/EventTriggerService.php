<?php

namespace App\Services\Automation;

use App\Models\EventStore;
use App\Services\Analytics\AnomalyDetectionService;
use App\Services\Analytics\PredictiveInsightsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * EventTriggerService
 *
 * Maps anomalies and predictions to automation triggers.
 * Determines which external workflows (n8n, webhooks) should be invoked
 * based on system events and analytics.
 */
class EventTriggerService
{
    public function __construct(
        private AnomalyDetectionService $anomalyService,
        private PredictiveInsightsService $predictiveService,
        private AutomationWorkflowService $workflowService
    ) {}

    /**
     * Evaluate organization state and trigger appropriate automations
     */
    public function evaluateAndTrigger(string $organizationId, string $triggerType = 'comprehensive'): array
    {
        $triggeredWorkflows = [];

        try {
            match ($triggerType) {
                'anomalies' => $triggeredWorkflows = $this->triggerFromAnomalies($organizationId),
                'predictions' => $triggeredWorkflows = $this->triggerFromPredictions($organizationId),
                'comprehensive' => $triggeredWorkflows = $this->triggerComprehensive($organizationId),
                default => [],
            };
        } catch (\Exception $e) {
            Log::error("Error in event trigger evaluation: {$e->getMessage()}");
        }

        return $triggeredWorkflows;
    }

    /**
     * Trigger workflows based on detected anomalies
     */
    private function triggerFromAnomalies(string $organizationId): array
    {
        $anomalies = $this->anomalyService->analyzeVerificationMetrics($organizationId);
        $triggeredWorkflows = [];

        // Latency spike → Performance investigation workflow
        if (isset($anomalies['avg_latency'])) {
            $latencyAnomaly = $anomalies['avg_latency'][0];
            $triggeredWorkflows[] = $this->workflowService->triggerWorkflow(
                organizationId: $organizationId,
                workflowCode: 'performance_investigation',
                triggerData: [
                    'anomaly_type' => 'latency_spike',
                    'current_latency_ms' => $latencyAnomaly['value'] ?? null,
                    'z_score' => $latencyAnomaly['z_score'] ?? null,
                    'severity' => $latencyAnomaly['severity'] ?? 'HIGH',
                    'action' => 'immediate_diagnostics',
                ]
            );
        }

        // System health degradation → Incident workflow
        if (isset($anomalies['system_health'])) {
            $health = $anomalies['system_health'][0];
            $triggeredWorkflows[] = $this->workflowService->triggerWorkflow(
                organizationId: $organizationId,
                workflowCode: 'incident_management',
                triggerData: [
                    'anomaly_type' => 'health_degradation',
                    'failure_rate' => $health['failure_rate'] ?? null,
                    'severity' => $health['severity'] ?? 'CRITICAL',
                    'action' => 'notify_ops_team',
                ]
            );
        }

        // Compliance drift → Compliance review workflow
        if (isset($anomalies['compliance_score'])) {
            $compliance = $anomalies['compliance_score'][0];
            $triggeredWorkflows[] = $this->workflowService->triggerWorkflow(
                organizationId: $organizationId,
                workflowCode: 'compliance_review',
                triggerData: [
                    'anomaly_type' => 'compliance_drift',
                    'old_score' => $compliance['old_average'] ?? null,
                    'new_score' => $compliance['new_average'] ?? null,
                    'direction' => $compliance['direction'] ?? 'UNKNOWN',
                    'action' => 'audit_and_remediate',
                ]
            );
        }

        return $triggeredWorkflows;
    }

    /**
     * Trigger workflows based on predictions
     */
    private function triggerFromPredictions(string $organizationId): array
    {
        $triggeredWorkflows = [];

        // Capacity saturation → Scaling workflow
        $resourcePrediction = $this->predictiveService->predictResourceNeeds($organizationId);

        if (isset($resourcePrediction['capacity_saturation_date'])) {
            $saturationDate = $resourcePrediction['capacity_saturation_date'];
            if ($saturationDate) {
                $daysUntil = now()->diffInDays(new \Carbon\Carbon($saturationDate));

                if ($daysUntil < 30) {
                    $triggeredWorkflows[] = $this->workflowService->triggerWorkflow(
                        organizationId: $organizationId,
                        workflowCode: 'capacity_scaling',
                        triggerData: [
                            'prediction_type' => 'capacity_saturation',
                            'saturation_date' => $saturationDate,
                            'days_until' => $daysUntil,
                            'action' => 'provision_resources',
                        ]
                    );
                }
            }
        }

        // Compliance breach forecast → Prevention workflow
        $complianceForecast = $this->predictiveService->forecastCompliance($organizationId);

        if (isset($complianceForecast['predicted_breach_date'])) {
            $breachDate = $complianceForecast['predicted_breach_date'];
            if ($breachDate) {
                $triggeredWorkflows[] = $this->workflowService->triggerWorkflow(
                    organizationId: $organizationId,
                    workflowCode: 'compliance_prevention',
                    triggerData: [
                        'prediction_type' => 'compliance_breach',
                        'predicted_breach_date' => $breachDate,
                        'current_score' => $complianceForecast['current_score'] ?? null,
                        'action' => 'preventive_measures',
                    ]
                );
            }
        }

        return $triggeredWorkflows;
    }

    /**
     * Comprehensive trigger evaluation
     */
    private function triggerComprehensive(string $organizationId): array
    {
        $anomalyTriggers = $this->triggerFromAnomalies($organizationId);
        $predictionTriggers = $this->triggerFromPredictions($organizationId);

        return array_values(array_filter(
            array_merge($anomalyTriggers, $predictionTriggers)
        ));
    }

    /**
     * Manually trigger a specific workflow
     */
    public function manuallyTrigger(
        string $organizationId,
        string $workflowCode,
        array $triggerData = []
    ): array {
        return $this->workflowService->triggerWorkflow(
            organizationId: $organizationId,
            workflowCode: $workflowCode,
            triggerData: $triggerData
        );
    }

    /**
     * Get available workflow codes
     */
    public function getAvailableWorkflows(): array
    {
        return [
            'performance_investigation' => 'Diagnostics for latency/throughput issues',
            'incident_management' => 'Ops team notification and incident tracking',
            'compliance_review' => 'Compliance audit and remediation',
            'capacity_scaling' => 'Infrastructure scaling request',
            'compliance_prevention' => 'Proactive compliance measures',
            'talent_alert' => 'Talent anomaly notifications',
            'security_incident' => 'Security event response',
            'custom' => 'Custom workflow (defined externally)',
        ];
    }

    /**
     * Log trigger event to event store
     */
    public function logTriggerEvent(
        string $organizationId,
        string $triggerType,
        array $triggerData,
        string $status = 'triggered'
    ): EventStore {
        return EventStore::create([
            'organization_id' => $organizationId,
            'event_name' => 'automation.trigger.' . $triggerType,
            'aggregate_type' => 'automation',
            'aggregate_id' => $organizationId,
            'data' => array_merge($triggerData, ['status' => $status]),
        ]);
    }
}
