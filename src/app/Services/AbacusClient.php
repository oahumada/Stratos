<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AbacusClient
{
    protected Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => config('services.abacus.base_url'),
            'timeout' => (int) config('services.abacus.timeout', 60),
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.abacus.key'),
                // Some Abacus endpoints (docs/examples) expect `apiKey` header instead
                // of Authorization. Send both so we have a fallback without extra roundtrips.
                'apiKey' => config('services.abacus.key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Call Abacus generate endpoint with prompt and options.
     * Returns decoded JSON response or throws on HTTP error.
     *
     * @param string $prompt
     * @param array $options
     * @return array|null
     * @throws GuzzleException
     */
    public function generate(string $prompt, array $options = []): ?array
    {
        $payload = array_merge([
            'model' => config('services.abacus.model'),
            'prompt' => $prompt,
            'max_tokens' => $options['max_tokens'] ?? 1000,
            'temperature' => $options['temperature'] ?? 0.2,
        ], $options['overrides'] ?? []);

        $resp = $this->http->post('/v1/generate', [
            'json' => $payload,
        ]);

        $body = (string) $resp->getBody();
        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : ['raw' => $body];
    }

    /**
     * Stream a generation from Abacus and invoke $onChunk for each text delta.
     * Returns the assembled text and raw payload when possible.
     *
     * @param string $prompt
     * @param array $options
     * @param callable|null $onChunk function(string $delta): void
     * @return array|null
     * @throws GuzzleException
     */
    public function generateStream(string $prompt, array $options = [], ?callable $onChunk = null): ?array
    {
        $payload = array_merge([
            'model' => config('services.abacus.model'),
            'messages' => [[ 'role' => 'user', 'content' => $prompt ]],
            'stream' => true,
            'max_tokens' => $options['max_tokens'] ?? 1000,
            'temperature' => $options['temperature'] ?? 0.2,
        ], $options['overrides'] ?? []);

        // Determine streaming endpoint: prefer explicit config, otherwise derive from base_url.
        $streamUrl = config('services.abacus.stream_url');
        if (empty($streamUrl)) {
            $base = rtrim(config('services.abacus.base_url') ?? '', '/');
            if (strpos($base, 'api.abacus.ai') !== false) {
                // production-style base uses api.abacus.ai — Abacus streaming uses routellm subdomain
                $streamUrl = 'https://routellm.abacus.ai/v1/chat/completions';
            } else {
                $streamUrl = $base . '/v1/chat/completions';
            }
        }

        // Ensure we don't buffer the whole response in Guzzle internals
        $resp = $this->http->post($streamUrl, [
            'json' => $payload,
            'stream' => true,
            'read_timeout' => $options['read_timeout'] ?? 0,
        ]);

        $body = $resp->getBody();
        $assembled = '';
        $buffer = '';
        $decoder = function ($bytes) {
            return is_string($bytes) ? $bytes : (string) $bytes;
        };

        while (! $body->eof()) {
            $chunk = $body->read(1024);
            if ($chunk === '') {
                // small sleep to avoid busy loop
                usleep(10000);
                continue;
            }
            $buffer .= $decoder($chunk);
            // split into lines
            $lines = preg_split('/\r?\n/', $buffer);
            // keep last partial line in buffer
            $buffer = array_pop($lines);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                if (strpos($line, 'data: ') !== 0) continue;
                $payloadLine = substr($line, 6);
                if ($payloadLine === '[DONE]') {
                    // finished
                    break 2;
                }
                $decoded = json_decode($payloadLine, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // ignore parse errors for incremental chunks
                    continue;
                }
                $delta = $decoded['choices'][0]['delta']['content'] ?? null;
                if (! empty($delta)) {
                    $assembled .= $delta;
                    if ($onChunk) {
                        try { call_user_func($onChunk, $delta); } catch (\Throwable $e) { /* swallow */ }
                    }
                }
            }
        }

        // Attempt to parse assembled as JSON (common when LLM returns full JSON object as content)
        $asJson = json_decode($assembled, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $asJson;
        }

        return ['content' => $assembled, 'raw' => null];
    }
}
