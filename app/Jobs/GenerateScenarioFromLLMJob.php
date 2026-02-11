<?php

namespace App\Jobs;

use App\Models\GenerationChunk;
use App\Models\ScenarioGeneration;
use App\Services\AbacusClient;
use App\Services\GenerationRedisBuffer;
use App\Services\LLMClient;
use App\Services\LLMProviders\Exceptions\LLMRateLimitException;
use App\Services\LLMProviders\Exceptions\LLMServerException;
use App\Services\RedactionService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class GenerateScenarioFromLLMJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $generationId;

    public function __construct(int $generationId)
    {
        $this->generationId = $generationId;
    }

    public function handle(LLMClient $llm, ?AbacusClient $abacus = null)
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
            $redisBuf = new GenerationRedisBuffer;
            $storageMode = strtolower(env('GENERATION_CHUNK_STORAGE', 'redis'));
            $useDb = in_array($storageMode, ['db', 'both']);
            $useRedis = in_array($storageMode, ['redis', 'both']);
            // If Redis is configured but not reachable in the test environment,
            // fallback to DB to ensure chunks are persisted and tests remain deterministic.
            if ($useRedis) {
                try {
                    // attempt a lightweight ping/exists to validate connection
                    Redis::ping();
                } catch (\Throwable $e) {
                    try {
                        \Log::warning('Redis unavailable, falling back to DB chunk storage', ['err' => $e->getMessage()]);
                    } catch (\Throwable $__) {
                    }
                    $useRedis = false;
                    $useDb = true;
                }
            }
            // In testing environment prefer DB persistence to make tests deterministic
            try {
                if (function_exists('app') && app()->environment() === 'testing') {
                    $useDb = true;
                    $useRedis = false;
                }
            } catch (\Throwable $_) {
            }
            $seq = 1;
            $buffer = '';
            $maxBuffer = 256; // bytes
            $flushInterval = 0.25; // seconds
            $lastFlush = microtime(true);
            $lastMeta = null;

            // Choose provider: if generation metadata requests Abacus, use AbacusClient
            $provider = $generation->metadata['provider'] ?? null;
            $options = $generation->metadata['provider_options'] ?? [];

            // Determine effective model that will be sent to the provider for traceability.
            try {
                $configModel = function_exists('config') ? config('services.abacus.model') : null;
            } catch (\Throwable $_) {
                $configModel = null;
            }
            if (empty($configModel)) {
                $configModel = env('ABACUS_MODEL') ?: null;
            }
            $overrideModel = is_array($options) && isset($options['overrides']['model']) ? $options['overrides']['model'] : null;
            $effectiveModel = null;
            if (! empty($overrideModel)) {
                $effectiveModel = $overrideModel;
            } elseif (! empty($configModel) && preg_match('/(gpt|claude|sonnet|claude-)/i', (string) $configModel)) {
                $effectiveModel = $configModel;
            } else {
                $effectiveModel = null; // let provider choose
            }

            try {
                $generation->metadata = array_merge($generation->metadata ?? [], ['used_provider_model' => $effectiveModel]);
                $generation->save();
            } catch (\Throwable $_) {
                try {
                    \Log::warning('Failed to persist generation metadata.used_provider_model', ['generation_id' => $generation->id]);
                } catch (\Throwable $__) {
                }
            }

            if ($provider === 'abacus') {
                // mark prompt sent time for monitoring
                try {
                    $generation->metadata = array_merge($generation->metadata ?? [], ['sent_at' => now()->toDateTimeString()]);
                    $generation->save();

            // Si existe un Scenario asociado (p.ej. importado/aceptado automáticamente),
            // crear TalentBlueprints a partir de suggested_roles y persistir el
            // índice de sintetización.
            try {
                $compactedData = $generation->llm_response ?? [];
                $scenario = $generation->scenario ?? null;

                if ($scenario && isset($compactedData['suggested_roles'])) {
                    app(\App\Services\TalentBlueprintService::class)->createFromLlmResponse(
                        $scenario,
                        $compactedData['suggested_roles']
                    );
                }

                if ($scenario) {
                    try {
                        $generation->update([
                            'synthetization_index' => $scenario->synthetization_index,
                        ]);
                    } catch (\Throwable $_) {
                        try {
                            \Log::warning('Failed to persist synthetization_index on generation (job)', ['generation_id' => $generation->id]);
                        } catch (\Throwable $__) {
                        }
                    }
                }
            } catch (\Throwable $e) {
                try {
                    \Log::warning('Error creating TalentBlueprints from generation in job', ['generation_id' => $generation->id, 'err' => $e->getMessage()]);
                } catch (\Throwable $__) {
                }
            }
                } catch (\Throwable $_) {
                    try {
                        \Log::warning('Failed to persist generation metadata.sent_at', ['generation_id' => $generation->id]);
                    } catch (\Throwable $__) {
                    }
                }

                $result = $abacus->generateStream($generation->prompt ?? '', $options, function ($delta, $meta = null) use (&$assembled, &$seq, $generation, &$buffer, $maxBuffer, &$lastFlush, $flushInterval, &$lastMeta, &$useDb, &$useRedis, $redisBuf) {
                    // on first chunk, record first_chunk_at
                    if (empty($generation->metadata['first_chunk_at'])) {
                        try {
                            $generation->metadata = array_merge($generation->metadata ?? [], ['first_chunk_at' => now()->toDateTimeString()]);
                            $generation->save();
                        } catch (\Throwable $_) {
                            try {
                                \Log::warning('Failed to persist generation metadata.first_chunk_at', ['generation_id' => $generation->id]);
                            } catch (\Throwable $__) {
                            }
                        }
                    }
                    $assembled .= $delta;
                    $buffer .= $delta;

                    // Track last known meta even if we don't flush immediately
                    if (isset($meta) && is_array($meta)) {
                        $lastMeta = $meta;
                    }

                    // update last_chunk_at timestamp for monitoring
                    try {
                        $generation->metadata = array_merge($generation->metadata ?? [], ['last_chunk_at' => now()->toDateTimeString()]);
                        $generation->save();
                    } catch (\Throwable $_) {
                        try {
                            \Log::warning('Failed to persist generation metadata.last_chunk_at', ['generation_id' => $generation->id]);
                        } catch (\Throwable $__) {
                        }
                    }

                    $now = microtime(true);
                    $shouldFlushBySize = strlen($buffer) >= $maxBuffer;
                    $shouldFlushByTime = ($now - ($lastFlush ?? 0)) >= $flushInterval;
                    if ($shouldFlushBySize || $shouldFlushByTime) {
                        try {
                            if ($useDb) {
                                GenerationChunk::create([
                                    'scenario_generation_id' => $generation->id,
                                    'sequence' => $seq++,
                                    'chunk' => $buffer,
                                ]);
                            }
                            if ($useRedis) {
                                $orgId = $generation->organization_id ?? ($generation->metadata['organization_id'] ?? null);
                                $scenarioId = $generation->metadata['scenario_id'] ?? null;
                                $redisBuf->pushChunk((int) $orgId, (int) $generation->id, $buffer, $meta ?? null, $scenarioId);
                            }
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
                    if ($useDb) {
                        GenerationChunk::create([
                            'scenario_generation_id' => $generation->id,
                            'sequence' => $seq++,
                            'chunk' => $buffer,
                        ]);
                    }
                    if ($useRedis) {
                        $orgId = $generation->organization_id ?? ($generation->metadata['organization_id'] ?? null);
                        $scenarioId = $generation->metadata['scenario_id'] ?? null;
                        $redisBuf->pushChunk((int) $orgId, (int) $generation->id, $buffer, null, $scenarioId);
                    }
                } catch (\Throwable $e) {
                    \Log::error('Failed to persist chunk: '.$e->getMessage(), ['generation_id' => $generation->id]);
                }

                $buffer = '';
                $result = $res;
            }

            // persist any remaining buffer
            try {
                \Log::info('Final buffer state before final persist', ['generation_id' => $generation->id, 'buffer_len' => strlen($buffer ?? ''), 'useDb' => $useDb ?? null, 'useRedis' => $useRedis ?? null]);
            } catch (\Throwable $_) {
            }

            if (! empty($buffer)) {
                try {
                    if ($useDb) {
                        GenerationChunk::create([
                            'scenario_generation_id' => $generation->id,
                            'sequence' => $seq++,
                            'chunk' => $buffer,
                        ]);
                    }
                    if ($useRedis) {
                        $orgId = $generation->organization_id ?? ($generation->metadata['organization_id'] ?? null);
                        $scenarioId = $generation->metadata['scenario_id'] ?? null;
                        $redisBuf->pushChunk((int) $orgId, (int) $generation->id, $buffer, null, $scenarioId);
                    }
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

            // record completion timestamp in metadata for monitoring
            try {
                $generation->metadata = array_merge($generation->metadata ?? [], ['completed_at' => now()->toDateTimeString()]);
                $generation->save();
            } catch (\Throwable $_) {
                try {
                    \Log::warning('Failed to persist generation metadata.completed_at', ['generation_id' => $generation->id]);
                } catch (\Throwable $__) {
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
            // Persist compacted version for fast retrieval by the UI (avoids client-side assembly)
            try {
                $compact = json_encode($generation->llm_response);
                if ($compact !== false) {
                    $meta = is_array($generation->metadata) ? $generation->metadata : (array) ($generation->metadata ?? []);
                    $meta['compacted'] = base64_encode($compact);
                    // record chunk count to help UI heuristics
                    if ($useDb && ! $useRedis) {
                        $count = \App\Models\GenerationChunk::where('scenario_generation_id', $generation->id)->count();
                    } elseif ($useRedis && ! $useDb) {
                        $orgId = $generation->organization_id ?? ($generation->metadata['organization_id'] ?? null);
                        $count = $redisBuf->getChunkCount((int) $orgId, (int) $generation->id);
                    } else {
                        // both: prefer DB count as authoritative
                        $count = \App\Models\GenerationChunk::where('scenario_generation_id', $generation->id)->count();
                    }
                    $meta['chunk_count'] = $count;
                    $generation->metadata = $meta;
                }
            } catch (\Throwable $_) {
                try {
                    \Log::warning('Failed to create compacted metadata for generation', ['generation_id' => $generation->id]);
                } catch (\Throwable $__) {
                }
            }

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
                'time' => now()->toDateTimeString(), 'type' => 'rate_limit', 'message' => $e->getMessage(),
            ]])]);
            $generation->save();
        } catch (LLMServerException $e) {
            // server-side errors: mark failed and record
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'server_error', 'message' => $e->getMessage(), 'errors' => array_merge($generation->metadata['errors'] ?? [], [[
                'time' => now()->toDateTimeString(), 'type' => 'server_error', 'message' => $e->getMessage(), 'details' => method_exists($e, 'getResponse') ? @((string) $e->getResponse()->getBody()) : null,
            ]])]);
            $generation->save();
            \Log::error('GenerateScenarioFromLLMJob server error', ['generation_id' => $generation->id, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'exception', 'message' => $e->getMessage(), 'errors' => array_merge($generation->metadata['errors'] ?? [], [[
                'time' => now()->toDateTimeString(), 'type' => 'exception', 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString(),
            ]])]);
            $generation->save();
            \Log::error('GenerateScenarioFromLLMJob exception', ['generation_id' => $generation->id, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    // app/Jobs/GenerateScenarioFromLLMJob.php

    protected function assembleAndPersist($generationId)
    {
        // ... tu lógica actual de ensamblado ...
        $data = json_decode($assembledContent, true);

        if ($data && isset($data['suggested_roles'])) {
            $summary = collect($data['suggested_roles'])->map(function ($role) {
                return [
                    'role' => $role['name'],
                    'human' => $role['talent_composition']['human_percentage'] ?? 100,
                    'synthetic' => $role['talent_composition']['synthetic_percentage'] ?? 0,
                    'strategy' => $role['talent_composition']['strategy_suggestion'] ?? 'Buy',
                ];
            });

            $generation = ScenarioGeneration::find($generationId);
            $generation->update([
                'hybrid_composition_summary' => $summary->toArray(),
                'llm_response' => $data,
            ]);
        }
    }
}
