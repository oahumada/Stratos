<?php

use App\Models\Organizations;
use App\Models\User;
use App\Models\ScenarioGeneration;
use App\Models\Capability;
use App\Models\Competency;
use App\Models\Skill;
use App\Models\Roles;
use App\Services\ScenarioGenerationService;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function validate_import() {
    echo "--- Iniciando Validación de Importación ---\n";

    // 1. Obtener organización y usuario de prueba
    $org = Organizations::withoutGlobalScopes()->where('id', 1)->first() ?? Organizations::withoutGlobalScopes()->first();
    $user = User::withoutGlobalScopes()->where('organization_id', $org->id)->first();

    if (!$org || !$user) {
        echo "Error: Base de datos vacía (falta Org o User)\n";
        return;
    }

    echo "Usando Org: {$org->name} (ID: {$org->id}) y Usuario: {$user->email}\n";

    // 2. Leer JSON simulado
    $filePath = base_path('resources/prompt_instructions/llm_sim_response.md');
    $content = file_get_contents($filePath);
    if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
        $json = json_decode($matches[1], true);
    } else {
        $json = json_decode($content, true);
    }

    if (!$json) {
        echo "Error: No se pudo parsear el JSON de $filePath\n";
        return;
    }

    // 3. Crear el registro de generación
    $generation = ScenarioGeneration::create([
        'organization_id' => $org->id,
        'created_by' => $user->id,
        'status' => 'complete',
        'llm_response' => $json,
        'metadata' => ['simulated' => true],
        'generated_at' => now(),
    ]);

    echo "Creado ScenarioGeneration ID: {$generation->id}\n";

    // 4. Ejecutar importación
    $svc = app(ScenarioGenerationService::class);
    try {
        $report = $svc->finalizeScenarioImport($generation);
        echo "\nRESULTADO DE IMPORTACIÓN:\n";
        print_r($report['stats']);
        
        $scenarioId = $report['scenario_id'];
        echo "\nValidando registros en Scenario ID: $scenarioId\n";

        // 5. Validaciones de base de datos
        $capsCount = Capability::where('discovered_in_scenario_id', $scenarioId)->where('status', 'in_incubation')->count();
        $compsCount = Competency::where('discovered_in_scenario_id', $scenarioId)->where('status', 'in_incubation')->count();
        $skillsCount = Skill::where('discovered_in_scenario_id', $scenarioId)->where('status', 'in_incubation')->count();
        $rolesCount = Roles::where('discovered_in_scenario_id', $scenarioId)->where('status', 'in_incubation')->count();

        echo "- Capacidades en incubación: $capsCount\n";
        echo "- Competencias en incubación: $compsCount\n";
        echo "- Skills en incubación: $skillsCount\n";
        echo "- Roles en incubación: $rolesCount\n";

        if ($capsCount > 0 && $rolesCount > 0) {
            echo "\n✅ VALIDACIÓN EXITOSA: Los registros fueron creados con el estado correcto.\n";
        } else {
            echo "\n❌ VALIDACIÓN FALLIDA: Algunos contadores están en cero.\n";
        }

    } catch (\Throwable $e) {
        echo "❌ ERROR DURANTE LA IMPORTACIÓN: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString();
    }
}

validate_import();
