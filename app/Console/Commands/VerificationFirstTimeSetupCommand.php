<?php

namespace App\Console\Commands;

use App\Services\VerificationAuditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * VerificationFirstTimeSetupCommand - Guided wizard for first-time configuration
 */
class VerificationFirstTimeSetupCommand extends Command
{
    protected $signature = 'verification:first-time-setup';

    protected $description = 'Interactive wizard for verification system first-time setup';

    public function handle(VerificationAuditService $auditService): int
    {
        $this->info('🚀 Verification System - First Time Setup Wizard');
        $this->line('');

        if ($this->confirmAlreadyConfigured()) {
            return Command::SUCCESS;
        }

        $mode = $this->choice(
            'Choose deployment mode:',
            [
                'auto_transitions' => 'Auto-Transitions (Recommended - Full Automation)',
                'hybrid' => 'Hybrid Mode (Semi-Automatic with Approvals)',
                'monitoring_only' => 'Monitoring Only (Manual Changes)',
            ],
            0
        );

        $config = ['deployment_mode' => array_keys(['auto_transitions', 'hybrid', 'monitoring_only'])[$mode] ?? 'auto_transitions'];

        if ($config['deployment_mode'] === 'auto_transitions') {
            $this->configureAutoTransitions($config);
        } elseif ($config['deployment_mode'] === 'hybrid') {
            $this->configureHybrid($config);
        }

        // Enable notifications
        $config['notifications_enabled'] = $this->confirm('Enable notifications?', true);

        // Save configuration
        $this->saveConfiguration($config);

        $this->info('✅ Setup complete!');
        $this->line('');
        $this->table(['Setting', 'Value'], [
            ['Mode', $config['deployment_mode']],
            ['Notifications', $config['notifications_enabled'] ? 'Enabled' : 'Disabled'],
        ]);

        return Command::SUCCESS;
    }

    private function confirmAlreadyConfigured(): bool
    {
        $configFile = config_path('verification-deployment.php');

        if (File::exists($configFile)) {
            $config = include $configFile;

            if (isset($config['deployment_mode']) && $config['deployment_mode'] !== 'monitoring_only') {
                $this->warn('⚠️  System already configured!');

                return ! $this->confirm('Run setup anyway?', false);
            }
        }

        return false;
    }

    private function configureAutoTransitions(array &$config): void
    {
        $this->info('Configure Auto-Transitions');
        $errorRate = $this->ask('Error rate threshold (%)', 80);
        $config['auto_transitions'] = [
            'error_rate_threshold' => (int) $errorRate,
            'retry_rate_threshold' => 50,
            'check_interval_minutes' => 30,
            'data_window_hours' => 24,
        ];
    }

    private function configureHybrid(array &$config): void
    {
        $this->info('Configure Hybrid Mode');
        $config['hybrid'] = [
            'metrics_collection_interval' => 15,
            'alert_threshold' => 30,
            'suggestions' => true,
        ];
    }

    private function saveConfiguration(array $config): void
    {
        $timestamp = now()->toIso8601String();
        $orgId = auth()->user()?->organization_id ?? 1;

        $php = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        File::put(config_path('verification-deployment.php'), $php);

        $this->line("Configuration saved to config/verification-deployment.php");
    }
}
