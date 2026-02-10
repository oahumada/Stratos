<?php

use App\Models\ScenarioGeneration;
use App\Models\GenerationChunk;
use App\Models\User;
use App\Models\Organization;

it('returns decoded compacted blob when metadata.compacted exists', function () {
    Organization::factory()->create(['id' => 1]);
    $user = User::factory()->create(['organization_id' => 1]);

    $payload = ['foo' => 'bar', 'n' => 1];
    $compact = base64_encode(json_encode($payload));

    $gen = ScenarioGeneration::create([
        'organization_id' => 1,
        'prompt' => 'test',
        'status' => 'complete',
        'metadata' => ['compacted' => $compact],
    ]);

    $resp = $this->actingAs($user)->getJson("/api/strategic-planning/scenarios/generate/{$gen->id}/compacted");
    $resp->assertStatus(200)->assertJson(['success' => true]);
    $data = $resp->json('data');
    expect($data['foo'])->toBe('bar');
    expect($data['n'])->toBe(1);
});

it('assembles response from chunks when compacted missing', function () {
    Organization::factory()->create(['id' => 1]);
    $user = User::factory()->create(['organization_id' => 1]);

    $gen = ScenarioGeneration::create([
        'organization_id' => 1,
        'prompt' => 'test 2',
        'status' => 'processing',
        'metadata' => [],
    ]);

    // create two chunks that together form a valid JSON string
    GenerationChunk::create(['scenario_generation_id' => $gen->id, 'sequence' => 1, 'chunk' => '{"foo":"']);
    GenerationChunk::create(['scenario_generation_id' => $gen->id, 'sequence' => 2, 'chunk' => 'bar"}']);

    $resp = $this->actingAs($user)->getJson("/api/strategic-planning/scenarios/generate/{$gen->id}/compacted");
    $resp->assertStatus(200)->assertJson(['success' => true]);
    $data = $resp->json('data');
    expect($data['foo'])->toBe('bar');
});
