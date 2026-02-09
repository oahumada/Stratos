<?php
// One-off script to create and enqueue the demo generation using existing DB user
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
// Boot minimal kernel to have app services available
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Organizations;
use App\Services\ScenarioGenerationService;

$user = User::first();
if (! $user) {
    echo "No user found in DB. Create a user first.\n";
    exit(1);
}

$org = Organizations::find($user->organization_id);
if (! $org) {
    echo "User has no organization_id or organization not found.\n";
    exit(1);
}

$svc = $app->make(ScenarioGenerationService::class);

$payload = [
    'company_name' => 'Acme Labs S.A.',
    'industry' => 'Tecnología / Software',
    'sub_industry' => 'Plataformas de datos e IA',
    'company_size' => 450,
    'geographic_scope' => 'LatAm',
    'organizational_cycle' => 'Anual',
    'current_challenges' => 'Baja adopción de modelos de IA, falta de datos etiquetados y equipos con experiencia limitada en ML.',
    'current_capabilities' => 'Equipos de data engineering básicos, pipelines de datos internos y experiencia en integración de APIs.',
    'current_gaps' => 'Carencia de modelos generativos productivos, no hay prácticas MLOps maduras.',
    'current_roles_count' => 120,
    'has_formal_competency_model' => false,
    'strategic_goal' => 'Adoptar IA generativa para mejorar productividad y automatizar tareas de atención al cliente y documentación técnica.',
    'target_markets' => 'Mercado latinoamericano; clientes enterprise y pymes tecnológicas',
    'expected_growth' => 'Aumento del 25% en eficiencia operativa en 12 meses',
    'transformation_type' => ['automation', 'innovation'],
    'key_initiatives' => 'Pilotos de chatbots generativos, generación automática de documentación, integración con soporte y herramientas internas.',
    'budget_level' => 'Medio',
    'talent_availability' => 'Limitada internamente; se requiere contratación y formación.',
    'training_capacity' => 'Moderada; equipo de L&D con programas trimestrales.',
    'technology_maturity' => 'Madurez media: infra de datos existente pero falta orquestación y modelos.',
    'critical_constraints' => 'Regulación de datos y privacidad, riesgo de calidad de respuestas generadas.',
    'time_horizon' => '12 meses',
    'urgency_level' => 'Alta',
    'milestones' => '1) Piloto interno (3 meses); 2) Integración con soporte (6 meses); 3) Escalado productivo (12 meses)',
    'instruction' => "Por favor, genera UN SOLO objeto JSON que describa el escenario generado con las siguientes claves: scenario_metadata, capabilities, competencies, skills, suggested_roles, impact_analysis, confidence_score, assumptions. Devuelve únicamente JSON válido sin texto adicional.",
];

try {
    $composed = $svc->composePromptWithInstruction($payload, $user, $org, 'es', null);
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "Instruction validation failed: ";
    print_r($e->errors());
    exit(1);
}

$prompt = $composed['prompt'] ?? '';
$meta = ['initiator' => $user->id, 'demo' => true, 'used_instruction' => $composed['instruction'] ?? null];

$generation = $svc->enqueueGeneration($prompt, $org->id, $user->id, $meta);

echo "Enqueued generation id: {$generation->id}\n";

// Poll for status up to timeout
$start = time();
$timeout = 180; // seconds
while (true) {
    $g = \App\Models\ScenarioGeneration::find($generation->id);
    if ($g->status === 'complete') {
        echo "Generation complete. ID: {$g->id}\n";
        echo "LLM response (trimmed):\n";
        $resp = $g->llm_response;
        if (is_array($resp)) {
            echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo substr((string)$g->llm_response, 0, 2000) . "\n";
        }
        break;
    }
    if (time() - $start > $timeout) {
        echo "Timeout waiting for generation to complete. Current status: {$g->status}\n";
        break;
    }
    echo "Waiting for completion... status: {$g->status}\n";
    sleep(3);
}

return 0;
