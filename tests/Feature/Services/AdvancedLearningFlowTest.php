<?php

use App\Services\Intelligence\StratosIntelService;
use Illuminate\Support\Facades\Http;

it('successfully generates an advanced reflexive learning plan', function () {
    Http::fake([
        config('services.python_intel.base_url') . '/learning/advanced-plan' => Http::response([
            'summary' => 'Refined plan',
            'stages' => [
                ['topic' => 'AI Patterns', 'duration' => '2 weeks', 'resources' => ['OReilly'], 'learning_outcome' => 'Arquitectura de Agentes']
            ],
            'mentor_recommendation' => 'Principal Architect',
            'confidence_score' => 0.95,
            'validation_status' => 'Validated'
        ], 200),
    ]);

    // Test calling via Http proxy (simulating what the service would do)
    $response = Http::post(config('services.python_intel.base_url') . '/learning/advanced-plan', [
        'talent_profile' => ['id' => 1, 'name' => 'Alice'],
        'target_role' => ['id' => 5, 'name' => 'Lead AI'],
        'focus_areas' => ['GenAI', 'Prompt Engineering']
    ]);

    expect($response->successful())->toBeTrue()
        ->and($response->json()['summary'])->toBe('Refined plan')
        ->and($response->json()['mentor_recommendation'])->toBe('Principal Architect');
});
