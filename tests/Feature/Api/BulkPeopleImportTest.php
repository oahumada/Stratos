<?php

use App\Models\ChangeSet;
use App\Models\Departments;
use App\Models\Organization;
use App\Models\OrganizationSnapshot;
use App\Models\People;
use App\Models\Roles;
use App\Models\User;

beforeEach(function () {
    $this->org = Organization::create(['name' => 'Cyberdyne Systems', 'subdomain' => 'cyberdyne']);
    $this->user = User::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Sarah Connor',
    ]);

    // Create some existing data
    $this->dept = Departments::create([
        'organization_id' => $this->org->id,
        'name' => 'Research & Development',
        'aliases' => ['R&D', 'Labs'],
    ]);

    $this->role = Roles::create([
        'organization_id' => $this->org->id,
        'name' => 'AI Engineer',
        'status' => 'active',
    ]);

    $this->existingPerson = People::create([
        'organization_id' => $this->org->id,
        'first_name' => 'Miles',
        'last_name' => 'Dyson',
        'email' => 'm.dyson@cyberdyne.com',
        'department_id' => $this->dept->id,
        'role_id' => $this->role->id,
    ]);
});

test('it can analyze bulk import data and detect movements', function () {
    $data = [
        'rows' => [
            [
                'first_name' => 'Miles',
                'last_name' => 'Dyson',
                'email' => 'm.dyson@cyberdyne.com',
                'department' => 'Advanced Robotics',
                'role' => 'Principal Architect',
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Connor',
                'email' => 'j.connor@resistance.io',
                'department' => 'Tactical Operations',
                'role' => 'Leader',
            ],
        ],
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/talent/bulk-import/analyze', $data);

    $response->assertStatus(200)
        ->assertJsonPath('analysis.people_count', 2)
        ->assertJsonPath('analysis.movements.hires.0.email', 'j.connor@resistance.io')
        ->assertJsonPath('analysis.movements.transfers.0.email', 'm.dyson@cyberdyne.com');

    // Test exit detection
    $otherPerson = People::create([
        'organization_id' => $this->org->id,
        'first_name' => 'T',
        'last_name' => '800',
        'email' => 'terminator@skynet.com',
    ]);

    $response = $this->actingAs($this->user)
        ->postJson('/api/talent/bulk-import/analyze', $data);

    $response->assertJsonPath('analysis.movements.exits.0.email', 'terminator@skynet.com');
});

test('it can stage an import and create a changeset', function () {
    $data = [
        'rows' => [['email' => 'test@test.com', 'department' => 'HR', 'role' => 'Manager']],
        'mapping' => [
            'departments' => [['raw_name' => 'HR', 'status' => 'new', 'suggested_name' => 'Human Resources']],
            'roles' => [['raw_name' => 'Manager', 'status' => 'new', 'suggested_name' => 'Area Manager']],
            'movements' => ['hires' => [['email' => 'test@test.com']], 'transfers' => [], 'exits' => []],
        ],
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/talent/bulk-import/stage', $data);

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'change_set_id']);

    $this->assertDatabaseHas('change_sets', [
        'organization_id' => $this->org->id,
        'status' => 'draft',
    ]);
});

test('it can approve and commit an import establishing a baseline', function () {
    $changeSet = ChangeSet::create([
        'organization_id' => $this->org->id,
        'status' => 'draft',
        'title' => 'Bulk Import Test',
        'diff' => [
            'rows' => [
                [
                    'first_name' => 'John',
                    'last_name' => 'Connor',
                    'email' => 'j.connor@resistance.io',
                    'department' => 'Tactical Ops',
                    'role' => 'Leader',
                ],
            ],
            'mapping' => [
                'departments' => [
                    ['raw_name' => 'Tactical Ops', 'status' => 'new', 'suggested_name' => 'Tactical Operations'],
                ],
                'roles' => [
                    ['raw_name' => 'Leader', 'status' => 'new', 'suggested_name' => 'Strategic Leader'],
                ],
                'movements' => [
                    'hires' => [['email' => 'j.connor@resistance.io']],
                    'exits' => [['email' => 'm.dyson@cyberdyne.com']],
                ],
            ],
        ],
        'metadata' => ['source' => 'test'],
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/talent/bulk-import/{$changeSet->id}/approve", [
            'signature' => 'SARAH_CONNOR_DIGITAL_SIG',
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('departments', ['name' => 'Tactical Operations']);
    $this->assertDatabaseHas('roles', ['name' => 'Strategic Leader']);
    $this->assertDatabaseHas('people', [
        'email' => 'j.connor@resistance.io',
        'first_name' => 'John',
    ]);

    $this->assertSoftDeleted('people', [
        'email' => 'm.dyson@cyberdyne.com',
    ]);

    // Verify Movement tracking
    $this->assertDatabaseHas('person_movements', [
        'organization_id' => $this->org->id,
        'type' => 'exit',
    ]);

    $this->assertDatabaseHas('person_movements', [
        'organization_id' => $this->org->id,
        'type' => 'hire',
    ]);

    $this->assertDatabaseHas('organization_snapshots', [
        'organizations_id' => $this->org->id,
    ]);

    $snapshot = OrganizationSnapshot::where('organizations_id', $this->org->id)->latest('id')->first();
    expect($snapshot->metadata['event'])->toBe('bulk_sync');
});
