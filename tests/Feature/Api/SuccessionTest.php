<?php

use App\Models\People;
use App\Models\Roles;
use App\Models\Departments;
use App\Models\Organizations;
use App\Models\User;
use App\Models\PersonMovement;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    
    $this->dept = Departments::factory()->create(['organization_id' => $this->org->id]);
    
    $this->roleA = Roles::factory()->create(['organization_id' => $this->org->id, 'name' => 'Manager']);
    $this->roleB = Roles::factory()->create(['organization_id' => $this->org->id, 'name' => 'Director']);
    
    $this->skill = Skill::factory()->create(['organization_id' => $this->org->id, 'name' => 'Leadership']);
    
    $this->roleB->skills()->attach($this->skill->id, ['required_level' => 4]);

    $this->person = People::factory()->create([
        'organization_id' => $this->org->id,
        'role_id' => $this->roleA->id,
        'department_id' => $this->dept->id,
        'is_high_potential' => true
    ]);

    // Attach skill to person with matching level
    $this->person->skills()->attach($this->skill->id, [
        'role_id' => $this->roleA->id,
        'current_level' => 4,
        'required_level' => 4,
        'is_active' => true
    ]);
});

test('it can calculate successors for a role', function () {
    $response = $this->actingAs($this->user)
        ->getJson("/api/talent/succession/role/{$this->roleB->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'candidates')
        ->assertJsonPath('candidates.0.person.name', $this->person->full_name);
});

test('it can analyze a specific candidate for succession', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/talent/succession/analyze-candidate", [
            'person_id' => $this->person->id,
            'role_id' => $this->roleB->id
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('analysis.readiness_level', 'Listo Ahora (Successor A)'); // High potential + match should be high
});

test('stability score is affected by movements', function () {
    // Create 3 movements in last year
    PersonMovement::factory()->count(3)->create([
        'person_id' => $this->person->id,
        'organization_id' => $this->org->id,
        'type' => 'transfer',
        'movement_date' => now()
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/talent/succession/analyze-candidate", [
            'person_id' => $this->person->id,
            'role_id' => $this->roleB->id
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('analysis.metrics.stability', 40);
});
