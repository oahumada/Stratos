<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuditTrailService
{
    /**
     * Registra el razonamiento detrás de una decisión tomada por la IA.
     */
    public function logDecision(string $module, string $entityId, string $decision, array $rationale, string $agentName = 'Stratos Sentinel'): void
    {
        Log::info("AuditTrail: [{$module}] Decision on {$entityId} by {$agentName}");

        // Por ahora lo guardamos en un log estructurado, pero en fase 2 irá a una tabla 'ai_audit_trails'
        Log::channel('ai_audit')->info(json_encode([
            'timestamp' => now()->toIso8601String(),
            'module' => $module,
            'entity_id' => $entityId,
            'decision' => $decision,
            'rationale' => $rationale,
            'agent' => $agentName,
            'fingerprint' => md5(json_encode($rationale))
        ]));
    }
}
