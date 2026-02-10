<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ScenarioGeneration;
use App\Models\GenerationChunk;

$g = ScenarioGeneration::find(59);
if (! $g) {
    echo "Generation 59 not found\n";
    exit(1);
}
$chunks = GenerationChunk::where('scenario_generation_id', 59)->count();
echo "ID: {$g->id}\n";
echo "STATUS: {$g->status}\n";
echo "CHUNKS: {$chunks}\n";
$meta = is_array($g->metadata) ? $g->metadata : [];
echo "metadata keys: ".implode(',', array_keys($meta))."\n";
echo "chunk_count metadata: ".($meta['chunk_count'] ?? 'null')."\n";
if (! empty($meta['compacted'])) {
    echo "compacted base64 len: ".strlen($meta['compacted'])."\n";
    $decoded = base64_decode($meta['compacted']);
    echo "decoded len: ".strlen($decoded)."\n";
} else {
    echo "no compacted in metadata\n";
}
echo "llm_response present: ".(empty($g->llm_response)?'no':'yes')."\n";
if (! empty($g->llm_response) && is_array($g->llm_response)) {
    echo "llm_response preview: ".json_encode(array_slice($g->llm_response,0,3))."\n";
}

echo "metadata JSON: \n".json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n";
