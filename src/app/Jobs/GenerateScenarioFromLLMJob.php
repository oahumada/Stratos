<?php

namespace App\Jobs;

use App\Models\ScenarioGeneration;
use App\Services\LLMClient;
use App\Services\AbacusClient;
use App\Models\GenerationChunk;
use App\Services\LLMProviders\Exceptions\LLMRateLimitException;
use App\Services\LLMProviders\Exceptions\LLMServerException;
use App\Services\RedactionService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateScenarioFromLLMJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $generationId;

    public function __construct(int $generationId)
    {
        $this->generationId = $generationId;
    }

    public function handle(LLMClient $llm, AbacusClient $abacus = null)
    {
        $generation = ScenarioGeneration::find($this->generationId);
        if (! $generation) {
            return;
        }

        if ($abacus === null) {
            $abacus = app(AbacusClient::class);
        }

        $generation->status = 'processing';
        $generation->save();

        try {
            // Use streaming when available so we can persist intermediate chunks
            $assembled = '';
            $seq = 1;
            $buffer = '';
            $maxBuffer = 256; // bytes
            $flushInterval = 0.25; // seconds
            $lastFlush = microtime(true);
            $lastMeta = null;

            // Choose provider: if generation metadata requests Abacus, use AbacusClient
            $provider = $generation->metadata['provider'] ?? null;
            $options = $generation->metadata['provider_options'] ?? [];

            if ($provider === 'abacus') {
                $result = $abacus->generateStream($generation->prompt ?? '', $options, function ($delta, $meta = null) use (&$assembled, &$seq, $generation, &$buffer, $maxBuffer, &$lastFlush, $flushInterval, &$lastMeta) {
                    $assembled .= $delta;
                    $buffer .= $delta;

                    // Track last known meta even if we don't flush immediately
                    if (isset($meta) && is_array($meta)) {
                        $lastMeta = $meta;
                    }

                    $now = microtime(true);
                    $shouldFlushBySize = strlen($buffer) >= $maxBuffer;
                    $shouldFlushByTime = ($now - ($lastFlush ?? 0)) >= $flushInterval;
                    if ($shouldFlushBySize || $shouldFlushByTime) {
                        try {
                            GenerationChunk::create([
                                'scenario_generation_id' => $generation->id,
                                'sequence' => $seq++,
                                'chunk' => $buffer,
                            ]);
                            // Persist progress metadata when provided by the streaming provider
                            if (isset($meta) && is_array($meta)) {
                                $lastMeta = $meta;
                                try {
                                    $generation->metadata = array_merge($generation->metadata ?? [], ['progress' => $meta]);
                                    $generation->save();
                                } catch (\Throwable $e) {
                                    \Log::warning('Failed to persist generation metadata.progress: '.$e->getMessage(), ['generation_id' => $generation->id]);
                                }
                            }
                        } catch (\Throwable $e) {
                            \Log::error('Failed to persist chunk: '.$e->getMessage(), ['generation_id' => $generation->id]);
                        }
                        $buffer = '';
                        $lastFlush = microtime(true);
                    }
                });
            } else {
                // Fallback for non-Abacus providers: call generate() (tests often provide a
                // mocked LLMClient that implements generate()). Emit a single delta
                // containing the provider response (as text) and persist as one chunk.
                $res = $llm->generate($generation->prompt ?? '');

                $raw = is_array($res) && array_key_exists('response', $res) ? $res['response'] : $res;
                if (is_array($raw) || is_object($raw)) {
                    $text = json_encode($raw, JSON_UNESCAPED_UNICODE);
                } elseif (is_string($raw)) {
                    $text = $raw;
                } else {
                    $text = (string) $raw;
                }

                // treat as single delta
                $assembled .= $text;
                $buffer .= $text;

                try {
                    GenerationChunk::create([
                        'scenario_generation_id' => $generation->id,
                        'sequence' => $seq++,
                        'chunk' => $buffer,
                    ]);
                } catch (\Throwable $e) {
                    \Log::error('Failed to persist chunk: '.$e->getMessage(), ['generation_id' => $generation->id]);
                }

                $buffer = '';
                $result = $res;
            }

            // persist any remaining buffer
            if (! empty($buffer)) {
                try {
                    GenerationChunk::create([
                        'scenario_generation_id' => $generation->id,
                        'sequence' => $seq++,
                        'chunk' => $buffer,
                    ]);
                    // if streaming provided progress metadata, persist the last known progress
                    if (! empty($lastMeta) && is_array($lastMeta)) {
                        try {
                            $generation->metadata = array_merge($generation->metadata ?? [], ['progress' => $lastMeta]);
                            $generation->save();
                        } catch (\Throwable $e) {
                            \Log::warning('Failed to persist final generation metadata.progress: '.$e->getMessage(), ['generation_id' => $generation->id]);
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::error('Failed to persist final chunk: '.$e->getMessage(), ['generation_id' => $generation->id]);
                }
            }

            // Normalize result: provider may return structure or rely on assembled text
            $rawResponse = is_array($result) && array_key_exists('response', $result) ? $result['response'] : $result;
            if (is_null($rawResponse)) {
                $parsed = json_decode($assembled, true);
            } else {
                if (is_string($rawResponse)) {
                    $parsed = json_decode($rawResponse, true);
                } elseif (is_array($rawResponse) || is_object($rawResponse)) {
                    $parsed = is_array($rawResponse) ? $rawResponse : json_decode(json_encode($rawResponse), true);
                } else {
                    $parsed = null;
                }
            }

            $isValid = is_array($parsed);

            if (! $isValid) {
                $generation->status = 'failed';
                $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'invalid_llm_response', 'message' => 'LLM returned invalid or non-JSON response']);
                $generation->save();
                return;
            }

            // Redact and persist final response
            $generation->llm_response = RedactionService::redactArray($parsed);
            $generation->confidence_score = is_array($result) && array_key_exists('confidence', $result) ? $result['confidence'] : null;
            $generation->model_version = is_array($result) && array_key_exists('model_version', $result) ? $result['model_version'] : null;
            $generation->generated_at = now();
            $generation->status = 'complete';
            $generation->save();
        } catch (LLMRateLimitException $e) {
            // transient rate limit: requeue with exponential backoff if attempts remain
            $attempts = $this->attempts();
            $maxAttempts = 5;
            $retryAfter = $e->getRetryAfter() ?? (int) pow(2, max(0, $attempts));

            if ($attempts < $maxAttempts) {
                // release back to queue for retry
                $this->release($retryAfter);

                return;
            }

            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'rate_limit_exceeded', 'message' => $e->getMessage(), 'errors' => array_merge($generation->metadata['errors'] ?? [], [[
                'time' => now()->toDateTimeString(), 'type' => 'rate_limit', 'message' => $e->getMessage()
            ]])]);
            $generation->save();
        } catch (LLMServerException $e) {
            // server-side errors: mark failed and record
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'server_error', 'message' => $e->getMessage(), 'errors' => array_merge($generation->metadata['errors'] ?? [], [[
                'time' => now()->toDateTimeString(), 'type' => 'server_error', 'message' => $e->getMessage(), 'details' => method_exists($e, 'getResponse') ? @((string) $e->getResponse()->getBody()) : null
            ]])]);
            $generation->save();
            \Log::error('GenerateScenarioFromLLMJob server error', ['generation_id' => $generation->id, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'exception', 'message' => $e->getMessage(), 'errors' => array_merge($generation->metadata['errors'] ?? [], [[
                'time' => now()->toDateTimeString(), 'type' => 'exception', 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()
            ]])]);
            $generation->save();
            \Log::error('GenerateScenarioFromLLMJob exception', ['generation_id' => $generation->id, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }
}
