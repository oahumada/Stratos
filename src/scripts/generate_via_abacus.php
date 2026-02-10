<?php

require __DIR__ . "/../vendor/autoload.php";

$app = require_once __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AbacusClient;
use App\Models\Scenario;
use App\Models\ScenarioGeneration;
use App\Models\GenerationChunk;
use App\Services\RedactionService;

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
    // Create a ScenarioGeneration row to track the streaming progress.
    $gen = ScenarioGeneration::create([
        'organization_id' => $scenario->organization_id,
        'created_by' => null,
        'prompt' => RedactionService::redactText($prompt),
        'status' => 'processing',
        'metadata' => ['provider' => 'abacus', 'model' => config('services.abacus.model')],
    ]);

    echo "Streaming response from Abacus (generation id={$gen->id}):\n";
    $assembled = '';
    $seq = 1;
    // Buffering strategy: group deltas until buffer >= 128 bytes or flush interval elapses
    $buffer = '';
    $maxBuffer = 128; // bytes
    $flushInterval = 0.20; // seconds
    $lastFlush = microtime(true);
    // Expose flush parameters to closure via globals (closure already uses globals for simplicity)
    $GLOBALS['__flush_interval__'] = $flushInterval;
    $GLOBALS['__last_flush_time__'] = $lastFlush;
    $result = $abacus->generateStream(
        $prompt,
        ['max_tokens' => 1200, 'temperature' => 0.1, 'overrides' => ['model' => 'gpt-5']],
        function ($delta, $meta = null) use (&$assembled, &$seq, $gen, &$buffer, $maxBuffer) {
        
        // Update progress metadata on the generation row if provided
        if (is_array($meta) && ! empty($meta)) {
            try {
                $md = $gen->metadata ?? [];
                $md['progress'] = $meta;
                $gen->metadata = $md;
                $gen->save();
            } catch (\Throwable $e) {
                fwrite(STDERR, "Failed to persist progress metadata: " . $e->getMessage() . "\n");
            }
        }

        // print without newlines so stream appears continuous
        echo $delta;
        $assembled .= $delta;
        // append to buffer
        $buffer .= $delta;
        // persist when buffer reaches threshold
        $now = microtime(true);
        $shouldFlushBySize = strlen($buffer) >= $maxBuffer;
        $shouldFlushByTime = ($now - ($GLOBALS['__last_flush_time__'] ?? 0)) >= ($GLOBALS['__flush_interval__'] ?? 0);
        // We don't have access to local $lastFlush in the closure via use(), so use globals set below.
        if ($shouldFlushBySize || $shouldFlushByTime) {
            try {
                GenerationChunk::create([
                    'scenario_generation_id' => $gen->id,
                    'sequence' => $seq++,
                    'chunk' => $buffer,
                ]);
            } catch (\Throwable $e) {
                fwrite(STDERR, "Failed to persist chunk: " . $e->getMessage() . "\n");
            }
            $buffer = '';
            $GLOBALS['__last_flush_time__'] = microtime(true);
        }
    }
    );
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

    // persist any remaining buffer
    if (! empty($buffer)) {
        try {
            GenerationChunk::create([
                'scenario_generation_id' => $gen->id,
                'sequence' => $seq++,
                'chunk' => $buffer,
            ]);
        } catch (\Throwable $e) {
            fwrite(STDERR, "Failed to persist final chunk: " . $e->getMessage() . "\n");
        }
    }

    // update generation row with final response
    $gen->llm_response = $resp;
    $gen->status = 'complete';
    $gen->save();

    echo "Persisted generation id=" . ($gen->id ?? 'n/a') . "\n";
} catch (Exception $e) {
    echo "Error calling Abacus: " . $e->getMessage() . "\n";
    exit(2);
}
