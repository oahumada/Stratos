<?php

use Illuminate\Support\Facades\Cache;

it('fails when heartbeat does not exist', function () {
    Cache::forget('lms:sync-progress:last-run');

    $this->artisan('lms:monitor-sync')
        ->assertExitCode(1);
});

it('passes when last success is within lag threshold', function () {
    Cache::forever('lms:sync-progress:last-run', [
        'status' => 'success',
        'started_at' => now()->subMinutes(10)->toIso8601String(),
        'finished_at' => now()->subMinutes(5)->toIso8601String(),
        'duration_ms' => 1000,
        'organization_id' => null,
        'action_id' => null,
        'processed' => 4,
        'updated' => 4,
        'error' => null,
    ]);

    $this->artisan('lms:monitor-sync', ['--max-lag-minutes' => 90])
        ->assertExitCode(0);
});

it('fails when last success exceeds lag threshold', function () {
    Cache::forever('lms:sync-progress:last-run', [
        'status' => 'success',
        'started_at' => now()->subHours(5)->toIso8601String(),
        'finished_at' => now()->subHours(4)->toIso8601String(),
        'duration_ms' => 1500,
        'organization_id' => null,
        'action_id' => null,
        'processed' => 2,
        'updated' => 2,
        'error' => null,
    ]);

    $this->artisan('lms:monitor-sync', ['--max-lag-minutes' => 60])
        ->assertExitCode(1);
});
