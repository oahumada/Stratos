<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AbacusClient
{
    protected Client $http;

    public function __construct(?Client $http = null)
    {
        if ($http !== null) {
            $this->http = $http;
            return;
        }
        // When running unit tests outside a full Laravel bootstrap, `config()` may
        // not be available. Guard access and fall back to env vars so tests can
        // construct a client without requiring the container.
        try {
            $baseUri = function_exists('config') ? config('services.abacus.base_url') : null;
        } catch (\Throwable $_) {
            $baseUri = null;
        }
        if (empty($baseUri)) {
            $baseUri = env('ABACUS_BASE_URL');
        }

        try {
            $timeout = function_exists('config') ? (int) config('services.abacus.timeout', 120) : 120;
        } catch (\Throwable $_) {
            $timeout = (int) (env('ABACUS_TIMEOUT') ?: 120);
        }

        try {
            $key = function_exists('config') ? config('services.abacus.key') : null;
        } catch (\Throwable $_) {
            $key = null;
        }
        if (empty($key)) {
            $key = env('ABACUS_KEY');
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];
        if (! empty($key)) {
            $headers['Authorization'] = 'Bearer ' . $key;
            $headers['apiKey'] = $key;
        }

        $this->http = new Client([
            'base_uri' => $baseUri,
            'timeout' => $timeout,
            'headers' => $headers,
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
        try {
            $modelCfg = function_exists('config') ? config('services.abacus.model') : null;
        } catch (\Throwable $_) {
            $modelCfg = null;
        }
        if (empty($modelCfg)) {
            $modelCfg = env('ABACUS_MODEL', null);
        }

        // Only include an explicit model when it appears to be a valid Abacus
        // model identifier. Some deployments set a placeholder like
        // "abacus-default" in config; sending that causes a 400 from
        // route-llm. If modelCfg is absent or looks suspicious, omit it so
        // the Abacus route can choose a default.
        $payloadBase = [
            'prompt' => $prompt,
            'max_tokens' => $options['max_tokens'] ?? 1000,
            'temperature' => $options['temperature'] ?? 0.2,
        ];

        if (! empty($modelCfg) && preg_match('/gpt/i', (string) $modelCfg)) {
            $payloadBase['model'] = $modelCfg;
        }

        $payload = array_merge($payloadBase, $options['overrides'] ?? []);

        $maxAttempts = (int) ($options['retries'] ?? 2);
        $attempt = 0;
        $lastEx = null;
        while ($attempt <= $maxAttempts) {
            try {
                $resp = $this->http->post('/v1/generate', [
                    'json' => $payload,
                ]);

                $body = (string) $resp->getBody();
                $decoded = json_decode($body, true);

                return is_array($decoded) ? $decoded : ['raw' => $body];
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                $lastEx = $e;
                $attempt++;
                $status = null;
                try { $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : null; } catch (\Throwable $_) { $status = null; }
                try { \Log::warning('AbacusClient::generate HTTP attempt failed', ['attempt' => $attempt, 'status' => $status, 'message' => $e->getMessage()]); } catch (\Throwable $_) {}
                // retry on 5xx or connection issues, otherwise break
                if ($attempt > $maxAttempts || ($status !== null && $status < 500 && $status !== 429)) {
                    // include response body if present
                    try {
                        $respBody = method_exists($e, 'getResponse') && $e->getResponse() ? (string) $e->getResponse()->getBody() : null;
                    } catch (\Throwable $__) { $respBody = null; }
                    try { \Log::error('AbacusClient::generate final failure', ['message' => $e->getMessage(), 'response_body' => $respBody]); } catch (\Throwable $_) {}
                    throw $e;
                }
                // exponential backoff
                sleep(min(5, (int) pow(2, $attempt)));
                continue;
            }
        }

        if ($lastEx) {
            throw $lastEx;
        }

        return null;
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

        try {
            $modelCfg = function_exists('config') ? config('services.abacus.model') : null;
        } catch (\Throwable $_) {
            $modelCfg = null;
        }
        if (empty($modelCfg)) {
            $modelCfg = env('ABACUS_MODEL', null);
        }

        $payloadBase = [
            'messages' => [[ 'role' => 'user', 'content' => $prompt ]],
            'stream' => true,
            'max_tokens' => $options['max_tokens'] ?? 1000,
            'temperature' => $options['temperature'] ?? 0.2,
        ];

        if (! empty($modelCfg) && preg_match('/gpt/i', (string) $modelCfg)) {
            $payloadBase['model'] = $modelCfg;
        }

        $payload = array_merge($payloadBase, $options['overrides'] ?? []);

        // Determine streaming endpoint: prefer explicit config, otherwise derive from base_url.
        try {
            $streamUrl = function_exists('config') ? config('services.abacus.stream_url') : null;
        } catch (\Throwable $_) { $streamUrl = null; }
        if (empty($streamUrl)) {
            try { $base = function_exists('config') ? rtrim(config('services.abacus.base_url') ?? '', '/') : ''; } catch (\Throwable $_) { $base = rtrim(env('ABACUS_BASE_URL', ''), '/'); }
            if (strpos($base, 'api.abacus.ai') !== false) {
                $streamUrl = 'https://routellm.abacus.ai/v1/chat/completions';
            } else {
                $streamUrl = $base . '/v1/chat/completions';
            }
        }

        // Allow caller to increase request timeout when expecting long responses
        try { $requestTimeout = $options['timeout'] ?? (function_exists('config') ? config('services.abacus.timeout', 60) : 60); } catch (\Throwable $_) { $requestTimeout = $options['timeout'] ?? (int) (env('ABACUS_TIMEOUT') ?: 60); }
        // How long to tolerate no data on the stream before considering it stalled
        $streamIdleTimeout = $options['stream_idle_timeout'] ?? 120; // seconds

        $maxAttempts = (int) ($options['retries'] ?? 2);
        $attempt = 0;
        $lastEx = null;
        while ($attempt <= $maxAttempts) {
            try {
                $resp = $this->http->post($streamUrl, [
                    'json' => $payload,
                    'stream' => true,
                    'timeout' => $requestTimeout,
                    'read_timeout' => $options['read_timeout'] ?? 0,
                ]);

                $body = $resp->getBody();
                return $this->parseStream($body, $options, $onChunk, $streamIdleTimeout);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                $lastEx = $e;
                $attempt++;
                $status = null;
                try { $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : null; } catch (\Throwable $_) { $status = null; }
                try { \Log::warning('AbacusClient::generateStream HTTP attempt failed', ['attempt' => $attempt, 'status' => $status, 'message' => $e->getMessage()]); } catch (\Throwable $_) {}
                if ($attempt > $maxAttempts || ($status !== null && $status < 500 && $status !== 429)) {
                    try { $respBody = method_exists($e, 'getResponse') && $e->getResponse() ? (string) $e->getResponse()->getBody() : null; } catch (\Throwable $__) { $respBody = null; }
                    try { \Log::error('AbacusClient::generateStream final failure', ['message' => $e->getMessage(), 'response_body' => $respBody, 'stream_url' => $streamUrl]); } catch (\Throwable $_) {}
                    throw $e;
                }
                sleep(min(5, (int) pow(2, $attempt)));
                continue;
            }
        }

        if ($lastEx) {
            throw $lastEx;
        }

        return null;

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
                    try { \Log::warning('AbacusClient::parseStream idle timeout reached'); } catch (\Throwable $_) {}
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
