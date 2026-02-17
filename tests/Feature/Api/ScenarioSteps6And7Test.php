<?php

use App\Models\Scenario;
use App\Models\User;
use App\Models\Organization;
use App\Models\ScenarioClosureStrategy;
use App\Models\ScenarioSkillDemand;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    \DB::listen(function($query) {
        \Log::info($query->sql, $query->bindings);
        echo $query->sql . "\n";
    });
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    $this->actingAs($this->user);
});

test('test6', function () {
    $scenario1 = Scenario::factory()->create([
        'organization_id' => $this->organization->id,
        'name' => 'Scenario A',
        'version_number' => 1,
        'owner_user_id' => $this->user->id,
        'created_by' => $this->user->id,
    ]);
    
    $scenario2 = Scenario::factory()->create([
        'organization_id' => $this->organization->id,
        'name' => 'Scenario B',
        'version_number' => 2,
        'owner_user_id' => $this->user->id,
        'created_by' => $this->user->id,
    ]);

    // Mock some data for scenario 1
    ScenarioClosureStrategy::create([
        'scenario_id' => $scenario1->id,
        'strategy' => 'buy',
        'strategy_name' => 'Outsourcing Deal',
        'estimated_cost' => 5000,
        'status' => 'approved',
        'skill_id' => Skill::factory()->create(['organization_id' => $this->organization->id])->id,
        'role_id' => \App\Models\Roles::factory()->create(['organization_id' => $this->organization->id])->id
    ]);

    ScenarioSkillDemand::create([
        'scenario_id' => $scenario1->id,
        'skill_id' => Skill::factory()->create(['organization_id' => $this->organization->id])->id,
        'required_headcount' => 10,
        'current_headcount' => 5,
        'priority' => 'high'
    ]);

    $response = $this->getJson("/api/strategic-planning/scenarios/{$scenario1->id}/compare-versions?ids[]={$scenario1->id}&ids[]={$scenario2->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id', 'name', 'version', 'iq', 'total_cost', 'total_req_fte', 'total_curr_fte', 'gap_fte', 'status', 'created_at'
                ]
            ]
        ]);
        
    $data = $response->json('data');
    expect($data)->toHaveCount(2);
    expect((float)$data[0]['total_cost'])->toBe(5000.0);
    expect($data[0]['gap_fte'])->toBe(5);
});

test('test7', function () {
    $scenario = Scenario::factory()->create([
        'organization_id' => $this->organization->id,
        'owner_user_id' => $this->user->id,
        'created_by' => $this->user->id,
    ]);

    // Mock strategies
    ScenarioClosureStrategy::create([
        'scenario_id' => $scenario->id,
        'strategy' => 'build',
        'strategy_name' => 'Internal Training Program',
        'estimated_cost' => 2000,
        'status' => 'approved',
        'skill_id' => Skill::factory()->create(['organization_id' => $this->organization->id])->id,
        'role_id' => \App\Models\Roles::factory()->create(['organization_id' => $this->organization->id])->id
    ]);
    
    ScenarioClosureStrategy::create([
        'scenario_id' => $scenario->id,
        'strategy' => 'buy',
        'strategy_name' => 'Hiring Campaign',
        'estimated_cost' => 3000,
        'status' => 'approved',
        'skill_id' => Skill::factory()->create(['organization_id' => $this->organization->id])->id,
        'role_id' => \App\Models\Roles::factory()->create(['organization_id' => $this->organization->id])->id
    ]);

    // Mock demand
    $skill = Skill::factory()->create(['name' => 'Critical Skill', 'organization_id' => $this->organization->id]);
    ScenarioSkillDemand::create([
        'scenario_id' => $scenario->id,
        'skill_id' => $skill->id,
        'required_headcount' => 20,
        'current_headcount' => 10,
        'priority' => 'high'
    ]);

    // Mock Talent Blueprint for Synthetization Index
    \App\Models\TalentBlueprint::factory()->create([
        'scenario_id' => $scenario->id,
        'total_fte_required' => 10,
        'synthetic_percentage' => 40, // 40% synthetic leverage
    ]);

    $response = $this->getJson("/api/strategic-planning/scenarios/{$scenario->id}/summary");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'iq',
                'confidence',
                'investment',
                'total_investment',
                'fte' => ['required', 'current', 'gap'],
                'critical_gaps',
                'synthetization_index',
                'risk_level'
            ]
        ]);

    $data = $response->json('data');
    expect((float)$data['total_investment'])->toBe(5000.0);
    expect($data['fte']['gap'])->toBe(10);
    expect((float)$data['synthetization_index'])->toBe(40.0);
    expect($data['risk_level'])->toBe('High');
    expect($data['critical_gaps'])->toHaveCount(1);
    expect($data['critical_gaps'][0]['skill'])->toBe('Critical Skill');
});
