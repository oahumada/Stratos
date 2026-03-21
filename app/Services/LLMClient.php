<?php

namespace App\Services;

use App\Services\LLMProviders\LLMProviderInterface;
use App\Traits\LogsPrompts;

class LLMClient
{
    use LogsPrompts;

    protected LLMProviderInterface $provider;

    public function __construct()
    {
        $providerKey = config('stratos.llm.default_provider', 'mock');

        switch ($providerKey) {
            case 'openai':
                $this->provider = new LLMProviders\OpenAIProvider(config('llm.openai', []));
                break;
            case 'intel':
                $this->provider = new \App\Services\LLMProviders\IntelProvider(config('services.python_intel', []));
                break;
            case 'mock':
            default:
                $this->provider = new LLMProviders\MockProvider(config('llm.mock', []));
                break;
        }
    }

    /**
     * Generate a response from the configured provider.
     * Logs are PII-safe using LogsPrompts trait.
     * Returns an array with keys: response (array), confidence (float), model_version (string)
     */
    public function generate(string $prompt): array
    {
        try {
            $result = $this->provider->generate($prompt);

            // Log with PII protection
            $this->logPrompt($prompt, $result, [
                'model' => $this->provider::class,
                'provider' => config('stratos.llm.default_provider', 'unknown'),
            ]);

            return $result;
        } catch (\Throwable $e) {
            // Log error with PII protection
            $this->logPromptError($prompt, $e, [
                'model' => $this->provider::class,
                'provider' => config('stratos.llm.default_provider', 'unknown'),
            ]);

            throw $e;
        }
    }

    /**
     * Streamed generation: call provider->generateStream if available.
     * The $onDelta callable will be invoked with string deltas as they arrive.
     * If provider does not support streaming, fallback to calling generate()
     * and invoke $onDelta once with the full text.
     * Returns the provider result array (same shape as generate()).
     *
     * @param  callable  $onDelta  function(string $delta): void
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
