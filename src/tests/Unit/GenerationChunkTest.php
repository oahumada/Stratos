<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ScenarioGeneration;
use App\Models\GenerationChunk;

class GenerationChunkTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_and_retrieve_generation_chunks()
    {
        $gen = ScenarioGeneration::create([
            'organization_id' => 1,
            'created_by' => null,
            'prompt' => 'test prompt',
            'status' => 'processing',
        ]);

        GenerationChunk::create([
            'scenario_generation_id' => $gen->id,
            'sequence' => 1,
            'chunk' => 'first',
        ]);

        GenerationChunk::create([
            'scenario_generation_id' => $gen->id,
            'sequence' => 2,
            'chunk' => 'second',
        ]);

        $chunks = GenerationChunk::where('scenario_generation_id', $gen->id)->orderBy('sequence')->get();
        $this->assertCount(2, $chunks);
        $this->assertEquals('first', $chunks[0]->chunk);
        $this->assertEquals('second', $chunks[1]->chunk);
    }
}
