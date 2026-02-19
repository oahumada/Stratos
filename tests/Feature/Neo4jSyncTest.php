<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Gate;
use App\Jobs\RunNeo4jSyncJob;
use App\Models\User;

it('artisan command triggers fastapi endpoint', function () {
    Http::fake(['*' => Http::response([], 200)]);
    // Ensure config for service URL exists so the command will call it
    config(['services.neo4j_etl.url' => 'http://example.test']);

    $this->artisan('neo4j:sync --via=fastapi')->assertExitCode(0);
});

it('api endpoint dispatches job when authorized', function () {
    Bus::fake();

    $user = User::factory()->create();
    // avoid persisting non-existing column; set in-memory flag
    $user->is_admin = true;

    // Ensure Gate allows the action in this test
    Gate::define('sync-neo4j', fn ($u) => true);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/neo4j/sync', ['via' => 'fastapi'])
        ->assertStatus(200)
        ->assertJsonFragment(['status' => 'dispatched']);

    Bus::assertDispatched(RunNeo4jSyncJob::class);
});
