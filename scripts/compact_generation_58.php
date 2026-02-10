<?php
// Assemble chunks for generation id=58, store metadata.compacted and chunk_count, print sizes
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ScenarioGeneration;
use App\Models\GenerationChunk;

$genId = 58;
$gen = ScenarioGeneration::find($genId);
if (! $gen) {
    echo "Generation {$genId} not found\n";
    exit(1);
}

$meta = is_array($gen->metadata) ? $gen->metadata : (array) ($gen->metadata ?? []);
if (! empty($meta['compacted'])) {
    $decoded = base64_decode($meta['compacted']);
    $sizeBase64 = strlen($meta['compacted']);
    $sizeDecoded = strlen($decoded);
    echo "Already compacted for generation {$genId}\n";
    echo "chunks_count_in_metadata: " . ($meta['chunk_count'] ?? 'null') . "\n";
    echo "compacted base64 length: {$sizeBase64}\n";
    echo "decoded JSON length: {$sizeDecoded}\n";
    exit(0);
}

$chunks = GenerationChunk::where('scenario_generation_id', $genId)->orderBy('sequence')->pluck('chunk')->toArray();
$count = count($chunks);
if ($count === 0) {
    echo "No chunks found for generation {$genId} (count=0)\n";
    exit(1);
}

$assembled = implode('', $chunks);
$encoded = base64_encode($assembled);

$meta['compacted'] = $encoded;
$meta['chunk_count'] = $count;
$meta['compacted_at'] = now()->toDateTimeString();
$gen->metadata = $meta;
$gen->save();

echo "Compacted and saved for generation {$genId}\n";
echo "chunk_count: {$count}\n";
echo "compacted base64 length: " . strlen($encoded) . "\n";
echo "decoded JSON length: " . strlen($assembled) . "\n";

$sample = substr($assembled, 0, 200);
echo "sample (first 200 chars):\n" . $sample . "\n";
