<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use App\Services\GenerationRedisBuffer;
use App\Models\ScenarioGeneration;

class MetadataValidationBehaviorTest extends TestCase
{
    public function test_soft_fail_persists_validation_issue_and_compacts()
    {
        // skip if redis not available
        try {
            Redis::ping();
        } catch (\Throwable $e) {
            $this->markTestSkipped('Redis not available');
        }

        $gen = ScenarioGeneration::create([
            'organization_id' => 1,
            'created_by' => 1,
            'prompt' => 'test',
            'metadata' => ['provider' => null, 'model' => null],
        ]);

        $buf = new GenerationRedisBuffer();
        $buf->pushChunk(1, $gen->id, '{"ok": true}');

        $res = $buf->assembleAndPersist(1, $gen->id, null, true);
        $this->assertTrue($res['ok']);

        $gen->refresh();
        $this->assertNotNull($gen->compacted);
        $this->assertArrayHasKey('validation_errors', $gen->metadata);
        $this->assertNotNull($gen->last_validation_issue_id);
    }

    public function test_strict_mode_rejects_on_validation_error()
    {
        // skip if redis not available
        try {
            Redis::ping();
        } catch (\Throwable $e) {
            $this->markTestSkipped('Redis not available');
        }

        putenv('METADATA_VALIDATION_STRICT=true');

        $gen = ScenarioGeneration::create([
            'organization_id' => 1,
            'created_by' => 1,
            'prompt' => 'test strict',
            'metadata' => ['provider' => null, 'model' => null],
        ]);

        $buf = new GenerationRedisBuffer();
        $buf->pushChunk(1, $gen->id, '{"ok": true}');

        $res = $buf->assembleAndPersist(1, $gen->id, null, false);
        $this->assertFalse($res['ok']);
        $this->assertEquals('validation_failed', $res['reason']);

        $gen->refresh();
        $this->assertNull($gen->compacted);
        $this->assertNotNull($res['validation_id']);

        // cleanup
        putenv('METADATA_VALIDATION_STRICT');
    }
}
