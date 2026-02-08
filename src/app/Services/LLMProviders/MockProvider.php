<?php

namespace App\Services\LLMProviders;

use Illuminate\Support\Arr;

class MockProvider implements LLMProviderInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function generate(string $prompt): array
    {
        $now = now()->toIso8601String();

        // Allow tests to simulate rate-limits
        if (! empty($this->config['simulate_429'])) {
            $retry = isset($this->config['simulate_429_retry_after']) ? (int) $this->config['simulate_429_retry_after'] : null;
            throw new \App\Services\LLMProviders\Exceptions\LLMRateLimitException('Simulated rate limit', $retry);
        }

        $response = Arr::get($this->config, 'response', [
            'scenario_metadata' => [
                'generated_at' => $now,
                'confidence_score' => 0.75,
                'assumptions' => ['mock response for testing'],
            ],
            'capacities' => [],
            'competencies' => [],
            'skills' => [],
            'suggested_roles' => [],
            'impact_analysis' => [],
        ]);

        return [
            'response' => $response,
            'confidence' => Arr::get($response, 'scenario_metadata.confidence_score', 0.75),
            'model_version' => Arr::get($this->config, 'model_version', 'mock-1.0'),
        ];
    }
}
