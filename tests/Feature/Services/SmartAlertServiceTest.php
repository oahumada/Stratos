<?php

use App\Models\Organization;
use App\Services\Intelligence\SmartAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('can create a smart notification', function () {
    $org = Organization::factory()->create();
    $service = new SmartAlertService;

    $service->notify(
        $org->id,
        'Critical Gap Found',
        'Architecture gap in Backend Engineer detected',
        'danger',
        'talent',
        ['text' => 'Ver Dashboard', 'url' => '/scenario/overview']
    );

    expect(DB::table('smart_alerts')->where('organization_id', $org->id)->count())->toBe(1);

    $alert = DB::table('smart_alerts')->where('organization_id', $org->id)->first();
    expect($alert->level)->toBe('danger')
        ->and(json_decode($alert->action_link)->url)->toBe('/scenario/overview');
});

it('can retrieve active alerts', function () {
    $org = Organization::factory()->create();
    $service = new SmartAlertService;

    $service->notify($org->id, 'Alert 1', 'Msg 1');
    $service->notify($org->id, 'Alert 2', 'Msg 2');

    $alerts = $service->getActiveAlerts($org->id);
    expect(count($alerts))->toBe(2);
});
