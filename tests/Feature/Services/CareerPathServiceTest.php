<?php

use App\Models\Organization;
use App\Models\Roles;
use App\Models\Skill;
use App\Services\Scenario\CareerPathService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('uses neo4j graph traversal when available', function () {
    $org = Organization::factory()->create();
    $roleA = Roles::factory()->create(['organization_id' => $org->id, 'name' => 'Role A']);
    $roleB = Roles::factory()->create(['organization_id' => $org->id, 'name' => 'Role B']);

    Http::fake([
        config('services.python_intel.base_url').'/career-path/pathfinding' => Http::response([
            'source_role' => 'Role A',
            'target_role' => 'Role B',
            'path' => [
                ['role_id' => $roleA->id, 'role_name' => 'Role A', 'similarity' => 1.0],
                ['role_id' => $roleB->id, 'role_name' => 'Role B', 'similarity' => 0.8],
            ],
            'total_similarity' => 0.8,
            'difficulty' => 'Medium',
        ], 200),
    ]);

    $service = app(CareerPathService::class);
    $result = $service->calculateOptimalRoute($roleA->id, $roleB->id);

    expect($result['engine'])->toBe('Neo4j (Knowledge Graph)')
        ->and($result['transferability_score'])->toBe(80.0)
        ->and(count($result['route']))->toBe(2);
});

it('falls back to sql when neo4j fails', function () {
    $org = Organization::factory()->create();
    $roleA = Roles::factory()->create(['organization_id' => $org->id, 'name' => 'Role A']);
    $roleB = Roles::factory()->create(['organization_id' => $org->id, 'name' => 'Role B']);

    Http::fake([
        config('services.python_intel.base_url').'/career-path/pathfinding' => Http::response([], 500),
    ]);

    $skill = Skill::factory()->create();
    $roleA->skills()->attach($skill->id, ['required_level' => 3]);
    $roleB->skills()->attach($skill->id, ['required_level' => 3]);

    $service = app(CareerPathService::class);
    $result = $service->calculateOptimalRoute($roleA->id, $roleB->id);

    expect($result['engine'])->toBe('SQL Fallback (Relational)')
        ->and($result['transferability_score'])->toBe(100.0);
});
