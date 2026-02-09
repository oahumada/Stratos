<?php

require __DIR__ . "/../vendor/autoload.php";

$app = require_once __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AbacusClient;
use App\Models\Scenario;
use App\Services\ScenarioGenerationService;

$scenario = Scenario::find(1);
if (! $scenario) {
    echo "Scenario id=1 not found\n";
    exit(1);
}

$prompt = <<<PROMPT
Por favor genera un JSON con la estructura de escenario (capabilities -> competencies -> skills) en español. Devuelve sólo JSON válido.
PROMPT;

$abacus = new AbacusClient();
try {
    // Use streaming to receive chunks and display them as they arrive.
    echo "Streaming response from Abacus:\n";
    $assembled = '';
    $result = $abacus->generateStream($prompt, ['max_tokens' => 1200, 'temperature' => 0.1, 'overrides' => ['model' => 'gpt-5']], function ($delta) use (&$assembled) {
        // print without newlines so stream appears continuous
        echo $delta;
        $assembled .= $delta;
    });
    echo "\n[stream finished]\n";

    // If the streaming method returned a parsed JSON object, use it as response
    if (is_array($result) && array_key_exists('scenario_metadata', $result)) {
        $resp = $result;
    } else {
        // try to decode the assembled text as JSON
        $decoded = json_decode($assembled, true);
        $resp = is_array($decoded) ? $decoded : ['content' => $assembled];
    }

    echo "\nAbacus parsed response:\n" . json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    // Optionally pass to ScenarioGenerationService to persist as a generation row
    if (class_exists(ScenarioGenerationService::class)) {
        $svc = app(ScenarioGenerationService::class);
        $gen = $svc->persistLLMResponse($scenario, $resp, ['prompt' => $prompt]);
        echo "Persisted generation id=" . ($gen->id ?? 'n/a') . "\n";
    }
} catch (Exception $e) {
    echo "Error calling Abacus: " . $e->getMessage() . "\n";
    exit(2);
}
