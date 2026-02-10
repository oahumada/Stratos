<?php

use App\Jobs\GenerateScenarioFromLLMJob;
use App\Models\ScenarioGeneration;
use App\Models\GenerationChunk;
use App\Services\AbacusClient;
use App\Services\LLMClient;

it('persists chunks and updates metadata.progress when Abacus streams', function () {
    $gen = ScenarioGeneration::create([
        'organization_id' => 1,
        'prompt' => 'test prompt',
        'status' => 'queued',
        'metadata' => ['provider' => 'abacus'],
    ]);

    $mock = new class extends AbacusClient {
        public function __construct() {}
        public function generateStream(string $prompt, array $options = [], ?callable $onChunk = null): ?array {
            $deltas = ['first ', 'second ', 'third'];
            $i = 1;
            foreach ($deltas as $d) {
                $meta = ['received_chunks' => $i, 'total_chunks' => count($deltas), 'percent' => intval(($i / count($deltas)) * 100)];
                if ($onChunk) { call_user_func($onChunk, $d, $meta); }
                $i++;
            }
            return ['response' => json_encode(['foo' => 'bar']), 'confidence' => 0.7, 'model_version' => 'abacus-mock'];
        }
    };

    $job = new GenerateScenarioFromLLMJob($gen->id);
    $job->handle(app(LLMClient::class), $mock);

    $gen->refresh();
    expect($gen->status)->toBe('complete');
    expect(is_array($gen->llm_response))->toBeTrue();

    $chunks = GenerationChunk::where('scenario_generation_id', $gen->id)->orderBy('sequence')->get();
    expect($chunks->count())->toBeGreaterThanOrEqual(1);
    expect($gen->metadata['progress']['received_chunks'])->toBe(3);
    expect($gen->metadata['progress']['total_chunks'])->toBe(3);
});
