<?php

use App\Models\Organization;
use App\Models\Person;
use App\Models\Role;
use App\Models\Skill;
use App\Services\GapAnalysisService;

it('calcula brechas y porcentaje de match correctamente con datos deterministas', function () {
    // Arrange: crear organización, skills, rol y persona con relaciones deterministas
    $org = Organization::create([
        'name' => 'TestOrg',
        'subdomain' => 'testorg',
        'industry' => 'Testing',
        'size' => 'small',
    ]);

    $skillA = Skill::create(['organization_id' => $org->id, 'name' => 'Skill A', 'category' => 'technical']);
    $skillB = Skill::create(['organization_id' => $org->id, 'name' => 'Skill B', 'category' => 'technical']);
    $skillC = Skill::create(['organization_id' => $org->id, 'name' => 'Skill C', 'category' => 'soft']);

    $role = Role::create([
        'organization_id' => $org->id,
        'name' => 'Role Test',
        'department' => 'QA',
        'level' => 'mid',
    ]);

    // Requerimientos: A=3 (critical), B=2 (no critical), C=4 (critical)
    $role->skills()->attach([
        $skillA->id => ['required_level' => 3, 'is_critical' => true],
        $skillB->id => ['required_level' => 2, 'is_critical' => false],
        $skillC->id => ['required_level' => 4, 'is_critical' => true],
    ]);

    $person = Person::create([
        'organization_id' => $org->id,
        'first_name' => 'Alice',
        'last_name' => 'Tester',
        'email' => 'alice@testorg.com',
    ]);

    // Niveles de persona: A=3 (ok), B=1 (gap=1 → developing), C=0 (gap=4 → critical)
    $person->skills()->attach([
        $skillA->id => ['level' => 3],
        $skillB->id => ['level' => 1],
        // C sin nivel (0)
    ]);

    // Act
    $service = new GapAnalysisService();
    $result = $service->calculate($person, $role);

    // Assert: porcentaje, conteos y estatus
    expect($result)
        ->toHaveKey('match_percentage')
        ->and($result['match_percentage'])->toBeGreaterThanOrEqual(33.3) // 1 de 3 skills ok → ~33.33%
        ->and($result['match_percentage'])->toBeLessThanOrEqual(33.34)
        ->and($result['summary']['skills_ok'])->toBe(1)
        ->and($result['summary']['total_skills'])->toBe(3)
        ->and($result['gaps'])->toBeArray()
        ->and(count($result['gaps']))->toBe(3);

    // Verificar estados por skill
    $byName = collect($result['gaps'])->keyBy('skill_name');
    expect($byName['Skill A']['status'])->toBe('ok');
    expect($byName['Skill B']['status'])->toBe('developing');
    expect($byName['Skill C']['status'])->toBe('critical');
});
