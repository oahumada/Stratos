<?php

use App\Services\LLMProviders\Exceptions\LLMRateLimitException;
use App\Services\LLMProviders\MockProvider;
use Tests\TestCase;

uses(TestCase::class);

it('returns a default mock response when no simulate flags', function () {
    $provider = new MockProvider([]);
    $res = $provider->generate('prompt');

    expect($res)->toBeArray();
    expect($res['response'])->toBeArray();
    expect($res['model_version'])->toBeString();
});

it('throws LLMRateLimitException when simulate_429 is true', function () {
    $this->expectException(LLMRateLimitException::class);

    $provider = new MockProvider(['simulate_429' => true, 'simulate_429_retry_after' => 2]);
    $provider->generate('prompt');
});
