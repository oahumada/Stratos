<?php

use App\Models\EventStore;
use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organizationA = Organization::factory()->create();
    $this->organizationB = Organization::factory()->create();

    $this->userA = User::factory()->create([
        'organization_id' => $this->organizationA->id,
    ]);
});

it('requires authentication for ai consent endpoint', function () {
    $this->postJson('/api/compliance/consents/ai-processing', [
        'accepted' => true,
    ])->assertUnauthorized();
});

it('records ai consent in event store', function () {
    $person = People::factory()->create([
        'organization_id' => $this->organizationA->id,
    ]);

    $response = $this->actingAs($this->userA, 'sanctum')
        ->postJson('/api/compliance/consents/ai-processing', [
            'accepted' => true,
            'policy_version' => '2026.03',
            'person_id' => $person->id,
            'notes' => 'Consentimiento explícito para IA',
        ]);

    $response->assertCreated();
    $response->assertJsonPath('data.person_id', $person->id);

    $this->assertDatabaseHas('event_store', [
        'event_name' => 'consent.ai_processing.accepted',
        'organization_id' => $this->organizationA->id,
        'aggregate_id' => (string) $person->id,
        'actor_id' => $this->userA->id,
    ]);
});

it('returns dry run for gdpr purge when confirm is false', function () {
    $role = Roles::factory()->create([
        'organization_id' => $this->organizationA->id,
    ]);

    $skill = Skill::create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Data Protection',
        'scope_type' => 'transversal',
        'status' => 'active',
    ]);

    $person = People::factory()->create([
        'organization_id' => $this->organizationA->id,
        'role_id' => $role->id,
    ]);

    PeopleRoleSkills::create([
        'people_id' => $person->id,
        'role_id' => $role->id,
        'skill_id' => $skill->id,
        'current_level' => 2,
        'required_level' => 3,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->userA, 'sanctum')
        ->postJson('/api/compliance/gdpr/purge', [
            'person_id' => $person->id,
            'confirm' => false,
        ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.status', 'dry_run');
    $response->assertJsonPath('data.impact.people_role_skills_records', 1);

    expect(People::query()->find($person->id))->not->toBeNull();
});

it('executes gdpr purge and writes audit event', function () {
    $role = Roles::factory()->create([
        'organization_id' => $this->organizationA->id,
    ]);

    $skill = Skill::create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Privacy Management',
        'scope_type' => 'transversal',
        'status' => 'active',
    ]);

    $person = People::factory()->create([
        'organization_id' => $this->organizationA->id,
        'role_id' => $role->id,
        'first_name' => 'Ana',
        'last_name' => 'Gomez',
        'email' => 'ana@example.com',
    ]);

    PeopleRoleSkills::create([
        'people_id' => $person->id,
        'role_id' => $role->id,
        'skill_id' => $skill->id,
        'current_level' => 3,
        'required_level' => 4,
        'is_active' => true,
        'notes' => 'dato sensible',
    ]);

    $response = $this->actingAs($this->userA, 'sanctum')
        ->postJson('/api/compliance/gdpr/purge', [
            'person_id' => $person->id,
            'confirm' => true,
            'reason' => 'Solicitud de titular',
        ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.status', 'executed');

    $this->assertSoftDeleted('people', [
        'id' => $person->id,
        'organization_id' => $this->organizationA->id,
        'first_name' => 'ANONYMIZED',
        'last_name' => 'USER',
        'email' => 'deleted+'.$person->id.'@anonymized.local',
    ]);

    $this->assertDatabaseHas('event_store', [
        'event_name' => 'gdpr.purge.executed',
        'organization_id' => $this->organizationA->id,
        'aggregate_id' => (string) $person->id,
        'actor_id' => $this->userA->id,
    ]);

    $event = EventStore::query()->where('event_name', 'gdpr.purge.executed')->first();
    expect($event)->not->toBeNull();
    expect($event->payload['reason'])->toBe('Solicitud de titular');
});

it('blocks gdpr purge for person in another organization', function () {
    $foreignPerson = People::factory()->create([
        'organization_id' => $this->organizationB->id,
    ]);

    $response = $this->actingAs($this->userA, 'sanctum')
        ->postJson('/api/compliance/gdpr/purge', [
            'person_id' => $foreignPerson->id,
            'confirm' => true,
        ]);

    $response->assertNotFound();
});
