<?php

require __DIR__ . "/../vendor/autoload.php";

$app = require_once __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Scenario;
use App\Models\ScenarioGeneration;

$scenario = Scenario::find(1);
if (! $scenario) {
    echo "Scenario id=1 not found\n";
    exit(1);
}

$jsonPath = __DIR__ . "/../tests/fixtures/llm/valid_generation_response.json";
if (! file_exists($jsonPath)) {
    echo "Fixture not found: $jsonPath\n";
    exit(1);
}

$payload = json_decode(file_get_contents($jsonPath), true);
if (! is_array($payload)) {
    echo "Invalid JSON payload\n";
    exit(1);
}

$generation = new ScenarioGeneration();
$generation->llm_response = $payload;

$importer = app(\App\Services\ScenarioGenerationImporter::class);

try {
    $report = $importer->importGeneration($scenario, $generation);
    echo "Import report:\n" . json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} catch (\Throwable $e) {
    echo "Importer threw: " . $e->getMessage() . "\n";
    exit(2);
}

