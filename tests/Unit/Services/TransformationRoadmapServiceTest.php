<?php

use App\Models\Scenario;
use App\Models\TransformationPhase;
use App\Services\ScenarioPlanning\TransformationRoadmapService;

it('can generate transformation plan', function () {
    $service = new TransformationRoadmapService;
    $scenario = Scenario::factory()->create(['time_horizon_weeks' => 52]);

    $service->generateTransformationPlan($scenario);

    $phases = TransformationPhase::where('scenario_id', $scenario->id)->count();

    expect($phases)->toBeGreaterThan(0);
});

it('generated phases have proper structure', function () {
    $service = new TransformationRoadmapService;
    $scenario = Scenario::factory()->create(['time_horizon_weeks' => 52]);

    $service->generateTransformationPlan($scenario);

    $phases = TransformationPhase::where('scenario_id', $scenario->id)
        ->orderBy('phase_number')
        ->get();

    expect($phases->count())->toBeGreaterThan(0);
    expect($phases[0]->phase_number)->toBe(1);
    expect($phases[0]->start_month)->toBeInt();
});

it('can identify blockers for phase', function () {
    $service = new TransformationRoadmapService;
    $scenario = Scenario::factory()->create();
    $phase = TransformationPhase::factory()->create(['scenario_id' => $scenario->id]);

    $blockers = $service->identifyBlockers($phase);

    expect($blockers)->toBeArray();
});

it('can generate roadmap', function () {
    $service = new TransformationRoadmapService;
    $scenario = Scenario::factory()->create(['time_horizon_weeks' => 52]);

    $service->generateTransformationPlan($scenario);
    $roadmap = $service->getRoadmap($scenario);

    expect($roadmap)->toBeArray();
});
