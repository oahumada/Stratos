<?php

namespace Tests\Feature\Jobs;

use App\Jobs\AnalyzeTalentGap;
use App\Models\ScenarioRoleCompetency;
use App\Services\Intelligence\StratosIntelService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('analyzes a talent gap and updates the database with IA strategy', function () {
    // 1. Setup Data - Satisfying foreign key constraints
    $scenario = \App\Models\Scenario::factory()->create();
    $role = \App\Models\Roles::factory()->create();
    
    // Create the mapping in scenario_roles
    $sRole = \App\Models\ScenarioRole::factory()->create([
        'scenario_id' => $scenario->id,
        'role_id' => $role->id,
    ]);

    // Now create the competency gap record
    $gapRecord = ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $scenario->id,
        'role_id' => $sRole->id,
        'required_level' => 3,
    ]);

    // 2. Mock Python Service
    $intelUrl = config('services.python_intel.base_url');
    Http::fake([
        "{$intelUrl}/analyze-gap" => Http::response([
            'strategy' => 'Build',
            'confidence_score' => 0.85,
            'reasoning_summary' => 'The gap is small enough for internal training.',
            'action_plan' => ['Step 1: Training', 'Step 2: Assessment']
        ], 200),
    ]);

    // 3. Execute Job
    $job = new AnalyzeTalentGap($gapRecord->id);
    app()->call([$job, 'handle']);

    // 4. Verify Database
    $gapRecord->refresh();
    expect($gapRecord->suggested_strategy)->toBe('Build')
        ->and($gapRecord->ia_confidence_score)->toBe(0.85)
        ->and($gapRecord->ia_action_plan)->toBe(['Step 1: Training', 'Step 2: Assessment']);
});
