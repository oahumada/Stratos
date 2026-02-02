<?php

use App\Models\WorkforcePlan;

it('creates a workforce plan via factory', function () {
    $plan = WorkforcePlan::factory()->create();

    expect($plan)->toBeInstanceOf(WorkforcePlan::class);
    $this->assertDatabaseHas('workforce_plans', ['id' => $plan->id]);
});

it('generates a code if none provided', function () {
    $plan = WorkforcePlan::factory()->create(['code' => null]);

    expect($plan->code)->toBeString()->not->toBeEmpty();
});
