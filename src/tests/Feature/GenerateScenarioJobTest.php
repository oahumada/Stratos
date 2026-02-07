<?php

use App\Jobs\GenerateScenarioFromLLMJob;
use App\Models\ScenarioGeneration;
use App\Services\LLMProviders\Exceptions\LLMRateLimitException;
use App\Services\LLMClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

it('releases job on rate limit and keeps generation in processing when attempts remain', function () {
    // create a generation record
    $gen = ScenarioGeneration::create(['organization_id' => 1, 'prompt' => 'p', 'status' => 'queued']);

    // Create a test LLMClient that throws rate limit
    $testClient = new class extends LLMClient {
        public function __construct()
        {
        }
        public function generate(string $prompt): array
        {
            throw new LLMRateLimitException('simulated', 1);
        }
    };

    // Create anonymous job with attempts() = 0
    $job = new class ($gen->id) extends GenerateScenarioFromLLMJob {
        public function attempts(): int
        {
            return 0; }
    };

    $job->handle($testClient);

    $gen->refresh();
    expect($gen->status)->toBe('processing');
});

it('marks generation failed after max attempts exceeded', function () {
    $gen = ScenarioGeneration::create(['organization_id' => 1, 'prompt' => 'p', 'status' => 'queued']);

    $testClient = new class extends LLMClient {
        public function __construct()
        {
        }
        public function generate(string $prompt): array
        {
            throw new LLMRateLimitException('simulated', 1);
        }
    };

    // attempts() returns high value to simulate exhausted retries
    $job = new class ($gen->id) extends GenerateScenarioFromLLMJob {
        public function attempts(): int
        {
            return 6; }
    };

    $job->handle($testClient);

    $gen->refresh();
    expect($gen->status)->toBe('failed');
    expect($gen->metadata['error'])->toBe('rate_limit_exceeded');
});
