<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class VerificationConfigureCommand extends Command
{
    protected $signature = 'verification:configure';

    protected $description = 'Configure verification deployment options interactively';

    public function handle(): int
    {
        $this->line('');
        $this->info('╔═══════════════════════════════════════════════════════════════╗');
        $this->info('║        CONFIGURACIÓN DEL SISTEMA DE VERIFICACIÓN             ║');
        $this->info('╚═══════════════════════════════════════════════════════════════╝');
        $this->line('');

        // 1. Seleccionar modo de despliegue
        $deploymentMode = $this->choice(
            '¿Qué modo deseas usar?',
            [
                '🤖 Opción 1: Auto-Transitions (autopiloto automático)',
                '⚙️ Opción 3: Hybrid (manual + insights inteligentes)',
                '📊 Opción 2: Solo monitoreo (recolectar datos)',
            ],
            0
        );

        $config = [
            'deployment_mode' => match ($deploymentMode) {
                0 => 'auto_transitions',
                1 => 'hybrid',
                2 => 'monitoring_only',
            },
            'configured_at' => now(),
        ];

        // 2. Configurar según el modo
        $this->line('');
        $this->info('⚙️ CONFIGURANDO PARÁMETROS...');
        $this->line('');

        if ($config['deployment_mode'] === 'auto_transitions') {
            $this->configureAutoTransitions($config);
        } elseif ($config['deployment_mode'] === 'hybrid') {
            $this->configureHybrid($config);
        } else {
            $this->configureMonitoringOnly($config);
        }

        // 3. Guardar configuración
        $configPath = config_path('verification-deployment.php');
        $this->saveConfiguration($config, $configPath);

        // 4. Mostrar resumen
        $this->displaySummary($config);

        return self::SUCCESS;
    }

    private function configureAutoTransitions(array &$config): void
    {
        $this->info('📊 OPCIÓN 1: AUTO-TRANSITIONS');
        $this->line('El sistema cambiará automáticamente de fase según métricas.');
        $this->line('');

        // Error rate threshold para Phase 2
        $errorRatePhase2 = (int) $this->ask(
            'Umbral error_rate para cambiar a Phase 2 (%)',
            '15'
        );

        // Retry rate threshold para Phase 4
        $retryRatePhase4 = (int) $this->ask(
            'Umbral retry_rate para cambiar a Phase 4 (%)',
            '10'
        );

        // Intervalo de verificación
        $checkInterval = (int) $this->ask(
            'Intervalo de verificación (minutos)',
            '60'
        );

        // Ventana de datos
        $dataWindow = (int) $this->ask(
            'Ventana de análisis de datos (horas)',
            '24'
        );

        // Notificaciones
        $enableNotifications = $this->confirm('¿Habilitar notificaciones de cambios?', true);

        $config['auto_transitions'] = [
            'error_rate_threshold_phase2' => $errorRatePhase2,
            'retry_rate_threshold_phase4' => $retryRatePhase4,
            'check_interval_minutes' => $checkInterval,
            'data_window_hours' => $dataWindow,
            'enable_notifications' => $enableNotifications,
            'notification_channel' => $enableNotifications ? $this->ask('Canal (log, slack, email)', 'log') : null,
        ];
    }

    private function configureHybrid(array &$config): void
    {
        $this->info('⚙️ OPCIÓN 3: HYBRID (Manual + Insights)');
        $this->line('Recolecta automáticamente pero tú decides cuándo cambiar.');
        $this->line('');

        // Intervalo de recolección de métricas
        $metricsInterval = (int) $this->ask(
            'Cada cuántos minutos recolectar métricas?',
            '60'
        );

        // Alertas si error_rate sube
        $alertThreshold = (int) $this->ask(
            'Alertar si error_rate sube más del (%) en 1 hora?',
            '20'
        );

        // Sugerencias automáticas
        $enableSuggestions = $this->confirm(
            '¿Mostrar sugerencias automáticas cuando sea momento de cambiar?',
            true
        );

        // Dashboard de métricas
        $enableDashboard = $this->confirm(
            '¿Habilitar dashboard web de métricas?',
            true
        );

        $config['hybrid'] = [
            'metrics_collection_interval' => $metricsInterval,
            'alert_threshold_percent' => $alertThreshold,
            'enable_suggestions' => $enableSuggestions,
            'enable_web_dashboard' => $enableDashboard,
            'suggestion_channel' => $enableSuggestions ? $this->ask('Dónde enviar sugerencias (cli, web, both)', 'both') : null,
        ];
    }

    private function configureMonitoringOnly(array &$config): void
    {
        $this->info('📊 OPCIÓN 2: SOLO MONITOREO');
        $this->line('Recolecta datos sin tomar decisiones automáticas.');
        $this->line('');

        $metricsInterval = (int) $this->ask(
            'Cada cuántos minutos recolectar métricas?',
            '60'
        );

        $retentionDays = (int) $this->ask(
            'Cuántos días retener métricas?',
            '30'
        );

        $config['monitoring_only'] = [
            'metrics_collection_interval' => $metricsInterval,
            'metrics_retention_days' => $retentionDays,
        ];
    }

    private function saveConfiguration(array $config, string $path): void
    {
        $content = "<?php\n\nreturn ".var_export($config, true).";\n";
        File::put($path, $content);

        // También actualizar .env
        $this->updateEnv('VERIFICATION_DEPLOYMENT_MODE', $config['deployment_mode']);
    }

    private function updateEnv(string $key, string $value): void
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        if (strpos($envContent, $key) !== false) {
            $envContent = preg_replace(
                "/{$key}=.*/",
                "{$key}={$value}",
                $envContent
            );
        } else {
            $envContent .= "\n{$key}={$value}\n";
        }

        File::put($envFile, $envContent);
    }

    private function displaySummary(array $config): void
    {
        $this->line('');
        $this->info('╔═══════════════════════════════════════════════════════════════╗');
        $this->info('║                    CONFIGURACIÓN GUARDADA                    ║');
        $this->info('╚═══════════════════════════════════════════════════════════════╝');
        $this->line('');

        $mode = $config['deployment_mode'];

        match ($mode) {
            'auto_transitions' => $this->displayAutoTransitionsSummary($config),
            'hybrid' => $this->displayHybridSummary($config),
            'monitoring_only' => $this->displayMonitoringOnlySummary($config),
        };

        $this->line('');
        $this->info('✅ Configuración guardada en: config/verification-deployment.php');
        $this->info('✅ Variables de entorno actualizadas en: .env');

        $this->line('');
        $this->comment('📚 Próximos pasos:');
        $this->comment('  1. Ejecuta: php artisan config:cache');
        $this->comment('  2. Ver estado: ./scripts/verification-phase-deploy.sh status');
        $this->comment('  3. Comenzar: ./scripts/verification-phase-deploy.sh silent');

        $this->line('');
    }

    private function displayAutoTransitionsSummary(array $config): void
    {
        $settings = $config['auto_transitions'];

        $this->line('🤖 MODO: AUTO-TRANSITIONS (Autopiloto)');
        $this->line('');
        $this->table(
            ['Parámetro', 'Valor'],
            [
                ['Error rate para Phase 2', $settings['error_rate_threshold_phase2'].'%'],
                ['Retry rate para Phase 4', $settings['retry_rate_threshold_phase4'].'%'],
                ['Verificación cada', $settings['check_interval_minutes'].' minutos'],
                ['Ventana de datos', $settings['data_window_hours'].' horas'],
                ['Notificaciones', $settings['enable_notifications'] ? 'Sí ('.$settings['notification_channel'].')' : 'No'],
            ]
        );
    }

    private function displayHybridSummary(array $config): void
    {
        $settings = $config['hybrid'];

        $this->line('⚙️ MODO: HYBRID (Manual + Insights)');
        $this->line('');
        $this->table(
            ['Parámetro', 'Valor'],
            [
                ['Recolectar métricas cada', $settings['metrics_collection_interval'].' minutos'],
                ['Alertar si sube más del', $settings['alert_threshold_percent'].'% en 1h'],
                ['Sugerencias automáticas', $settings['enable_suggestions'] ? 'Sí' : 'No'],
                ['Dashboard web', $settings['enable_web_dashboard'] ? 'Sí' : 'No'],
            ]
        );
    }

    private function displayMonitoringOnlySummary(array $config): void
    {
        $settings = $config['monitoring_only'];

        $this->line('📊 MODO: SOLO MONITOREO');
        $this->line('');
        $this->table(
            ['Parámetro', 'Valor'],
            [
                ['Recolectar métricas cada', $settings['metrics_collection_interval'].' minutos'],
                ['Retener datos por', $settings['metrics_retention_days'].' días'],
            ]
        );
    }
}
