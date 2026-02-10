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
        // Prevent PHP from timing out during long streaming responses
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

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
        // Allow caller to increase request timeout when expecting long responses
        $requestTimeout = $options['timeout'] ?? config('services.abacus.timeout', 60);
        // How long to tolerate no data on the stream before considering it stalled
        $streamIdleTimeout = $options['stream_idle_timeout'] ?? 120; // seconds

        try {
            $resp = $this->http->post($streamUrl, [
                'json' => $payload,
                'stream' => true,
                'timeout' => $requestTimeout,
                'read_timeout' => $options['read_timeout'] ?? 0,
            ]);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            try {
                $respBody = method_exists($e, 'getResponse') && $e->getResponse() ? (string) $e->getResponse()->getBody() : null;
            } catch (\Throwable $inner) {
                $respBody = null;
            }
            \Log::error('AbacusClient::generateStream HTTP error', [
                'message' => $e->getMessage(),
                'stream_url' => $streamUrl,
                'payload' => $payload,
                'response_body' => $respBody,
            ]);
            throw $e;
        }

        $body = $resp->getBody();

        return $this->parseStream($body, $options, $onChunk, $streamIdleTimeout);

    }

    /**
     * Parse a streaming body and invoke $onChunk for each delta.
     * Separated for testability.
     *
     * @param mixed $body
     * @param array $options
     * @param callable|null $onChunk
     * @param int $streamIdleTimeout
     * @return array|null
     */
    protected function parseStream($body, array $options = [], ?callable $onChunk = null, int $streamIdleTimeout = 120): ?array
    {
        $assembled = '';
        $buffer = '';
        $receivedChunks = 0;
        $totalChunks = $options['expected_total_chunks'] ?? null;
        $expectedTotalBytes = $options['expected_total_bytes'] ?? null;
        $decoder = function ($bytes) {
            return is_string($bytes) ? $bytes : (string) $bytes;
        };
        $lastChunkAt = microtime(true);
        while (! $body->eof()) {
            $chunk = $body->read(1024);
            if ($chunk === '') {
                usleep(50000);
                if ((microtime(true) - $lastChunkAt) > $streamIdleTimeout) {
                    \Log::warning('AbacusClient::parseStream idle timeout reached');
                    break;
                }
                continue;
            }
            $lastChunkAt = microtime(true);
            $buffer .= $decoder($chunk);
            $lines = preg_split('/\r?\n/', $buffer);
            $buffer = array_pop($lines);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                if (strpos($line, 'data: ') !== 0) continue;
                $payloadLine = substr($line, 6);
                if ($payloadLine === '[DONE]') {
                    break 2;
                }
                $decoded = json_decode($payloadLine, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }
                $delta = $decoded['choices'][0]['delta']['content'] ?? null;
                if (! empty($delta)) {
                    $assembled .= $delta;
                    $receivedChunks++;
                    if (isset($decoded['progress']['total'])) {
                        $totalChunks = (int) $decoded['progress']['total'];
                    } elseif (isset($decoded['choices'][0]['progress']['total'])) {
                        $totalChunks = (int) $decoded['choices'][0]['progress']['total'];
                    }

                    $percent = null;
                    if ($totalChunks !== null && $totalChunks > 0) {
                        $percent = min(100, ($receivedChunks / $totalChunks) * 100);
                    } elseif ($expectedTotalBytes !== null && $expectedTotalBytes > 0) {
                        $percent = min(100, (strlen($assembled) / $expectedTotalBytes) * 100);
                    }

                    $meta = [
                        'received_chunks' => $receivedChunks,
                        'total_chunks' => $totalChunks,
                        'received_bytes' => strlen($assembled),
                        'expected_total_bytes' => $expectedTotalBytes,
                        'percent' => $percent,
                    ];

                    if ($onChunk) {
                        try { call_user_func($onChunk, $delta, $meta); } catch (\Throwable $e) { /* swallow */ }
                    }
                }
            }
        }

        $asJson = json_decode($assembled, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $asJson;
        }

        return ['content' => $assembled, 'raw' => null];
    }
}
