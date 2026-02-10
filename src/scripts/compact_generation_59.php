<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$genId = 59;
$gen = \App\Models\ScenarioGeneration::find($genId);
if (! $gen) {
    echo "ScenarioGeneration $genId not found\n";
    exit(1);
}

$chunks = \App\Models\GenerationChunk::where('scenario_generation_id', $genId)
    ->orderBy('sequence')
    ->pluck('chunk')
    ->toArray();

$count = count($chunks);
$assembled = implode('', $chunks);
$encoded = base64_encode($assembled);

$meta = is_array($gen->metadata) ? $gen->metadata : [];
$meta['compacted'] = $encoded;
$meta['chunk_count'] = $count;
$meta['compacted_at'] = now()->toDateTimeString();
$gen->metadata = $meta;
$gen->save();

echo "Saved compacted for gen $genId\n";
echo "chunk_count: $count\n";
echo "compacted base64 len: " . strlen($encoded) . "\n";
echo "decoded len: " . strlen($assembled) . "\n";

return 0;
