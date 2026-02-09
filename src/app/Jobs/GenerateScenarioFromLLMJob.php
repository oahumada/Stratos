<?php

namespace App\Jobs;

use App\Models\ScenarioGeneration;
use App\Services\LLMClient;
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

    public function handle(LLMClient $llm)
    {
        $generation = ScenarioGeneration::find($this->generationId);
        if (! $generation) {
            return;
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

            $result = $llm->generateStream($generation->prompt ?? '', function ($delta) use (&$assembled, &$seq, $generation, &$buffer, $maxBuffer, &$lastFlush, $flushInterval) {
                // append and persist in buffered chunks
                $assembled .= $delta;
                $buffer .= $delta;

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
                    } catch (\Throwable $e) {
                        // log and continue
                    }
                    $buffer = '';
                    $lastFlush = microtime(true);
                }
            });

            // persist any remaining buffer
            if (! empty($buffer)) {
                try {
                    GenerationChunk::create([
                        'scenario_generation_id' => $generation->id,
                        'sequence' => $seq++,
                        'chunk' => $buffer,
                    ]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            // Normalize result: provider may return structure or rely on assembled text
            $rawResponse = $result['response'] ?? $result ?? null;
            if (is_null($rawResponse)) {
                // try parse assembled text
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
                // If parsing failed but we have assembled text, store as content
                if (! empty($assembled)) {
                    $parsed = ['content' => $assembled];
                    $isValid = true;
                }
            }

            if (! $isValid) {
                $generation->status = 'failed';
                $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'invalid_llm_response', 'message' => 'LLM returned invalid or non-JSON response']);
                $generation->save();
                return;
            }

            // Redact and persist final response
            $generation->llm_response = RedactionService::redactArray($parsed);
            $generation->confidence_score = $result['confidence'] ?? null;
            $generation->model_version = $result['model_version'] ?? null;
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
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'rate_limit_exceeded', 'message' => $e->getMessage()]);
            $generation->save();
        } catch (LLMServerException $e) {
            // server-side errors: mark failed and record
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'server_error', 'message' => $e->getMessage()]);
            $generation->save();
        } catch (Exception $e) {
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'exception', 'message' => $e->getMessage()]);
            $generation->save();
        }
    }
}
