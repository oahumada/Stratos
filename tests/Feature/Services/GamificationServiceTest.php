<?php

use App\Models\Organization;
use App\Models\People;
use App\Services\Talent\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('can award points to a person', function () {
    $org = Organization::factory()->create();
    $person = People::factory()->create(['organization_id' => $org->id]);
    $service = new GamificationService;

    $service->awardPoints($person->id, 100, 'Skill Level Up', ['skill' => 'PHP']);

    expect(DB::table('people_points')->count())->toBe(1);

    $points = DB::table('people_points')->where('people_id', $person->id)->first();
    expect($points->points)->toBe(100)
        ->and(json_decode($points->meta)->skill)->toBe('PHP');
});

it('can award a badge to a person', function () {
    $org = Organization::factory()->create();
    $person = People::factory()->create(['organization_id' => $org->id]);
    $service = new GamificationService;

    // Seed a badge first
    DB::table('badges')->insert([
        'name' => 'Visionary',
        'slug' => 'visionary',
        'description' => 'Test',
        'icon' => 'mdi-brain',
        'color' => 'blue',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $service->awardBadge($person->id, 'visionary');

    expect(DB::table('people_badges')->where('people_id', $person->id)->count())->toBe(1);
});
