<?php

// Usage: php src/scripts/dump_generation_llm.php 69

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ScenarioGeneration;

$id = $argv[1] ?? null;
if (! $id) {
    echo "Usage: php src/scripts/dump_generation_llm.php <generation_id>\n";
    exit(1);
}

$gen = ScenarioGeneration::find($id);
if (! $gen) {
    echo "ScenarioGeneration id={$id} not found\n";
    exit(2);
}

$llm = $gen->llm_response;
if (! is_array($llm) && ! is_object($llm)) {
    echo "llm_response is not JSON/array\n";
    var_export($llm);
    exit(3);
}

echo json_encode($llm, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
