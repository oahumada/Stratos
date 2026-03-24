<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * AuditService - Registro de auditoría para verificaciones y acciones
 */
class AuditService
{
    /**
     * Log a verification audit event
     */
    public function logVerification(array $data): void
    {
        Log::channel('audit')->info('verification', $data);
    }

    /**
     * Log an action audit event
     */
    public function logAction(array $data): void
    {
        Log::channel('audit')->info('action', $data);
    }

    /**
     * Log an error audit event
     */
    public function logError(array $data): void
    {
        Log::channel('audit')->error('verification_error', $data);
    }
}
