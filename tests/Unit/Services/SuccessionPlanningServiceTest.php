<?php

use App\Models\People;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\SuccessionCandidate;
use App\Services\ScenarioPlanning\SuccessionPlanningService;

it('can calculate skill match between 0 and 100', function () {
    $service = new SuccessionPlanningService;
    $scenario = Scenario::factory()->create();
    $person = People::factory()->create();
    $targetRole = Roles::factory()->create(['organization_id' => $scenario->organization_id]);

    $score = $service->calculateSkillMatch($person, $targetRole, $scenario);

    expect($score)->toBeGreaterThanOrEqual(0);
    expect($score)->toBeLessThanOrEqual(100);
});

it('can assess readiness level', function () {
    $service = new SuccessionPlanningService;
    $candidate = SuccessionCandidate::factory()->create();

    $level = $service->assessReadiness($candidate);

    expect($level)->toBeIn(['junior', 'intermediate', 'senior', 'expert']);
});

it('can calculate succession coverage', function () {
    $service = new SuccessionPlanningService;
    $scenario = Scenario::factory()->create();
    Roles::factory()->create([
        'organization_id' => $scenario->organization_id,
    ]);

    $coverage = $service->calculateSuccessionCoverage($scenario);

    expect($coverage)->toBeArray();
});

it('can identify high risk talent', function () {
    $service = new SuccessionPlanningService;
    $lowSkillCandidate = SuccessionCandidate::factory()->create(['skill_match_score' => 20]);
    $highSkillCandidate = SuccessionCandidate::factory()->create(['skill_match_score' => 90]);

    $lowLevel = $service->assessReadiness($lowSkillCandidate);
    $highLevel = $service->assessReadiness($highSkillCandidate);

    $levelMap = ['junior' => 1, 'intermediate' => 2, 'senior' => 3, 'expert' => 4];
    expect($levelMap[$highLevel])->toBeGreaterThanOrEqual($levelMap[$lowLevel]);
});
