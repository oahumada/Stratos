<?php

namespace App\Services;

use App\Models\AlertHistory;
use App\Models\AlertThreshold;
use App\Models\EscalationPolicy;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;

class AlertService
{
    /**
     * Check if a metric value triggers any active thresholds
     */
    public function checkMetric(int $organizationId, string $metric, float $value): ?AlertHistory
    {
        $thresholds = AlertThreshold::forOrganization($organizationId)
            ->forMetric($metric)
            ->active()
            ->get();

        if ($thresholds->isEmpty()) {
            return null;
        }

        foreach ($thresholds as $threshold) {
            // If there's an existing recent unacknowledged alert, return it
            $recentAlert = $threshold->alertHistories()
                ->triggered()
                ->whereNull('acknowledged_at')
                ->latest('triggered_at')
                ->first();

            if ($recentAlert) {
                if ($value > $threshold->threshold) {
                    return $recentAlert;
                }

                // value not above threshold, continue to next threshold
                continue;
            }

            if ($this->shouldTriggerThreshold($threshold, $value)) {
                return $this->createAlert($threshold, $value);
            }
        }

        return null;
    }

    /**
     * Determine if a metric value exceeds threshold
     */
    public function shouldTriggerThreshold(AlertThreshold $threshold, float $value): bool
    {
        // Check if recent unacknowledged alert exists for this threshold
        $recentAlert = $threshold->alertHistories()
            ->triggered()
            ->whereNull('acknowledged_at')
            ->latest('triggered_at')
            ->first();

        // If there's already a recent unacknowledged alert for this threshold,
        // do not create a duplicate alert. Only create when there is no recent
        // unacknowledged alert and the value exceeds threshold.
        if ($recentAlert) {
            return false;
        }

        // New alert: trigger if value exceeds threshold
        return $value > $threshold->threshold;
    }

    /**
     * Create a new alert history record
     */
    public function createAlert(AlertThreshold $threshold, float $value): AlertHistory
    {
        return AlertHistory::create([
            'organization_id' => $threshold->organization_id,
            'alert_threshold_id' => $threshold->id,
            'triggered_at' => now(),
            'severity' => $threshold->severity,
            'status' => 'triggered',
            'metric_value' => $value,
        ]);
    }

    /**
     * Acknowledge an alert (with optional notes)
     */
    public function acknowledgeAlert(AlertHistory $alert, int $userId, ?string $notes = null): AlertHistory
    {
        $alert->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => $userId,
            'status' => 'acknowledged',
            'notes' => $notes,
        ]);

        return $alert->fresh();
    }

    /**
     * Resolve an alert
     */
    public function resolveAlert(AlertHistory $alert): AlertHistory
    {
        $alert->update([
            'resolved_at' => now(),
            'status' => 'resolved',
        ]);

        return $alert->fresh();
    }

    /**
     * Mute an alert (suppress notifications)
     */
    public function muteAlert(AlertHistory $alert): AlertHistory
    {
        $alert->update([
            'status' => 'muted',
            'muted_at' => now(),
        ]);

        return $alert->fresh();
    }

    /**
     * Get unacknowledged alerts for organization
     */
    public function getUnacknowledgedAlerts(int $organizationId): Collection
    {
        return AlertHistory::forOrganization($organizationId)
            ->triggered()
            ->whereNull('acknowledged_at')
            ->latest('triggered_at')
            ->get();
    }

    /**
     * Get critical alerts (severity >= 'critical' or 'high')
     */
    public function getCriticalAlerts(int $organizationId): Collection
    {
        return AlertHistory::forOrganization($organizationId)
            ->whereIn('severity', ['critical', 'high'])
            ->whereNull('resolved_at')
            ->latest('triggered_at')
            ->get();
    }

    /**
     * Get escalation policy for a severity level
     */
    public function getEscalationPolicies(int $organizationId, string $severity): Collection
    {
        return EscalationPolicy::getChainForSeverity($organizationId, $severity);
    }

    /**
     * Calculate alert statistics for dashboard
     */
    public function getAlertStatistics(int $organizationId): array
    {
        $unacknowledged = AlertHistory::forOrganization($organizationId)
            ->triggered()
            ->whereNull('acknowledged_at')
            ->count();

        $critical = AlertHistory::forOrganization($organizationId)
            ->critical()
            ->where('status', '!=', 'resolved')
            ->count();

        $recentTriggered = AlertHistory::forOrganization($organizationId)
            ->latest('triggered_at')
            ->where('triggered_at', '>=', now()->subHours(24))
            ->count();

        $acknowledgedRate = $this->calculateAcknowledgeRate($organizationId);

        return [
            'unacknowledged_count' => $unacknowledged,
            'critical_count' => $critical,
            'recent_24h' => $recentTriggered,
            'acknowledge_rate' => $acknowledgedRate,
        ];
    }

    /**
     * Calculate percentage of acknowledged alerts in last 7 days
     */
    private function calculateAcknowledgeRate(int $organizationId): float
    {
        $totalAlerts = AlertHistory::forOrganization($organizationId)
            ->where('triggered_at', '>=', now()->subDays(7))
            ->count();

        if ($totalAlerts === 0) {
            return 100.0;
        }

        $acknowledgedAlerts = AlertHistory::forOrganization($organizationId)
            ->where('triggered_at', '>=', now()->subDays(7))
            ->whereNotNull('acknowledged_at')
            ->count();

        return round(($acknowledgedAlerts / $totalAlerts) * 100, 2);
    }

    /**
     * Get recent alert history with context
     */
    public function getAlertHistory(int $organizationId, int $limit = 50): Collection
    {
        return AlertHistory::forOrganization($organizationId)
            ->with('alertThreshold', 'acknowledgedBy')
            ->latest('triggered_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Clear old resolved alerts (older than specified days)
     */
    public function archiveOldAlerts(int $organizationId, int $daysBefore = 90): int
    {
        return AlertHistory::forOrganization($organizationId)
            ->where('status', 'resolved')
            ->where('resolved_at', '<', now()->subDays($daysBefore))
            ->delete();
    }

    /**
     * Bulk create thresholds from configuration
     */
    public function createThresholds(int $organizationId, array $thresholdConfigs): Collection
    {
        $created = collect();

        foreach ($thresholdConfigs as $config) {
            $threshold = AlertThreshold::create([
                'organization_id' => $organizationId,
                'metric' => $config['metric'],
                'threshold' => $config['threshold'],
                'severity' => $config['severity'] ?? 'medium',
                'is_active' => $config['is_active'] ?? true,
                'description' => $config['description'] ?? null,
            ]);

            $created->push($threshold);
        }

        return Collection::make($created->all());
    }
}
