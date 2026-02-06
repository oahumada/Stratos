<?php

use App\Jobs\GenerateScenarioFromLLMJob;
use App\Models\ScenarioGeneration;
use App\Services\LLMClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('processes generation job and persists llm response', function () {
    // create a generation record
    $generation = ScenarioGeneration::create([
        'organization_id' => 1,
        'created_by' => 1,
        'prompt' => 'test prompt',
        'status' => 'queued',
    ]);

    // create a mock LLMClient that returns a deterministic response
    $mock = Mockery::mock(LLMClient::class);
    $mock->shouldReceive('generate')->once()->with('test prompt')->andReturn([
        'response' => ['foo' => 'bar'],
        'confidence' => 0.9,
        'model_version' => 'mock-x',
    ]);

    // bind the mock into the container
    $this->app->instance(LLMClient::class, $mock);

    // run the job handler synchronously
    $job = new GenerateScenarioFromLLMJob($generation->id);
    $job->handle(app(LLMClient::class));

    $generation->refresh();

    expect($generation->status)->toBe('complete');
    expect(is_array($generation->llm_response))->toBeTrue();
    expect(array_key_exists('foo', $generation->llm_response))->toBeTrue();
    expect($generation->confidence_score)->toBe(0.9);
    expect($generation->model_version)->toBe('mock-x');
    expect($generation->generated_at)->not->toBeNull();
});
