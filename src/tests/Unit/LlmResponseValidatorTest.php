<?php

use App\Services\LlmResponseValidator;

it('validates a minimal valid llm response', function () {
    $validator = new LlmResponseValidator();

    $payload = [
        'scenario_metadata' => ['name' => 'Test Scenario'],
        'capabilities' => [
            [
                'name' => 'Capability A',
                'competencies' => [
                    [
                        'name' => 'Competency 1',
                        'skills' => [
                            ['name' => 'Skill X']
                        ]
                    ]
                ]
            ]
        ]
    ];

    $result = $validator->validate($payload);
    expect($result['valid'])->toBeTrue();
    expect($result['errors'])->toBe([]);
});

it('returns structured errors for missing fields', function () {
    $validator = new LlmResponseValidator();

    $payload = [
        // missing scenario_metadata.name
        'scenario_metadata' => [],
        'capabilities' => [
            ['competencies' => 'not-an-array']
        ]
    ];

    $result = $validator->validate($payload);
    expect($result['valid'])->toBeFalse();
    expect($result['errors'])->not->toBeEmpty();
    // ensure errors are structured arrays with path and message
    expect(is_array($result['errors'][0]))->toBeTrue();
    expect(array_key_exists('path', $result['errors'][0]))->toBeTrue();
    expect(array_key_exists('message', $result['errors'][0]))->toBeTrue();
});
