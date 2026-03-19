<?php

use App\Models\EventStore;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organizationA = Organization::factory()->create();
    $this->organizationB = Organization::factory()->create();

    $this->userA = User::factory()->create([
        'organization_id' => $this->organizationA->id,
    ]);
});

it('requires authentication to list audit events', function () {
    $this->getJson('/api/compliance/audit-events')->assertUnauthorized();
});

it('lists only audit events of authenticated user organization', function () {
    EventStore::create([
        'id' => (string) Str::uuid(),
        'event_name' => 'role.requirements_updated',
        'aggregate_type' => 'App\\Models\\Roles',
        'aggregate_id' => '101',
        'organization_id' => $this->organizationA->id,
        'actor_id' => $this->userA->id,
        'payload' => ['source' => 'test-a'],
        'occurred_at' => now()->subMinutes(10),
    ]);

    EventStore::create([
        'id' => (string) Str::uuid(),
        'event_name' => 'role.requirements_updated',
        'aggregate_type' => 'App\\Models\\Roles',
        'aggregate_id' => '102',
        'organization_id' => $this->organizationA->id,
        'actor_id' => $this->userA->id,
        'payload' => ['source' => 'test-a-2'],
        'occurred_at' => now()->subMinutes(5),
    ]);

    EventStore::create([
        'id' => (string) Str::uuid(),
        'event_name' => 'role.requirements_updated',
        'aggregate_type' => 'App\\Models\\Roles',
        'aggregate_id' => '201',
        'organization_id' => $this->organizationB->id,
        'actor_id' => null,
        'payload' => ['source' => 'test-b'],
        'occurred_at' => now()->subMinutes(1),
    ]);

    $response = $this->actingAs($this->userA, 'sanctum')
        ->getJson('/api/compliance/audit-events?per_page=50');

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);
    $response->assertJsonCount(2, 'data.data');

    $organizationIds = collect($response->json('data.data'))->pluck('organization_id')->unique()->values()->all();
    expect($organizationIds)->toBe([$this->organizationA->id]);
});

it('returns summary scoped to authenticated user organization', function () {
    EventStore::create([
        'id' => (string) Str::uuid(),
        'event_name' => 'role.requirements_updated',
        'aggregate_type' => 'App\\Models\\Roles',
        'aggregate_id' => '301',
        'organization_id' => $this->organizationA->id,
        'actor_id' => $this->userA->id,
        'payload' => ['source' => 'summary-a'],
        'occurred_at' => now()->subHours(2),
    ]);

    EventStore::create([
        'id' => (string) Str::uuid(),
        'event_name' => 'role.requirements_updated',
        'aggregate_type' => 'App\\Models\\Roles',
        'aggregate_id' => '302',
        'organization_id' => $this->organizationB->id,
        'actor_id' => null,
        'payload' => ['source' => 'summary-b'],
        'occurred_at' => now()->subHours(2),
    ]);

    $response = $this->actingAs($this->userA, 'sanctum')
        ->getJson('/api/compliance/audit-events/summary');

    $response->assertSuccessful();
    $response->assertJsonPath('data.total_events', 1);
    $response->assertJsonPath('data.unique_event_names', 1);
    $response->assertJsonPath('data.unique_aggregates', 1);
});
