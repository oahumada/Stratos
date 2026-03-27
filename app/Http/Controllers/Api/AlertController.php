<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlertThresholdRequest;
use App\Http\Requests\UpdateAlertThresholdRequest;
use App\Models\AlertHistory;
use App\Models\AlertThreshold;
use App\Services\AlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function __construct(private AlertService $alertService)
    {
    }

    /**
     * Get all active alert thresholds
     */
    public function indexThresholds(Request $request): JsonResponse
    {
        $this->authorize('viewAny', AlertThreshold::class);

        $organizationId = $request->user()->organization_id;

        $thresholds = AlertThreshold::forOrganization($organizationId)
            ->active()
            ->latest()
            ->paginate(50);

        return response()->json($thresholds);
    }

    /**
     * Store a new alert threshold
     */
    public function storeThreshold(StoreAlertThresholdRequest $request): JsonResponse
    {
        $this->authorize('create', AlertThreshold::class);

        $organizationId = $request->user()->organization_id;

        $threshold = AlertThreshold::create(array_merge(
            $request->validated(),
            ['organization_id' => $organizationId]
        ));

        return response()->json($threshold, 201);
    }

    /**
     * Get alert threshold details
     */
    public function showThreshold(AlertThreshold $threshold, Request $request): JsonResponse
    {
        $this->authorize('view', $threshold);

        $organizationId = $request->user()->organization_id;

        if ($threshold->organization_id !== $organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($threshold->load('alertHistories'));
    }

    /**
     * Update alert threshold
     */
    public function updateThreshold(
        AlertThreshold $threshold,
        UpdateAlertThresholdRequest $request
    ): JsonResponse {
        $this->authorize('update', $threshold);

        $organizationId = $request->user()->organization_id;

        if ($threshold->organization_id !== $organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $threshold->update($request->validated());

        return response()->json($threshold);
    }

    /**
     * Delete alert threshold (soft delete)
     */
    public function destroyThreshold(AlertThreshold $threshold, Request $request): JsonResponse
    {
        $this->authorize('delete', $threshold);

        $organizationId = $request->user()->organization_id;

        if ($threshold->organization_id !== $organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $threshold->delete();

        return response()->json(['message' => 'Threshold deleted'], 200);
    }

    /**
     * Get all alert history entries
     */
    public function indexHistory(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $history = AlertHistory::forOrganization($organizationId)
            ->with('alertThreshold', 'acknowledgedBy')
            ->latest('triggered_at')
            ->paginate(100);

        return response()->json($history);
    }

    /**
     * Get alert history details
     */
    public function showHistory(AlertHistory $alert, Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        if ($alert->organization_id !== $organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($alert->load('alertThreshold', 'acknowledgedBy', 'organization'));
    }

    /**
     * Acknowledge an alert
     */
    public function acknowledgeAlert(AlertHistory $alert, Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        if ($alert->organization_id !== $organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->alertService->acknowledgeAlert(
            $alert,
            $request->user()->id,
            $request->input('notes')
        );

        return response()->json($alert->fresh());
    }

    /**
     * Resolve an alert
     */
    public function resolveAlert(AlertHistory $alert, Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        if ($alert->organization_id !== $organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->alertService->resolveAlert($alert);

        return response()->json($alert->fresh());
    }

    /**
     * Mute an alert
     */
    public function muteAlert(AlertHistory $alert, Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        if ($alert->organization_id !== $organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->alertService->muteAlert($alert);

        return response()->json($alert->fresh());
    }

    /**
     * Get unacknowledged alerts
     */
    public function getUnacknowledged(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $alerts = $this->alertService->getUnacknowledgedAlerts($organizationId);

        return response()->json($alerts);
    }

    /**
     * Get critical alerts
     */
    public function getCritical(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $alerts = $this->alertService->getCriticalAlerts($organizationId);

        return response()->json($alerts);
    }

    /**
     * Get alert statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $stats = $this->alertService->getAlertStatistics($organizationId);

        return response()->json($stats);
    }

    /**
     * Get alert history with pagination
     */
    public function history(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $history = $this->alertService->getAlertHistory($organizationId, 50);

        return response()->json($history);
    }

    /**
     * Bulk acknowledge alerts
     */
    public function bulkAcknowledge(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $validated = $request->validate([
            'alert_ids' => 'required|array|min:1',
            'alert_ids.*' => 'integer|exists:alert_histories,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $alerts = AlertHistory::forOrganization($organizationId)
            ->whereIn('id', $validated['alert_ids'])
            ->get();

        foreach ($alerts as $alert) {
            $this->alertService->acknowledgeAlert(
                $alert,
                $request->user()->id,
                $validated['notes'] ?? null
            );
        }

        return response()->json(['acknowledged' => $alerts->count()]);
    }

    /**
     * Export alert history (CSV)
     */
    public function exportHistory(Request $request)
    {
        $organizationId = $request->user()->organization_id;

        $alerts = $this->alertService->getAlertHistory($organizationId, 1000);

        $csv = "Alert ID,Metric,Severity,Triggered At,Acknowledged At,Resolved At,Status,Value\n";

        foreach ($alerts as $alert) {
            $csv .= sprintf(
                '"%d","%s","%s","%s","%s","%s","%s","%.2f"' . "\n",
                $alert->id,
                $alert->alertThreshold->metric,
                $alert->severity,
                $alert->triggered_at->format('Y-m-d H:i:s'),
                $alert->acknowledged_at?->format('Y-m-d H:i:s') ?? '',
                $alert->resolved_at?->format('Y-m-d H:i:s') ?? '',
                $alert->status,
                $alert->metric_value
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="alerts_' . now()->format('Y-m-d_His') . '.csv"');
    }
}
