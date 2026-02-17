<?php

use App\Models\Scenario;
use App\Models\User;
use Database\Seeders\Phase3TestDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

beforeEach(function () {
    $this->seed(Phase3TestDataSeeder::class);
    $this->user = User::first();
    $this->scenario = Scenario::first();
});

test('it returns real headcount in role forecasts', function () {
    $response = $this->actingAs($this->user)
        ->getJson("/api/scenarios/{$this->scenario->id}/step2/role-forecasts");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'role_id',
                'role_name',
                'fte_current',
                'fte_future',
                'fte_delta',
            ]
        ]
    ]);

    // Alice and Bob have roleDev (id calculated in seeder)
    // Charlie has roleAI
    $data = $response->json('data');
    
    $aiLead = collect($data)->firstWhere('role_name', Phase3TestDataSeeder::ROLE_AI_LEAD);
    $cloudDev = collect($data)->firstWhere('role_name', Phase3TestDataSeeder::ROLE_CLOUD_DEV);

    expect($aiLead['fte_current'])->toEqual(1.0); // Charlie
    expect($cloudDev['fte_current'])->toEqual(2.0); // Alice & Bob
});

test('it calculates matching results based on skills', function () {
    $response = $this->actingAs($this->user)
        ->getJson("/api/scenarios/{$this->scenario->id}/step2/matching-results");

    $data = $response->json('data');

    // Alice has 100% match for AI Lead in our seeder logic (GenAI 5, Strategic 4)
    $aliceMatch = collect($data)->firstWhere('candidate_name', 'Alice Ready');
    expect($aliceMatch)->not->toBeNull();
    expect($aliceMatch['match_percentage'])->toEqual(100.0);
    expect($aliceMatch['target_position'])->toBe(Phase3TestDataSeeder::ROLE_AI_LEAD);

    // Bob shouldn't match AI Lead well
    $bobMatch = collect($data)->firstWhere('candidate_name', 'Bob Developer');
    if ($bobMatch && $bobMatch['target_position'] === Phase3TestDataSeeder::ROLE_AI_LEAD) {
        expect($bobMatch['match_percentage'])->toBeLessThan(100);
    }
});

test('it generates succession plans for critical roles', function () {
    $response = $this->actingAs($this->user)
        ->getJson("/api/scenarios/{$this->scenario->id}/step2/succession-plans");

    $response->assertStatus(200);
    $data = $response->json('data');

    $aiPlan = collect($data)->firstWhere('role_name', Phase3TestDataSeeder::ROLE_AI_LEAD);
    expect($aiPlan)->not->toBeNull();
    expect($aiPlan['criticality_level'])->toBe('critical');
    expect($aiPlan['current_holder'])->toBe('Charlie Holder');
    expect($aiPlan['primary_successor']['name'])->toBe('Alice Ready');
});

test('it returns skill gaps matrix', function () {
    $response = $this->actingAs($this->user)
        ->getJson("/api/scenarios/{$this->scenario->id}/step2/skill-gaps-matrix");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'roles' => [
            '*' => [
                'id',
                'name',
                'fte',
            ]
        ],
        'skills' => [
            '*' => [
                'id',
                'name',
                'competency_id',
                'competency_name',
            ]
        ],
        'gaps' => [
            '*' => [
                'skill_id',
                'role_id',
                'role_name',
                'required_level',
                'current_level',
                'gap',
            ]
        ],
    ]);
});
