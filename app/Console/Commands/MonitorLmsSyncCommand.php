<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitorLmsSyncCommand extends Command
{
    protected $signature = 'lms:monitor-sync {--max-lag-minutes=90 : Máximo atraso permitido desde última ejecución exitosa}';

    protected $description = 'Monitorea el estado de lms:sync-progress y emite alerta mínima si está atrasado o falló.';

    public function handle(): int
    {
        $maxLagMinutes = max(1, (int) $this->option('max-lag-minutes'));
        $lastRun = Cache::get('lms:sync-progress:last-run');

        if (! is_array($lastRun)) {
            $message = 'No existe heartbeat de lms:sync-progress. Ejecuta el comando al menos una vez.';
            $this->error($message);
            Log::warning('LMS sync monitor: missing heartbeat', [
                'max_lag_minutes' => $maxLagMinutes,
            ]);

            return self::FAILURE;
        }

        $status = (string) ($lastRun['status'] ?? 'unknown');
        $finishedAtRaw = $lastRun['finished_at'] ?? null;
        $finishedAt = is_string($finishedAtRaw) ? Carbon::parse($finishedAtRaw) : null;

        if ($status === 'failed') {
            $message = 'Última ejecución de lms:sync-progress terminó en fallo.';
            $this->error($message);
            Log::warning('LMS sync monitor: last run failed', [
                'last_run' => $lastRun,
            ]);

            return self::FAILURE;
        }

        if (! $finishedAt) {
            $message = 'No hay timestamp final válido para la última ejecución LMS.';
            $this->error($message);
            Log::warning('LMS sync monitor: invalid finished_at', [
                'last_run' => $lastRun,
            ]);

            return self::FAILURE;
        }

        $lagMinutes = $finishedAt->diffInMinutes(now());

        if ($lagMinutes > $maxLagMinutes) {
            $message = "Atraso detectado: última ejecución exitosa hace {$lagMinutes} min (máximo {$maxLagMinutes}).";
            $this->error($message);
            Log::warning('LMS sync monitor: lag exceeded', [
                'lag_minutes' => $lagMinutes,
                'max_lag_minutes' => $maxLagMinutes,
                'last_run' => $lastRun,
            ]);

            return self::FAILURE;
        }

        $this->info('OK: lms:sync-progress dentro del umbral operativo.');
        $this->line("Última ejecución exitosa: {$finishedAt->toDateTimeString()} ({$lagMinutes} min atrás)");

        return self::SUCCESS;
    }
}
