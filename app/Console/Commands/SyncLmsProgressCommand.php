<?php

namespace App\Console\Commands;

use App\Services\Talent\Lms\LmsAnalyticsService;
use App\Services\Talent\Lms\LmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncLmsProgressCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'lms:sync-progress {--organization_id= : Filtra por organización} {--action_id= : Sincroniza una acción específica}';

    /**
     * @var string
     */
    protected $description = 'Sincroniza el progreso LMS por lote y emite certificados automáticamente al completar.';

    public function handle(LmsService $lmsService, LmsAnalyticsService $lmsAnalyticsService): int
    {
        $organizationId = $this->option('organization_id');
        $actionId = $this->option('action_id');
        $startedAt = now();

        Cache::forever('lms:sync-progress:last-run', [
            'status' => 'running',
            'started_at' => $startedAt->toIso8601String(),
            'finished_at' => null,
            'duration_ms' => null,
            'organization_id' => $organizationId !== null ? (int) $organizationId : null,
            'action_id' => $actionId !== null ? (int) $actionId : null,
            'processed' => null,
            'updated' => null,
            'error' => null,
        ]);

        $this->info('Iniciando sincronización LMS...');

        try {
            $result = $lmsService->syncPendingActions(
                $organizationId !== null ? (int) $organizationId : null,
                $actionId !== null ? (int) $actionId : null,
            );

            $finishedAt = now();

            Cache::forever('lms:sync-progress:last-run', [
                'status' => 'success',
                'started_at' => $startedAt->toIso8601String(),
                'finished_at' => $finishedAt->toIso8601String(),
                'duration_ms' => $finishedAt->diffInMilliseconds($startedAt),
                'organization_id' => $organizationId !== null ? (int) $organizationId : null,
                'action_id' => $actionId !== null ? (int) $actionId : null,
                'processed' => $result['processed'] ?? null,
                'updated' => $result['updated'] ?? null,
                'error' => null,
            ]);

            $lmsAnalyticsService->trackSyncBatch(
                $organizationId !== null ? (int) $organizationId : null,
                (int) ($result['processed'] ?? 0),
                (int) ($result['updated'] ?? 0),
                true,
            );

            $this->info("Acciones procesadas: {$result['processed']}");
            $this->info("Acciones actualizadas: {$result['updated']}");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $finishedAt = now();

            Cache::forever('lms:sync-progress:last-run', [
                'status' => 'failed',
                'started_at' => $startedAt->toIso8601String(),
                'finished_at' => $finishedAt->toIso8601String(),
                'duration_ms' => $finishedAt->diffInMilliseconds($startedAt),
                'organization_id' => $organizationId !== null ? (int) $organizationId : null,
                'action_id' => $actionId !== null ? (int) $actionId : null,
                'processed' => null,
                'updated' => null,
                'error' => $exception->getMessage(),
            ]);

            $lmsAnalyticsService->trackSyncBatch(
                $organizationId !== null ? (int) $organizationId : null,
                0,
                0,
                false,
                $exception->getMessage(),
            );

            Log::error('LMS sync progress command failed', [
                'organization_id' => $organizationId,
                'action_id' => $actionId,
                'error' => $exception->getMessage(),
            ]);

            $this->error('La sincronización LMS falló: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
