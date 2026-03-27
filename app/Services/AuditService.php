<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * AuditService - Registro de auditoría para verificaciones, acciones y CRUD
 *
 * Provides methods to:
 * - Log verification/action events (legacy)
 * - Query and filter audit logs (new)
 * - Generate reports and exports
 * - Analyze activity patterns
 */
class AuditService
{
    /**
     * Log a verification audit event (legacy)
     */
    public function logVerification(array $data): void
    {
        Log::channel('audit')->info('verification', $data);
    }

    /**
     * Log an action audit event (legacy)
     */
    public function logAction(array $data): void
    {
        Log::channel('audit')->info('action', $data);
    }

    /**
     * Log an error audit event (legacy)
     */
    public function logError(array $data): void
    {
        Log::channel('audit')->error('verification_error', $data);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // NEW METHODS: AuditLog Querying and Reporting
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Get recent audit logs for organization with optional filters
     */
    public function getRecentLogs(
        int $organizationId,
        array $filters = [],
        int $limit = 50
    ): Collection {
        $query = AuditLog::forOrganization($organizationId);

        // Apply filters
        if ($filters['entity_type'] ?? null) {
            $query->where('entity_type', $filters['entity_type']);
        }

        if ($filters['action'] ?? null) {
            $query->action($filters['action']);
        }

        if ($filters['user_id'] ?? null) {
            $query->createdBy($filters['user_id']);
        }

        if ($filters['days'] ?? null) {
            $query->recent($filters['days']);
        }

        return $query->latest('created_at')->limit($limit)->get();
    }

    /**
     * Get paginated logs for table display
     */
    public function paginateLogs(
        int $organizationId,
        array $filters = [],
        int $perPage = 20
    ): mixed {
        $query = AuditLog::forOrganization($organizationId);

        if ($filters['entity_type'] ?? null) {
            $query->where('entity_type', $filters['entity_type']);
        }

        if ($filters['action'] ?? null) {
            $query->action($filters['action']);
        }

        if ($filters['user_id'] ?? null) {
            $query->createdBy($filters['user_id']);
        }

        if ($filters['days'] ?? null) {
            $query->recent($filters['days']);
        }

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Get activity timeline for a specific entity
     */
    public function getEntityTimeline(string $entityType, string $entityId): Collection
    {
        return AuditLog::forEntity($entityType, $entityId)
            ->with('user:id,name,email')
            ->latest('created_at')
            ->get();
    }

    /**
     * Get audit statistics for organization
     */
    public function getStatistics(int $organizationId, int $days = 30): array
    {
        $logs = AuditLog::forOrganization($organizationId)
            ->recent($days)
            ->get();

        return [
            'total_events' => $logs->count(),
            'creates' => $logs->filter(fn ($l) => $l->action === 'created')->count(),
            'updates' => $logs->filter(fn ($l) => $l->action === 'updated')->count(),
            'deletes' => $logs->filter(fn ($l) => $l->action === 'deleted')->count(),
            'unique_users' => $logs->pluck('user_id')->unique()->count(),
            'unique_entities' => $logs->pluck('entity_type')->unique()->count(),
            'by_triggered_by' => $logs->groupBy('triggered_by')
                ->map(fn ($group) => $group->count())
                ->toArray(),
        ];
    }

    /**
     * Get user activity summary
     */
    public function getUserActivity(int $organizationId, int $userId, int $days = 30): array
    {
        $logs = AuditLog::forOrganization($organizationId)
            ->createdBy($userId)
            ->recent($days)
            ->get();

        return [
            'total_actions' => $logs->count(),
            'last_action' => $logs->first(),
            'first_action' => $logs->last(),
            'actions_by_type' => $logs->groupBy('entity_type')
                ->map(fn ($group) => $group->count())
                ->toArray(),
            'actions_by_verb' => $logs->groupBy('action')
                ->map(fn ($group) => $group->count())
                ->toArray(),
        ];
    }

    /**
     * Get changes to a specific entity over time
     */
    public function getEntityChangeHistory(string $entityType, string $entityId): array
    {
        $logs = AuditLog::forEntity($entityType, $entityId)
            ->orderBy('created_at', 'asc')
            ->get();

        $timeline = [];
        foreach ($logs as $log) {
            $timeline[] = [
                'timestamp' => $log->created_at,
                'action' => $log->action,
                'user' => $log->user?->name ?? 'System',
                'changes' => $log->changes ?? [],
                'summary' => $log->getChangeSummary(),
            ];
        }

        return $timeline;
    }

    /**
     * Export logs to CSV format
     */
    public function exportToCSV(int $organizationId, array $filters = []): string
    {
        $logs = $this->getRecentLogs($organizationId, $filters, 10000);

        $csv = "Timestamp,Action,Entity Type,Entity ID,User,Triggered By,Changes\n";

        foreach ($logs as $log) {
            $changes = $log->changes ? json_encode($log->changes) : '';
            $changes = str_replace('"', '""', $changes); // Escape quotes

            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log->created_at->format('Y-m-d H:i:s'),
                $log->action,
                $log->entity_type,
                $log->entity_id,
                $log->user?->name ?? 'N/A',
                $log->triggered_by,
                $changes
            );
        }

        return $csv;
    }

    /**
     * Get activity heatmap data (by hour of day, for visualization)
     */
    public function getActivityHeatmap(int $organizationId, int $days = 7): array
    {
        $logs = AuditLog::forOrganization($organizationId)
            ->recent($days)
            ->get();

        $heatmap = array_fill(0, 24, 0);

        foreach ($logs as $log) {
            $hour = $log->created_at->hour;
            $heatmap[$hour]++;
        }

        return $heatmap;
    }

    /**
     * Get activity by day (for trend visualization)
     */
    public function getActivityByDay(int $organizationId, int $days = 30): array
    {
        $logs = AuditLog::forOrganization($organizationId)
            ->recent($days)
            ->get();

        $trend = [];
        $now = now();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->format('Y-m-d');
            $trend[$date] = 0;
        }

        foreach ($logs as $log) {
            $date = $log->created_at->format('Y-m-d');
            if (isset($trend[$date])) {
                $trend[$date]++;
            }
        }

        return $trend;
    }
}

