<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Tests\CreatesApplication;
use App\Models\ScenarioGeneration;
use App\Models\GenerationChunk;
use App\Services\GenerationRedisBuffer;

class RedisBufferManualTest extends TestCase
{
    use CreatesApplication;

    protected $app;

    protected function setUp(): void
    {
        $this->app = $this->createApplication();
    }

    public function test_push_chunks_from_db_to_redis_and_compact()
    {
        // Use existing DB row (id=59) if present; otherwise skip the manual test
        $generation = ScenarioGeneration::find(59);
        if (! $generation) {
            $this->markTestSkipped('scenario_generation id=59 not found in DB; skipping manual Redis buffer test.');
            return;
        }

        $orgId = (int) $generation->organization_id;
        $generationId = (int) $generation->id;

        // load existing chunk rows for this generation
        $chunks = GenerationChunk::where('scenario_generation_id', $generationId)
            ->orderBy('sequence')
            ->get();

        if ($chunks->isEmpty()) {
            $this->markTestSkipped('No generation_chunks found for generation id=59; skipping.');
            return;
        }

        $buffer = new GenerationRedisBuffer();

        // push all chunks into Redis using the buffer service
        foreach ($chunks as $c) {
            $buffer->pushChunk($orgId, $generationId, $c->chunk);
        }

        // assemble and persist; delete redis keys after compacting
        $res = $buffer->assembleAndPersist($orgId, $generationId, null, true);

        $this->assertIsArray($res);
        $this->assertArrayHasKey('ok', $res);
        $this->assertTrue($res['ok'], 'assembleAndPersist reported failure: ' . json_encode($res));
        $this->assertArrayHasKey('chunk_count', $res);
        $this->assertGreaterThan(0, $res['chunk_count']);

        // reload generation and assert metadata contains compacted and chunk_count
        $generation->refresh();
        $meta = is_array($generation->metadata) ? $generation->metadata : [];
        $this->assertArrayHasKey('compacted', $meta);
        $this->assertArrayHasKey('chunk_count', $meta);
        $this->assertEquals($res['chunk_count'], $meta['chunk_count']);
    }
}
