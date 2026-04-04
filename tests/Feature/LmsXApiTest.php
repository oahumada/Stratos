<?php

use App\Models\LmsXapiStatement;
use App\Models\Organization;
use App\Models\User;
use App\Services\Lms\XApiService;
use Laravel\Sanctum\Sanctum;

it('can store a single xAPI statement', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson('/api/lms/xapi/statements', [
        'actor' => ['id' => $user->id, 'name' => $user->name, 'mbox' => $user->email],
        'verb' => ['id' => 'http://adlnet.gov/expapi/verbs/completed', 'display' => 'completed'],
        'object' => ['type' => 'Activity', 'id' => 'https://lms.example.com/courses/1', 'name' => 'Test Course'],
        'result' => ['completion' => true, 'success' => true],
        'timestamp' => now()->toIso8601String(),
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_xapi_statements', [
        'verb_id' => 'http://adlnet.gov/expapi/verbs/completed',
        'object_id' => 'https://lms.example.com/courses/1',
        'organization_id' => $org->id,
    ]);
});

it('can store multiple statements', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $statements = [
        [
            'actor' => ['id' => $user->id, 'name' => $user->name],
            'verb' => ['id' => 'http://adlnet.gov/expapi/verbs/attempted', 'display' => 'attempted'],
            'object' => ['type' => 'Activity', 'id' => 'https://lms.example.com/courses/1'],
            'result' => [],
            'timestamp' => now()->toIso8601String(),
        ],
        [
            'actor' => ['id' => $user->id, 'name' => $user->name],
            'verb' => ['id' => 'http://adlnet.gov/expapi/verbs/completed', 'display' => 'completed'],
            'object' => ['type' => 'Activity', 'id' => 'https://lms.example.com/courses/1'],
            'result' => ['completion' => true],
            'timestamp' => now()->toIso8601String(),
        ],
    ];

    $response = $this->postJson('/api/lms/xapi/statements', [
        'statements' => $statements,
    ]);

    $response->assertStatus(201);
    $response->assertJsonFragment(['count' => 2]);
    expect(LmsXapiStatement::where('organization_id', $org->id)->count())->toBe(2);
});

it('can query statements by verb', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    LmsXapiStatement::create([
        'organization_id' => $org->id,
        'actor_id' => $user->id,
        'actor_name' => $user->name,
        'verb_id' => 'http://adlnet.gov/expapi/verbs/completed',
        'verb_display' => 'completed',
        'object_type' => 'Activity',
        'object_id' => 'https://lms.example.com/courses/1',
        'statement_timestamp' => now(),
        'stored_at' => now(),
    ]);

    LmsXapiStatement::create([
        'organization_id' => $org->id,
        'actor_id' => $user->id,
        'actor_name' => $user->name,
        'verb_id' => 'http://adlnet.gov/expapi/verbs/attempted',
        'verb_display' => 'attempted',
        'object_type' => 'Activity',
        'object_id' => 'https://lms.example.com/courses/2',
        'statement_timestamp' => now(),
        'stored_at' => now(),
    ]);

    $response = $this->getJson('/api/lms/xapi/statements?verb=' . urlencode('http://adlnet.gov/expapi/verbs/completed'));
    $response->assertOk();

    $data = $response->json('data');
    expect(count($data))->toBe(1);
    expect($data[0]['verb_id'])->toBe('http://adlnet.gov/expapi/verbs/completed');
});

it('can query statements by actor', function () {
    $org = Organization::factory()->create();
    $user1 = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user1, ['*']);

    LmsXapiStatement::create([
        'organization_id' => $org->id,
        'actor_id' => $user1->id,
        'actor_name' => $user1->name,
        'verb_id' => 'http://adlnet.gov/expapi/verbs/completed',
        'verb_display' => 'completed',
        'object_type' => 'Activity',
        'object_id' => 'https://lms.example.com/courses/1',
        'statement_timestamp' => now(),
        'stored_at' => now(),
    ]);

    LmsXapiStatement::create([
        'organization_id' => $org->id,
        'actor_id' => $user2->id,
        'actor_name' => $user2->name,
        'verb_id' => 'http://adlnet.gov/expapi/verbs/completed',
        'verb_display' => 'completed',
        'object_type' => 'Activity',
        'object_id' => 'https://lms.example.com/courses/2',
        'statement_timestamp' => now(),
        'stored_at' => now(),
    ]);

    $response = $this->getJson("/api/lms/xapi/statements?actor={$user1->id}");
    $response->assertOk();

    $data = $response->json('data');
    expect(count($data))->toBe(1);
    expect($data[0]['actor_id'])->toBe($user1->id);
});

it('can emit LMS event (helper method)', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);

    $service = app(XApiService::class);
    $statement = $service->emitLmsEvent(
        'completed',
        $user->id,
        'https://lms.example.com/courses/42',
        'Advanced Laravel',
        $org->id,
        ['completion' => true, 'success' => true],
    );

    expect($statement)->toBeInstanceOf(LmsXapiStatement::class);
    expect($statement->verb_id)->toBe('http://adlnet.gov/expapi/verbs/completed');
    expect($statement->verb_display)->toBe('completed');
    expect($statement->object_name)->toBe('Advanced Laravel');
    expect($statement->organization_id)->toBe($org->id);
});

it('activity stats aggregation works', function () {
    $org = Organization::factory()->create();
    $user1 = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user1, ['*']);

    $objectId = 'https://lms.example.com/courses/stats-test';

    LmsXapiStatement::create([
        'organization_id' => $org->id,
        'actor_id' => $user1->id,
        'actor_name' => $user1->name,
        'verb_id' => 'http://adlnet.gov/expapi/verbs/completed',
        'verb_display' => 'completed',
        'object_type' => 'Activity',
        'object_id' => $objectId,
        'result_completion' => true,
        'result_score_raw' => 85.0,
        'statement_timestamp' => now(),
        'stored_at' => now(),
    ]);

    LmsXapiStatement::create([
        'organization_id' => $org->id,
        'actor_id' => $user2->id,
        'actor_name' => $user2->name,
        'verb_id' => 'http://adlnet.gov/expapi/verbs/completed',
        'verb_display' => 'completed',
        'object_type' => 'Activity',
        'object_id' => $objectId,
        'result_completion' => true,
        'result_score_raw' => 95.0,
        'statement_timestamp' => now(),
        'stored_at' => now(),
    ]);

    $response = $this->getJson('/api/lms/xapi/activities/' . urlencode($objectId) . '/stats');
    $response->assertOk();
    $response->assertJsonFragment([
        'total_statements' => 2,
        'unique_actors' => 2,
        'completions' => 2,
        'average_score' => 90.0,
    ]);
});

it('enforces multi-tenant isolation on statements', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    $user1 = User::factory()->admin()->create(['current_organization_id' => $org1->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org2->id]);

    LmsXapiStatement::create([
        'organization_id' => $org1->id,
        'actor_id' => $user1->id,
        'actor_name' => $user1->name,
        'verb_id' => 'http://adlnet.gov/expapi/verbs/completed',
        'verb_display' => 'completed',
        'object_type' => 'Activity',
        'object_id' => 'https://lms.example.com/courses/secret',
        'statement_timestamp' => now(),
        'stored_at' => now(),
    ]);

    // User from org2 should not see org1 statements
    Sanctum::actingAs($user2, ['*']);
    $response = $this->getJson('/api/lms/xapi/statements');
    $response->assertOk();

    $data = $response->json('data');
    $orgIds = collect($data)->pluck('organization_id')->unique()->all();
    expect($orgIds)->not->toContain($org1->id);
});
