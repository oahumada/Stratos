<?php

namespace App\Services\LLMProviders;

interface LLMProviderInterface
{
    /**
     * Generate a response for the given prompt.
     * Must return array with keys: response (array), confidence (float), model_version (string)
     */
    public function generate(string $prompt): array;
}
