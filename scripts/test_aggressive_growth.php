<?php

use App\Models\Scenario;
use App\Services\ScenarioGenerationService;
use Illuminate\Support\Facades\Auth;

// Simular login de usuario admin
$user = \App\Models\User::find(1);
Auth::login($user);

echo "🚀 Iniciando simulación de Escenario: 'Crecimiento Agresivo en Expansión Digital'\n";

$data = [
    'company_name' => 'Stratos Bank Corp',
    'industry' => 'Financial Services',
    'sub_industry' => 'Fintech',
    'company_size' => 1200,
    'geographic_scope' => 'global',
    'organizational_cycle' => 'expansion',
    'current_challenges' => 'Migración lenta a la nube, falta de arquitectos de microservicios, alta rotación en roles senior.',
    'strategic_goal' => 'Liderar el mercado de banca abierta en LATAM en 18 meses, creciendo la base de usuarios en 300%.',
    'expected_growth' => 'high',
    'transformation_type' => ['digital', 'cultural'],
    'budget_level' => 'high',
    'time_horizon' => '18_months',
    'urgency_level' => 'high',
];

$service = app(ScenarioGenerationService::class);
$org = \App\Models\Organizations::find($user->organization_id);

echo "📝 Preparando prompt y solicitando Blueprint al LLM (esto puede tardar 20-40s)...\n";

try {
    // 1. Preparar el prompt
    $prompt = $service->preparePrompt($data, $user, $org);

    // 2. Encolar la generación (usamos 'mock' para esta demo ya que el Smart Mock detectará el keyword)
    $generation = $service->enqueueGeneration($prompt, $org->id, $user->id, [
        'provider' => 'mock',
        'company_name' => $data['company_name'],
        'language' => 'es',
    ]);
    echo '✅ Generación encolada con ID: '.$generation->id.". Usando Smart Simulation para el escenario de expansión...\n";

    // 2. Poll para esperar a que termine
    $maxAttempts = 120; // Damos más tiempo para una generación real
    $attempt = 0;
    while ($generation->status !== 'complete' && $generation->status !== 'failed' && $attempt < $maxAttempts) {
        $attempt++;
        sleep(3);
        $generation->refresh();
        echo '⏳ Estado actual: '.$generation->status." (intento $attempt/$maxAttempts)\n";
    }

    if ($generation->status !== 'complete') {
        throw new \Exception('La generación falló o excedió el tiempo de espera. Estado: '.$generation->status.' Error: '.($generation->metadata['message'] ?? 'Desconocido'));
    }

    echo "✨ Blueprint real recibido con éxito!\n";

    // 3. Aceptar la generación para crear el escenario real
    echo "💡 Aceptando blueprint y creando escenario (con import=true)...\n";
    $request = new \Illuminate\Http\Request;
    $request->merge(['import' => 'true']);
    // Forzamos el usuario en el request para evitar 'Unauthenticated'
    $request->setUserResolver(fn () => $user);

    // Aseguramos que el flag de import esté activo para la prueba
    config(['features.import_generation' => true]);

    $response = app(\App\Http\Controllers\Api\ScenarioGenerationController::class)->accept($request, $generation->id);
    $responseData = json_decode($response->getContent(), true);

    if (isset($responseData['import_errors'])) {
        echo "❌ ERRORES DE IMPORTACIÓN:\n";
        print_r($responseData['import_errors']);
    }

    if (empty($responseData['success'])) {
        throw new \Exception('Error al aceptar la generación: '.print_r($responseData, true));
    }

    $scenarioId = $responseData['data']['id'];
    $scenario = Scenario::find($scenarioId);
    echo "✅ Escenario Creado: ID $scenarioId - ".$scenario->name."\n";

    $scenarioController = app(\App\Http\Controllers\Api\ScenarioController::class);

    // 4. Derivar Skills para los nuevos roles (esto vincula roles -> competencias -> skills)
    echo "🏗️ Derivando skills para los nuevos roles...\n";
    $scenarioController->deriveAllSkills($scenarioId);

    // 5. Disparar Análisis de Gaps (en modo sincrónico)
    echo "🔍 Lanzando Análisis de brechas (Gap Analysis - Sincrónico)...\n";
    $gaps = \App\Models\ScenarioRoleCompetency::where('scenario_id', $scenarioId)->get();
    foreach ($gaps as $gap) {
        \App\Jobs\AnalyzeTalentGap::dispatchSync($gap->id);
    }

    // 6. Generar Estrategias Sugeridas
    echo "🛠️ Generando estrategias de cierre de brechas...\n";
    $refreshRequest = new \Illuminate\Http\Request;
    $strategiesResponse = $scenarioController->refreshSuggestedStrategies($scenarioId, $refreshRequest);
    echo "✅ Estrategias generadas.\n";

    // 7. Consultar Impacto Final
    echo "📊 Consultando Impacto Analytics...\n";
    $impactResponse = $scenarioController->getImpact($scenarioId);
    $impactData = json_decode($impactResponse->getContent(), true);

    echo "\n--- RESULTADOS DEL ESCENARIO AGRESIVO ---\n";
    echo 'KPI Cierre de Brechas: '.($impactData['data']['gap_closure'] ?? 'N/A')."%\n";
    echo 'KPI ROI Estimado: '.($impactData['data']['estimated_roi'] ?? 'N/A')."x\n";
    echo 'TFC (Tiempo Plena Capacidad): '.($impactData['data']['time_to_fill'] ?? 'N/A')." semanas\n";
    echo 'Resumen: '.($impactData['data']['summary'] ?? 'N/A')."\n";
    echo "-----------------------------------------\n\n";

    echo "🎉 Prueba completada satisfactoriamente.\n";

} catch (\Exception $e) {
    echo '❌ ERROR: '.$e->getMessage()."\n";
    if (isset($generation)) {
        echo 'Metadata de error: '.print_r($generation->metadata, true)."\n";
    }
}
