<?php

namespace App\Services\LLMProviders;

class OpenAIProvider implements LLMProviderInterface
{
    protected array $config;

    protected array $lastResponseHeaders = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function generate(string $prompt): array
    {
        $apiKey = $this->config['api_key'] ?? env('LLM_API_KEY');
        $model = $this->config['model'] ?? env('LLM_OPENAI_MODEL', 'gpt-4o');
        $endpoint = $this->config['endpoint'] ?? 'https://api.openai.com/v1/chat/completions';

        if (empty($apiKey)) {
            throw new \RuntimeException('OpenAI API key not configured.');
        }

        $payload = json_encode([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a structured scenario generator.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.2,
            'max_tokens' => 1500,
        ]);

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '.$apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $rawHeaders = substr($result, 0, $headerSize);
        $body = substr($result, $headerSize);
        curl_close($ch);

        // parse headers into associative array (lowercase keys)
        $this->lastResponseHeaders = [];
        foreach (explode("\r\n", $rawHeaders) as $h) {
            if (strpos($h, ':') !== false) {
                [$k, $v] = array_map('trim', explode(':', $h, 2));
                $this->lastResponseHeaders[strtolower($k)] = $v;
            }
        }

        $result = $body;

        if ($errno !== 0) {
            throw new \RuntimeException('HTTP client error: '.$errmsg);
        }

        if ($httpCode >= 400) {
            // Rate limit
            if ($httpCode === 429) {
                $retryAfter = null;
                // Try parse Retry-After header
                $headers = $this->lastResponseHeaders ?? [];
                if (is_array($headers) && isset($headers['retry-after'])) {
                    $retryAfter = (int) $headers['retry-after'];
                }
                throw new \App\Services\LLMProviders\Exceptions\LLMRateLimitException('LLM rate limit (429)', $retryAfter);
            }

            // Server errors
            if ($httpCode >= 500) {
                throw new \App\Services\LLMProviders\Exceptions\LLMServerException('LLM provider server error: '.$httpCode);
            }

            throw new \RuntimeException('LLM provider returned HTTP '.$httpCode.': '.substr($result, 0, 200));
        }

        $data = json_decode($result, true);
        // Extract assistant content -- structure depends on provider
        $assistant = $data['choices'][0]['message']['content'] ?? null;

        // Attempt to parse JSON from assistant; if fails, return raw text under 'raw_text'
        $parsed = json_decode($assistant, true);
        $responsePayload = is_array($parsed) ? $parsed : ['raw_text' => $assistant];

        return [
            'response' => $responsePayload,
            'confidence' => 0.0,
            'model_version' => $model,
        ];
    }
}
