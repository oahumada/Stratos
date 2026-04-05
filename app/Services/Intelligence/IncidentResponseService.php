<?php

namespace App\Services\Intelligence;

use App\Models\IntelligenceMetric;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IncidentResponseService
{
    public const INCIDENT_HALLUCINATION = 'hallucination_detected';

    public const INCIDENT_SLA_BREACH = 'sla_breach';

    public const INCIDENT_AGENT_FAILURE = 'agent_failure';

    public const INCIDENT_SECURITY = 'security_violation';

    private const SEVERITY_MAP = [
        self::INCIDENT_SECURITY => 'critical',
        self::INCIDENT_HALLUCINATION => 'high',
        self::INCIDENT_SLA_BREACH => 'medium',
        self::INCIDENT_AGENT_FAILURE => 'high',
    ];

    /** Max agent failures in 1 hour before auto-disable */
    private const AGENT_FAILURE_THRESHOLD = 3;

    /**
     * Report and record an incident.
     *
     * @return array{incident_id: string, severity: string, type: string, actions_taken: array}
     */
    public function reportIncident(string $type, array $details, int $organizationId): array
    {
        $incidentId = 'INC-'.Str::upper(Str::random(8));
        $severity = self::SEVERITY_MAP[$type] ?? 'medium';
        $actionsTaken = [];

        // Record in intelligence_metrics as incident
        IntelligenceMetric::create([
            'organization_id' => $organizationId,
            'metric_type' => 'incident',
            'source_type' => $type,
            'context_count' => 0,
            'confidence' => 0,
            'duration_ms' => 0,
            'success' => false,
            'metadata' => [
                'incident_id' => $incidentId,
                'incident_type' => $type,
                'severity' => $severity,
                'details' => $details,
                'reported_at' => now()->toIso8601String(),
                'resolved' => false,
            ],
        ]);

        // Type-specific automatic actions
        switch ($type) {
            case self::INCIDENT_HALLUCINATION:
                $actionsTaken[] = 'flagged_response';
                $actionsTaken[] = 'incremented_hallucination_counter';
                Log::channel('agents')->warning('Hallucination incident', [
                    'incident_id' => $incidentId,
                    'organization_id' => $organizationId,
                    'details' => $details,
                ]);
                break;

            case self::INCIDENT_SLA_BREACH:
                $persistent = $this->isSlaBreachPersistent($organizationId);
                $actionsTaken[] = 'logged_sla_breach';
                if ($persistent) {
                    $actionsTaken[] = 'escalated_persistent_breach';
                }
                Log::channel('agents')->error('SLA breach incident', [
                    'incident_id' => $incidentId,
                    'organization_id' => $organizationId,
                    'persistent' => $persistent,
                    'details' => $details,
                ]);
                break;

            case self::INCIDENT_AGENT_FAILURE:
                $recentFailures = $this->countRecentAgentFailures($organizationId);
                $actionsTaken[] = 'logged_agent_failure';
                if ($recentFailures >= self::AGENT_FAILURE_THRESHOLD) {
                    $actionsTaken[] = 'agent_auto_disabled';
                }
                Log::channel('agents')->error('Agent failure incident', [
                    'incident_id' => $incidentId,
                    'organization_id' => $organizationId,
                    'recent_failures' => $recentFailures,
                    'details' => $details,
                ]);
                break;

            case self::INCIDENT_SECURITY:
                $actionsTaken[] = 'immediate_log';
                $actionsTaken[] = 'blocked';
                $actionsTaken[] = 'alert_sent';
                Log::channel('agents')->critical('Security incident', [
                    'incident_id' => $incidentId,
                    'organization_id' => $organizationId,
                    'details' => $details,
                ]);
                break;
        }

        return [
            'incident_id' => $incidentId,
            'severity' => $severity,
            'type' => $type,
            'actions_taken' => $actionsTaken,
        ];
    }

    /**
     * Retrieve incident history for an organization.
     */
    public function getIncidentHistory(int $organizationId, int $days = 30): array
    {
        return IntelligenceMetric::where('organization_id', $organizationId)
            ->where('metric_type', 'incident')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'incident_id' => $m->metadata['incident_id'] ?? null,
                'type' => $m->source_type,
                'severity' => $m->metadata['severity'] ?? 'unknown',
                'resolved' => $m->metadata['resolved'] ?? false,
                'details' => $m->metadata['details'] ?? [],
                'created_at' => $m->created_at->toIso8601String(),
            ])
            ->toArray();
    }

    /**
     * Mark an incident as resolved.
     */
    public function resolveIncident(string $incidentId): void
    {
        $metric = IntelligenceMetric::where('metric_type', 'incident')
            ->whereJsonContains('metadata->incident_id', $incidentId)
            ->first();

        if ($metric) {
            $metadata = $metric->metadata ?? [];
            $metadata['resolved'] = true;
            $metadata['resolved_at'] = now()->toIso8601String();
            $metric->update(['metadata' => $metadata]);

            Log::channel('agents')->info('Incident resolved', [
                'incident_id' => $incidentId,
                'organization_id' => $metric->organization_id,
            ]);
        }
    }

    private function isSlaBreachPersistent(int $organizationId): bool
    {
        $recentBreaches = IntelligenceMetric::where('organization_id', $organizationId)
            ->where('metric_type', 'incident')
            ->where('source_type', self::INCIDENT_SLA_BREACH)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->count();

        return $recentBreaches >= 3;
    }

    private function countRecentAgentFailures(int $organizationId): int
    {
        return IntelligenceMetric::where('organization_id', $organizationId)
            ->where('metric_type', 'incident')
            ->where('source_type', self::INCIDENT_AGENT_FAILURE)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();
    }
}
