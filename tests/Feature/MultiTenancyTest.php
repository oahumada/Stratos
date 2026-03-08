<?php

use App\Models\AssessmentSession;
use App\Models\Competency;
use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use App\Models\Scenario;
use App\Models\Skill;

beforeEach(function () {
    $this->org1 = Organization::factory()->create(['name' => 'Org 1']);
    $this->org2 = Organization::factory()->create(['name' => 'Org 2']);
    
    $this->admin1 = User::factory()->create(['organization_id' => $this->org1->id, 'role' => 'admin']);
    $this->admin2 = User::factory()->create(['organization_id' => $this->org2->id, 'role' => 'admin']);
});

it('enforces automatic organization assignment on creation for all models using the trait', function () {
    $this->actingAs($this->admin1, 'sanctum');
    
    // Scenario (uses trait + factory to satisfy constraints)
    $scenario = Scenario::factory()->create([
        'name' => 'Org 1 Scenario',
        'organization_id' => null // Force trait to set it
    ]);
    expect($scenario->organization_id)->toBe($this->org1->id);
    
    // Skill (uses trait)
    $skill = Skill::create([
        'name' => 'Org 1 Skill',
    ]);
    expect($skill->organization_id)->toBe($this->org1->id);
    
    // People (uses trait)
    $person = People::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@org1.com'
    ]);
    expect($person->organization_id)->toBe($this->org1->id);
});

it('prevents cross-tenant data leakage via global scope', function () {
    // Create data for Org 1
    Scenario::factory()->create(['organization_id' => $this->org1->id, 'name' => 'Scenario Org 1']);
    Scenario::factory()->create(['organization_id' => $this->org1->id, 'name' => 'Scenario Org 1 B']);
    
    // Create data for Org 2
    Scenario::factory()->create(['organization_id' => $this->org2->id, 'name' => 'Scenario Org 2']);
    
    // Acting as Admin 1
    $this->actingAs($this->admin1, 'sanctum');
    expect(Scenario::count())->toBe(2);
    expect(Scenario::where('name', 'Scenario Org 2')->exists())->toBeFalse();
    
    // Acting as Admin 2
    $this->actingAs($this->admin2, 'sanctum');
    expect(Scenario::count())->toBe(1);
    expect(Scenario::where('name', 'Scenario Org 1')->exists())->toBeFalse();
    expect(Scenario::first()->name)->toBe('Scenario Org 2');
});

it('denies access to resources via API when they belong to another organization', function () {
    $scenario1 = Scenario::factory()->create([
        'organization_id' => $this->org1->id,
        'name' => 'Private Scenario Org 1'
    ]);
    
    // Admin 2 tries to access Scenario 1 via API
    $response = $this->actingAs($this->admin2, 'sanctum')
        ->getJson("/api/strategic-planning/scenarios/{$scenario1->id}");
    
    // Should return 404 because the global scope makes it "not exist" for the query
    $response->assertStatus(404);
});

it('prevents relationship hijacking across organizations', function () {
    // Scenario in Org 1
    $scenario1 = Scenario::factory()->create(['organization_id' => $this->org1->id, 'name' => 'Scenario Org 1']);
    
    // Skill in Org 2
    $skillOrg2 = Skill::create(['organization_id' => $this->org2->id, 'name' => 'Foreign Skill']);
    
    $this->actingAs($this->admin1, 'sanctum');
    
    // Admin 1 tries to find the skill from Org 2
    $foreignSkill = Skill::find($skillOrg2->id);
    expect($foreignSkill)->toBeNull();
});
