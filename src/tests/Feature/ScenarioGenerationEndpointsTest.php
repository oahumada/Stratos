<?php

use App\Jobs\GenerateScenarioFromLLMJob;
use App\Models\Organizations;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('returns prompt preview for authenticated user', function () {
    $org = Organizations::create(['name' => 'ACME Corp', 'subdomain' => 'acme', 'industry' => 'tech', 'size' => 'small']);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $payload = ['company_name' => 'ACME Corp', 'organization_id' => $org->id];

    $this->actingAs($user)
        ->postJson('/api/strategic-planning/scenarios/generate/preview', $payload)
        ->assertStatus(200)
        ->assertJsonStructure(['success', 'data' => ['prompt']]);
});

it('enqueues generation on store and returns 202', function () {
    Queue::fake();

    $org = Organizations::create(['name' => 'ACME Corp', 'subdomain' => 'acme', 'industry' => 'tech', 'size' => 'small']);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $payload = ['company_name' => 'ACME Corp', 'organization_id' => $org->id];

    $this->actingAs($user)
        ->postJson('/api/strategic-planning/scenarios/generate', $payload)
        ->assertStatus(202)
        ->assertJson(['success' => true]);

    Queue::assertPushed(GenerateScenarioFromLLMJob::class);
});
