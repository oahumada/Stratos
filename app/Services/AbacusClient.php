<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class AbacusClient
{
    protected Client $http;

    public function __construct(?Client $http = null)
    {
        if ($http !== null) {
            $this->http = $http;

            return;
        }

        $baseUri = config('services.abacus.base_url');
        $timeout = (int) config('services.abacus.timeout', 120);
        $key = config('services.abacus.key');

        $headers = [
            'Content-Type' => 'application/json',
        ];

        if (! empty($key)) {
            $headers['Authorization'] = 'Bearer '.$key;
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
     * @throws GuzzleException
     */
    public function generate(string $prompt, array $options = []): ?array
    {
        $modelCfg = config('services.abacus.model');

        $payload = array_merge([
            'prompt' => $prompt,
            'max_tokens' => $options['max_tokens'] ?? 1000,
            'temperature' => $options['temperature'] ?? 0.2,
        ], $options['overrides'] ?? []);

        if (! empty($modelCfg) && preg_match('/gpt/i', (string) $modelCfg)) {
            $payload['model'] = $modelCfg;
        }

        $maxAttempts = (int) ($options['retries'] ?? 2);

        return $this->executeWithRetry(function () use ($payload) {
            $resp = $this->http->post('/v1/generate', [
                'json' => $payload,
            ]);

            $body = (string) $resp->getBody();
            $decoded = json_decode($body, true);

            return is_array($decoded) ? $decoded : ['raw' => $body];
        }, $maxAttempts, 'generate');
    }

    /**
     * Stream a generation from Abacus and invoke $onChunk for each text delta.
     */
    public function generateStream(string $prompt, array $options = [], ?callable $onChunk = null): ?array
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $modelCfg = config('services.abacus.model');
        $streamUrl = $this->resolveStreamUrl();
        $requestTimeout = $options['timeout'] ?? config('services.abacus.timeout', 60);
        $streamIdleTimeout = $options['stream_idle_timeout'] ?? 120;

        $payload = array_merge([
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'stream' => true,
            'max_tokens' => $options['max_tokens'] ?? 1000,
            'temperature' => $options['temperature'] ?? 0.2,
        ], $options['overrides'] ?? []);

        if (! empty($modelCfg) && preg_match('/gpt/i', (string) $modelCfg)) {
            $payload['model'] = $modelCfg;
        }

        $maxAttempts = (int) ($options['retries'] ?? 2);

        return $this->executeWithRetry(function () use ($streamUrl, $payload, $requestTimeout, $options, $onChunk, $streamIdleTimeout) {
            $resp = $this->http->post($streamUrl, [
                'json' => $payload,
                'stream' => true,
                'timeout' => $requestTimeout,
                'read_timeout' => $options['read_timeout'] ?? 0,
            ]);

            return $this->parseStream($resp->getBody(), $options, $onChunk, $streamIdleTimeout);
        }, $maxAttempts, 'generateStream');
    }

    protected function resolveStreamUrl(): string
    {
        $streamUrl = config('services.abacus.stream_url');
        if (! empty($streamUrl)) {
            return $streamUrl;
        }

        $base = rtrim(config('services.abacus.base_url') ?? '', '/');
        if (str_contains($base, 'api.abacus.ai')) {
            return 'https://routellm.abacus.ai/v1/chat/completions';
        }

        return $base.'/v1/chat/completions';
    }

    protected function executeWithRetry(callable $operation, int $maxAttempts, string $context): mixed
    {
        $attempt = 0;
        $lastEx = null;

        while ($attempt <= $maxAttempts) {
            try {
                return $operation();
            } catch (GuzzleException $e) {
                $lastEx = $e;
                $attempt++;
                $status = ($e instanceof RequestException && $e->getResponse())
                    ? $e->getResponse()->getStatusCode()
                    : null;

                Log::warning("AbacusClient::{$context} attempt failed", [
                    'attempt' => $attempt,
                    'status' => $status,
                    'message' => $e->getMessage(),
                ]);

                if ($attempt > $maxAttempts || ($status !== null && $status < 500 && $status !== 429)) {
                    $respBody = ($e instanceof RequestException && $e->getResponse())
                        ? (string) $e->getResponse()->getBody()
                        : null;

                    Log::error("AbacusClient::{$context} final failure", [
                        'message' => $e->getMessage(),
                        'response_body' => $respBody,
                    ]);
                    throw $e;
                }

                sleep((int) min(5, pow(2, $attempt)));
            }
        }

        throw $lastEx;
    }

    protected function parseStream($body, array $options = [], ?callable $onChunk = null, int $streamIdleTimeout = 120): ?array
    {
        $assembled = '';
        $buffer = '';
        $receivedChunks = 0;
        $totalChunks = $options['expected_total_chunks'] ?? null;
        $expectedTotalBytes = $options['expected_total_bytes'] ?? null;

        $lastChunkAt = microtime(true);
        while (! $body->eof()) {
            $chunk = $body->read(1024);
            if ($chunk === '') {
                usleep(50000);
                if ((microtime(true) - $lastChunkAt) > $streamIdleTimeout) {
                    Log::warning('AbacusClient::parseStream idle timeout reached');
                    break;
                }

                continue;
            }
            $lastChunkAt = microtime(true);
            $buffer .= $chunk;
            $lines = preg_split('/\r?\n/', $buffer);
            $buffer = array_pop($lines);

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || ! str_starts_with($line, 'data: ')) {
                    continue;
                }

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

                    if ($onChunk) {
                        $meta = $this->buildStreamMeta($decoded, $receivedChunks, $totalChunks, $assembled, $expectedTotalBytes);
                        try {
                            call_user_func($onChunk, $delta, $meta);
                        } catch (\Throwable $_) {
                        }
                    }
                }
            }
        }

        $asJson = json_decode($assembled, true);

        return (json_last_error() === JSON_ERROR_NONE) ? $asJson : ['content' => $assembled, 'raw' => null];
    }

    protected function buildStreamMeta(array $decoded, int $receivedChunks, ?int &$totalChunks, string $assembled, ?int $expectedTotalBytes): array
    {
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

        return [
            'received_chunks' => $receivedChunks,
            'total_chunks' => $totalChunks,
            'received_bytes' => strlen($assembled),
            'expected_total_bytes' => $expectedTotalBytes,
            'percent' => $percent,
        ];
    }
}
