<?php

namespace App\Services\Intelligence;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmartAlertService
{
    /**
     * Create a context-aware notification for the organization.
     */
    public function notify(int $orgId, string $title, string $message, string $level = 'info', string $category = 'system', ?array $action = null): void
    {
        try {
            DB::table('smart_alerts')->insert([
                'organization_id' => $orgId,
                'level' => $level,
                'category' => $category,
                'title' => $title,
                'message' => $message,
                'action_link' => json_encode($action),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::debug("Smart Alert created for $orgId: $title");
        } catch (\Exception $e) {
            Log::error("Failed to create smart alert", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get unread alerts for the organization.
     */
    public function getActiveAlerts(int $orgId): array
    {
        return DB::table('smart_alerts')
            ->where('organization_id', $orgId)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }
}
