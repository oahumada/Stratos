<?php

namespace App\Services;

use App\Models\VerificationAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerificationAuditService
{
    public function logPhaseTransition(
        int $organizationId,
        string $fromPhase,
        string $toPhase,
        string $triggeredBy = 'system',
        string $reason = '',
        ?int $userId = null
    ): void {
        try {
            VerificationAuditLog::create([
                'organization_id' => $organizationId,
                'user_id' => $userId ?? Auth::id(),
                'action' => 'phase_transition',
                'phase_from' => $fromPhase,
                'phase_to' => $toPhase,
                'triggered_by' => $triggeredBy,
                'reason' => $reason,
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging audit', ['error' => $e->getMessage()]);
        }
    }

    public function logConfigChange(
        int $organizationId,
        array $changes,
        string $triggeredBy = 'user',
        ?int $userId = null
    ): void {
        try {
            VerificationAuditLog::create([
                'organization_id' => $organizationId,
                'user_id' => $userId ?? Auth::id(),
                'action' => 'config_change',
                'changes' => $changes,
                'triggered_by' => $triggeredBy,
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging audit', ['error' => $e->getMessage()]);
        }
    }

    public function logManualOverride(
        int $organizationId,
        array $changes,
        string $reason = '',
        ?int $userId = null
    ): void {
        try {
            VerificationAuditLog::create([
                'organization_id' => $organizationId,
                'user_id' => $userId ?? Auth::id(),
                'action' => 'manual_override',
                'changes' => $changes,
                'triggered_by' => 'user',
                'reason' => $reason,
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging audit', ['error' => $e->getMessage()]);
        }
    }

    public function getOrganizationHistory(int $organizationId, int $days = 30): array
    {
        return VerificationAuditLog::where('organization_id', $organizationId)
            ->recent($days)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();
    }

    public function getPhaseTransitionHistory(int $organizationId): array
    {
        return VerificationAuditLog::where('organization_id', $organizationId)
            ->action('phase_transition')
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get()
            ->toArray();
    }
}
