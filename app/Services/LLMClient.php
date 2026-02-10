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

    /**
     * Streamed generation: call provider->generateStream if available.
     * The $onDelta callable will be invoked with string deltas as they arrive.
     * If provider does not support streaming, fallback to calling generate()
     * and invoke $onDelta once with the full text.
     * Returns the provider result array (same shape as generate()).
     *
     * @param string $prompt
     * @param callable $onDelta function(string $delta): void
     * @return array
     */
    public function generateStream(string $prompt, callable $onDelta): array
    {
        if (method_exists($this->provider, 'generateStream')) {
            return $this->provider->generateStream($prompt, $onDelta);
        }

        // Fallback: non-streaming provider — call generate and emit a single delta
        $res = $this->generate($prompt);
        $raw = $res['response'] ?? $res;
        // If array/object, serialize to JSON string for client-side parsing
        if (is_array($raw) || is_object($raw)) {
            $text = json_encode($raw, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($raw)) {
            $text = $raw;
        } else {
            $text = (string) $raw;
        }
        // emit as a single chunk
        try {
            $onDelta($text);
        } catch (\Throwable $e) {
            // ignore callback failures — provider result is still returned
        }

        return $res;
    }
}
