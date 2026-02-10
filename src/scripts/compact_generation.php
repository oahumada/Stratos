<?php
// Locate composer autoload and Laravel bootstrap in common locations and include them.
$autoloadCandidates = [
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
];

$autoloadFound = false;
foreach ($autoloadCandidates as $candidate) {
    if (file_exists($candidate)) {
        require_once $candidate;
        $autoloadFound = true;
        break;
    }
}

if (! $autoloadFound) {
    fwrite(STDERR, "Error: Could not find vendor/autoload.php. Run 'composer install' in project root or in src/.\n");
    exit(1);
}

$bootstrapCandidates = [
    __DIR__ . '/../../bootstrap/app.php',
    __DIR__ . '/../bootstrap/app.php',
    __DIR__ . '/../../../bootstrap/app.php',
];

$appFound = false;
foreach ($bootstrapCandidates as $b) {
    if (file_exists($b)) {
        $app = require $b;
        $appFound = true;
        break;
    }
}

if (! $appFound) {
    fwrite(STDERR, "Error: Could not find bootstrap/app.php. Are you running this from the project workspace?\n");
    exit(1);
}

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$genId = 70;
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
