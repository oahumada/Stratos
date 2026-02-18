<?php

use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Models\ScenarioRoleSkill;
use App\Models\Roles;
use App\Models\Competency;
use App\Models\Skill;
use App\Models\Organization;
use App\Models\User;
use App\Services\Intelligence\GapAnalysisService;
use App\Jobs\AnalyzeTalentGap;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->actingAs($this->user);
});

it('orchestrates end-to-end ai strategy generation', function () {
    // 1. Setup Data Complex
    $scenario = Scenario::factory()->create(['organization_id' => $this->org->id]);
    $role = Roles::factory()->create(['organization_id' => $this->org->id]);
    $sRole = ScenarioRole::factory()->create([
        'scenario_id' => $scenario->id,
        'role_id' => $role->id,
    ]);
    $competency = Competency::factory()->create(['organization_id' => $this->org->id]);
    $skill = Skill::factory()->create(['organization_id' => $this->org->id]);

    // Link competency to skill
    \Illuminate\Support\Facades\DB::table('competency_skills')->insert([
        'competency_id' => $competency->id,
        'skill_id' => $skill->id,
        'weight' => 100,
    ]);

    // Create the competency gap
    $cGap = ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $scenario->id,
        'role_id' => $sRole->id,
        'competency_id' => $competency->id,
        'required_level' => 4,
    ]);

    // Create the derived skill gap (mimicking derivation service)
    ScenarioRoleSkill::create([
        'scenario_id' => $scenario->id,
        'role_id' => $sRole->id,
        'skill_id' => $skill->id,
        'competency_id' => $competency->id,
        'required_level' => 4,
        'current_level' => 1,
        'source' => 'competency',
    ]);

    // 2. Mock AI Service
    Http::fake([
        'http://localhost:8000/analyze-gap' => Http::response([
            'strategy' => 'Borrow',
            'confidence_score' => 0.92,
            'reasoning_summary' => 'Need external experts for rapid growth.',
            'action_plan' => ['Hire 2 freelancers']
        ], 200),
    ]);

    // 3. Trigger Refresh (Job dispatching is synchronous in tests by default if no queue fake)
    // Actually let's use Queue::fake to verify dispatching first, then run job.
    Queue::fake();

    $response = $this->postJson("/api/strategic-planning/scenarios/{$scenario->id}/refresh-suggested-strategies");
    $response->assertStatus(200);

    Queue::assertPushed(AnalyzeTalentGap::class);

    // 4. Manually run the job to verify the rest of the chain
    $job = new AnalyzeTalentGap($cGap->id);
    $job->handle(new GapAnalysisService());

    // 5. Verify official strategy table
    $this->assertDatabaseHas('scenario_closure_strategies', [
        'scenario_id' => $scenario->id,
        'skill_id' => $skill->id,
        'strategy' => 'borrow',
        'is_ia_generated' => true,
    ]);

    $strategy = \App\Models\ScenarioClosureStrategy::where('scenario_id', $scenario->id)->first();
    expect($strategy->ia_confidence_score)->toBe(0.92)
        ->and($strategy->ia_strategy_rationale)->toBe('Need external experts for rapid growth.');
});
