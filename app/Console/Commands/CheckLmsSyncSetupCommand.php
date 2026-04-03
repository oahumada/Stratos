<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CheckLmsSyncSetupCommand extends Command
{
    protected $signature = 'lms:check-sync-setup';

    protected $description = 'Valida que lms:sync-progress esté correctamente registrado y programado en el scheduler activo.';

    public function handle(): int
    {
        $errors = [];
        $warnings = [];

        if ($this->isLmsSyncCommandRegistered()) {
            $this->info('OK: comando lms:sync-progress registrado.');
        } else {
            $errors[] = 'Comando lms:sync-progress no registrado en Artisan.';
        }

        $bootstrapContent = $this->readFile(base_path('bootstrap/app.php'));
        $kernelContent = $this->readFile(app_path('Console/Kernel.php'));

        $scheduledInBootstrap = $this->hasLmsSyncSchedule($bootstrapContent);
        $scheduledInKernel = $this->hasLmsSyncSchedule($kernelContent);

        if (! $scheduledInBootstrap) {
            $errors[] = 'lms:sync-progress no está programado en bootstrap/app.php (scheduler activo).';
        } else {
            $this->info('OK: lms:sync-progress programado en bootstrap/app.php.');
        }

        if ($scheduledInKernel) {
            $warnings[] = 'Se detectó lms:sync-progress en app/Console/Kernel.php; mantener una sola fuente de verdad para evitar confusión.';
        }

        $warnings = array_merge(
            $warnings,
            $this->collectScheduleHardeningWarnings($bootstrapContent, $scheduledInBootstrap)
        );

        foreach ($warnings as $warning) {
            $this->warn('WARN: '.$warning);
        }

        if (! empty($errors)) {
            foreach ($errors as $error) {
                $this->error('ERROR: '.$error);
            }

            return self::FAILURE;
        }

        $this->info('Resultado: configuración operativa de sincronización LMS válida.');

        return self::SUCCESS;
    }

    private function isLmsSyncCommandRegistered(): bool
    {
        $artisanCommands = array_keys(Artisan::all());

        return in_array('lms:sync-progress', $artisanCommands, true);
    }

    private function readFile(string $path): string
    {
        if (! is_file($path)) {
            return '';
        }

        return (string) file_get_contents($path);
    }

    private function hasLmsSyncSchedule(string $content): bool
    {
        return str_contains($content, "command('lms:sync-progress')")
            || str_contains($content, 'command("lms:sync-progress")');
    }

    /**
     * @return array<int, string>
     */
    private function collectScheduleHardeningWarnings(
        string $bootstrapContent,
        bool $scheduledInBootstrap,
    ): array {
        if (! $scheduledInBootstrap) {
            return [];
        }

        $warnings = [];

        if (! str_contains($bootstrapContent, '->hourly()')) {
            $warnings[] = 'No se detectó ->hourly() para lms:sync-progress en bootstrap/app.php.';
        }

        if (! str_contains($bootstrapContent, '->withoutOverlapping()')) {
            $warnings[] = 'No se detectó ->withoutOverlapping() para lms:sync-progress en bootstrap/app.php.';
        }

        if (! str_contains($bootstrapContent, '->runInBackground()')) {
            $warnings[] = 'No se detectó ->runInBackground() para lms:sync-progress en bootstrap/app.php.';
        }

        return $warnings;
    }
}
