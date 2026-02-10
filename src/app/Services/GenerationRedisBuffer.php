<?php
namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Models\ScenarioGeneration;
use App\Services\MetadataValidator;

class GenerationRedisBuffer
{
    public static function key(int $organizationId, int $generationId, ?int $scenarioId = null): string
    {
        // Key pattern includes app context, organization, optional scenario, and generation
        $prefix = 'app:scenario_planning';
        if ($scenarioId) {
            return "{$prefix}:org:{$organizationId}:scenario:{$scenarioId}:generation:{$generationId}:chunks";
        }
        return "{$prefix}:org:{$organizationId}:generation:{$generationId}:chunks";
    }

    public function pushChunk(int $organizationId, int $generationId, string $chunk, ?array $providerMeta = null, ?int $scenarioId = null, int $ttlSeconds = null): void
    {
        // Default TTL: 24 hours (can be overridden via env GENERATION_CHUNK_TTL)
        if ($ttlSeconds === null) {
            $env = getenv('GENERATION_CHUNK_TTL');
            $ttlSeconds = $env !== false ? (int) $env : 86400;
        }

        $key = self::key($organizationId, $generationId, $scenarioId);
        $metaKey = $key . ':meta';
        $isNew = ! Redis::exists($key);

        // push chunk
        Redis::rpush($key, $chunk);

        // update meta hash atomically-ish
        Redis::hincrby($metaKey, 'received_chunks', 1);
        Redis::hincrby($metaKey, 'received_bytes', strlen($chunk));
        if (! Redis::hexists($metaKey, 'first_chunk_at')) {
            Redis::hset($metaKey, 'first_chunk_at', now()->toDateTimeString());
        }
        Redis::hset($metaKey, 'last_chunk_at', now()->toDateTimeString());

        // if provider reported total_chunks include it
        if (! empty($providerMeta) && isset($providerMeta['total_chunks'])) {
            Redis::hset($metaKey, 'total_chunks', (int) $providerMeta['total_chunks']);
        }

        // ensure TTL set on both keys if new
        if ($isNew) {
            Redis::expire($key, $ttlSeconds);
            Redis::expire($metaKey, $ttlSeconds);
        }
    }

    public function getChunkCount(int $organizationId, int $generationId, ?int $scenarioId = null): int
    {
        $key = self::key($organizationId, $generationId, $scenarioId);
        return (int) Redis::llen($key);
    }

    /**
     * Assemble all chunks from Redis, persist compacted metadata and optional llm_response.
     * Returns array with metadata about the operation.
     */
    public function assembleAndPersist(int $organizationId, int $generationId, ?int $scenarioId = null, bool $deleteAfter = false): array
    {
        $key = self::key($organizationId, $generationId, $scenarioId);
        $chunks = Redis::lrange($key, 0, -1);
        $chunkCount = is_array($chunks) ? count($chunks) : 0;
        $metaKey = $key . ':meta';
        $metaHash = Redis::hgetall($metaKey) ?: [];
        $assembled = implode('', $chunks ?: []);
        $encoded = base64_encode($assembled);

        $generation = ScenarioGeneration::find($generationId);
        if (! $generation) {
            return ['ok' => false, 'reason' => 'generation_not_found'];
        }

        // Try to parse assembled into JSON for llm_response; fallback to existing llm_response
        $parsed = json_decode($assembled, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $generation->llm_response = $parsed;
        }

        $meta = is_array($generation->metadata) ? $generation->metadata : [];
        // persist known meta from redis meta hash (e.g., total_chunks)
        if (! empty($metaHash) && is_array($metaHash)) {
            if (isset($metaHash['total_chunks'])) {
                $meta['total_chunks'] = (int) $metaHash['total_chunks'];
            }
            if (isset($metaHash['received_chunks'])) {
                $meta['received_chunks'] = (int) $metaHash['received_chunks'];
            }
            if (isset($metaHash['received_bytes'])) {
                $meta['received_bytes'] = (int) $metaHash['received_bytes'];
            }
            if (isset($metaHash['first_chunk_at'])) {
                $meta['first_chunk_at'] = $metaHash['first_chunk_at'];
            }
            if (isset($metaHash['last_chunk_at'])) {
                $meta['last_chunk_at'] = $metaHash['last_chunk_at'];
            }
        }
        // store compacted blob in dedicated column (prefer separate storage from metadata)
        $generation->compacted = $encoded;

        // keep other useful metadata fields but remove any existing compacted blob from metadata
        if (isset($meta['compacted'])) {
            unset($meta['compacted']);
        }
        $meta['chunk_count'] = $chunkCount;
        $meta['compacted_at'] = now()->toDateTimeString();

        // validate metadata lightly before persisting (log errors but do not block)
        try {
            $validator = new MetadataValidator();
            $errors = $validator->validate($meta);
            if (! empty($errors)) {
                Log::warning('Metadata validation failed for generation', ['generation_id' => $generationId, 'errors' => $errors]);
                $meta['validation_errors'] = $errors;
            }
        } catch (\Throwable $e) {
            Log::warning('Metadata validation error', ['generation_id' => $generationId, 'exception' => (string) $e]);
        }

        $generation->metadata = $meta;

        // also populate dedicated columns for easier queries and auditing
        $generation->chunk_count = $chunkCount;
        $generation->compacted_at = now();
        try {
            if (function_exists('auth') && auth()->check()) {
                $generation->compacted_by = auth()->id();
            }
        } catch (\Throwable $e) {
            // ignore in CLI contexts where auth isn't available
        }

        $generation->save();

        if ($deleteAfter) {
            Redis::del($key);
        }

        return [
            'ok' => true,
            'generation_id' => $generationId,
            'chunk_count' => $chunkCount,
            'compacted_base64_len' => strlen($encoded),
            'decoded_len' => strlen($assembled),
        ];
    }
}
