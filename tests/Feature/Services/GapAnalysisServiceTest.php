<?php

use App\Services\Intelligence\GapAnalysisService;
use Illuminate\Support\Facades\Http;

it('recommends a strategy by calling the python service', function () {
    Http::fake([
        'http://localhost:8000/analyze-gap' => Http::response([
            'strategy' => 'Build',
            'confidence_score' => 0.9,
            'reasoning_summary' => 'Test reason',
            'action_plan' => ['Step 1', 'Step 2']
        ], 200),
    ]);

    $service = new GapAnalysisService();
    $result = $service->analyzeGap([
        'role_context' => ['role_id' => 1, 'role_name' => 'Tester'],
        'competency_context' => ['competency_name' => 'PHP', 'required_level' => 3, 'current_level' => 1, 'gap_size' => 2],
        'talent_context' => ['current_headcount' => 1, 'talent_status' => 'Active']
    ]);

    expect($result)->toBeArray()
        ->and($result['strategy'])->toBe('Build')
        ->and($result['confidence_score'])->toBe(0.9);
});

it('handles python service failure gracefully', function () {
    Http::fake([
        'http://localhost:8000/analyze-gap' => Http::response([], 500),
    ]);

    $service = new GapAnalysisService();
    $result = $service->analyzeGap([]);

    expect($result)->toBeNull();
});
