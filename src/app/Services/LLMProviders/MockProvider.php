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

        // If 'response' is explicitly null in config, fall back to the default mock payload.
        $response = Arr::get($this->config, 'response') ?? [
            'scenario_metadata' => [
                'generated_at' => $now,
                'confidence_score' => 0.75,
                'assumptions' => ['mock response for testing'],
            ],
            'capabilities' => [],
            'competencies' => [],
            'skills' => [],
            'suggested_roles' => [],
            'impact_analysis' => [],
        ];

        return [
            'response' => $response,
            'confidence' => Arr::get($response, 'scenario_metadata.confidence_score', 0.75),
            'model_version' => Arr::get($this->config, 'model_version', 'mock-1.0'),
        ];
    }

    /**
     * Simulate streamed generation by invoking $onDelta with small chunks.
     * Returns the same shape as generate().
     * This helps local/demo jobs persist chunks.
     *
     * @param string $prompt
     * @param callable $onDelta
     * @return array
     */
    public function generateStream(string $prompt, callable $onDelta): array
    {
        $res = $this->generate($prompt);
        $raw = $res['response'] ?? $res;
        $text = is_array($raw) || is_object($raw) ? json_encode($raw, JSON_UNESCAPED_UNICODE) : (string) $raw;

        // split into ~120 char chunks
        $len = strlen($text);
        $pos = 0;
        $chunkSize = 120;
        while ($pos < $len) {
            $part = substr($text, $pos, $chunkSize);
            try {
                $onDelta($part);
            } catch (\Throwable $e) {
                // ignore
            }
            $pos += $chunkSize;
            // small pause to mimic streaming
            usleep(50000); // 50ms
        }

        return $res;
    }
}
