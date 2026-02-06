<?php

namespace App\Services;

class LLMClient
{
    public function generate(string $prompt): array
    {
        // Default behavior: mock provider for local development/tests
        $provider = config('llm.provider', 'mock');

        if ($provider === 'mock') {
            return [
                'response' => [
                    'scenario_metadata' => [
                        'generated_at' => now()->toIso8601String(),
                        'confidence_score' => 0.75,
                        'assumptions' => ['mock response for testing'],
                    ],
                    'capacities' => [],
                    'competencies' => [],
                    'skills' => [],
                    'suggested_roles' => [],
                    'impact_analysis' => [],
                ],
                'confidence' => 0.75,
                'model_version' => 'mock-1.0'
            ];
        }

        // TODO: implement real provider (OpenAI, Anthropic, etc.) using Guzzle
        throw new \RuntimeException('No LLM provider configured.');
    }
}
