<?php

namespace App\Http\Controllers\Deployment;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class VerificationConfigurationController extends Controller
{
    public function show()
    {
        $config = $this->loadConfiguration();

        return Inertia::render('Deployment/VerificationConfiguration', [
            'currentConfig' => $config,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|in:auto_transitions,hybrid,monitoring_only',
            'autoConfig' => 'required_if:mode,auto_transitions|array',
            'hybridConfig' => 'required_if:mode,hybrid|array',
            'monitoringConfig' => 'required_if:mode,monitoring_only|array',
        ]);

        $config = [
            'deployment_mode' => $validated['mode'],
            'configured_at' => now(),
        ];

        if ($validated['mode'] === 'auto_transitions') {
            $config['auto_transitions'] = $validated['autoConfig'];
        } elseif ($validated['mode'] === 'hybrid') {
            $config['hybrid'] = $validated['hybridConfig'];
        } else {
            $config['monitoring_only'] = $validated['monitoringConfig'];
        }

        // Guardar configuración
        $this->saveConfiguration($config);

        // Actualizar .env
        $this->updateEnvFile('VERIFICATION_DEPLOYMENT_MODE', $validated['mode']);

        return redirect()->back()->with('success', 'Configuración guardada exitosamente');
    }

    public function status()
    {
        $config = $this->loadConfiguration();
        $currentPhase = env('VERIFICATION_PHASE', 'silent');

        return response()->json([
            'config' => $config,
            'current_phase' => $currentPhase,
            'mode' => env('VERIFICATION_DEPLOYMENT_MODE', 'manual'),
        ]);
    }

    private function loadConfiguration(): array
    {
        $configPath = config_path('verification-deployment.php');

        if (File::exists($configPath)) {
            return include $configPath;
        }

        return [
            'deployment_mode' => 'manual',
            'configured_at' => null,
        ];
    }

    private function saveConfiguration(array $config): void
    {
        $configPath = config_path('verification-deployment.php');
        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";

        File::put($configPath, $content);
    }

    private function updateEnvFile(string $key, string $value): void
    {
        $envFile = base_path('.env');

        if (!File::exists($envFile)) {
            File::put($envFile, "{$key}={$value}\n");
            return;
        }

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
}
