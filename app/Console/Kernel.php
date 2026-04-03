<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands are auto-discovered from app/Console/Commands
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run compacting of generation chunks daily using configured TTL
        $days = config('services.abacus.chunks_ttl_days', 30);
        $schedule->command("generate:compact-chunks --days={$days}")->daily();

        // Generar reportes de impacto y ROI de forma automática
        $schedule->command('stratos:generate-impact-reports')->weeklyOn(1, '08:00');

        // Aggregate intelligence metrics daily at 01:00 UTC
        $schedule->job(new \App\Jobs\AggregateIntelligenceMetricsDaily)->dailyAt('01:00');

        // Sincronizar progreso LMS regularmente (emisión automática de certificados)
        // Ejecuta cada hora, evita solapamientos y corre en background.
        $schedule->command('lms:sync-progress')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
