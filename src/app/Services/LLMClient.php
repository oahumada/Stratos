<?php

namespace App\Services;

use App\Services\LLMProviders\LLMProviderInterface;

class LLMClient
{
    protected LLMProviderInterface $provider;

    public function __construct()
    {
        $providerKey = config('llm.provider', env('LLM_PROVIDER', 'mock'));

        switch ($providerKey) {
            case 'openai':
                $this->provider = new LLMProviders\OpenAIProvider(config('llm.openai', []));
                break;
            case 'mock':
            default:
                $this->provider = new LLMProviders\MockProvider(config('llm.mock', []));
                break;
        }
    }

    /**
     * Generate a response from the configured provider.
     * Returns an array with keys: response (array), confidence (float), model_version (string)
     */
    public function generate(string $prompt): array
    {
        return $this->provider->generate($prompt);
    }
}
