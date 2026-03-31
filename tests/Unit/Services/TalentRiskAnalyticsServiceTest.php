<?php

use App\Models\Scenario;
use App\Models\TalentRiskIndicator;
use App\Services\ScenarioPlanning\TalentRiskAnalyticsService;

it('can analyze volatility risk', function () {
    $service = new TalentRiskAnalyticsService;
    $person = \App\Models\People::factory()->create();
    $org = $person->organization;

    $score = $service->analyzeVolatilityRisk($person, $org);

    expect($score)->toBeGreaterThanOrEqual(0);
    expect($score)->toBeLessThanOrEqual(100);
});

it('can predict retention risk', function () {
    $service = new TalentRiskAnalyticsService;
    $person = \App\Models\People::factory()->create();
    $scenario = Scenario::factory()->create(['organization_id' => $person->organization_id]);

    $score = $service->predictRetentionRisk($person, $scenario);

    expect($score)->toBeGreaterThanOrEqual(0);
    expect($score)->toBeLessThanOrEqual(100);
});

it('can calculate skill gaps', function () {
    $service = new TalentRiskAnalyticsService;
    $scenario = Scenario::factory()->create();

    $gaps = $service->calculateSkillGaps($scenario);

    expect($gaps)->toBeArray();
});

it('can identify high risk talent', function () {
    $service = new TalentRiskAnalyticsService;
    $scenario = Scenario::factory()->create();
    TalentRiskIndicator::factory(3)->create([
        'scenario_id' => $scenario->id,
        'risk_score' => 85,
    ]);

    $highRisk = $service->identifyHighRiskTalent($scenario);

    expect($highRisk)->toBeArray();
});
