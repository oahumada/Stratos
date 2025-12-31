<?php

use App\Models\Organization;
use App\Models\People;
use App\Models\Role;
use App\Models\Skill;
use App\Models\User;
use App\Models\JobOpening;
use App\Services\MatchingService;

it('ordena candidatos por mayor porcentaje de match', function () {
    // Arrange: organización y usuario creador
    $org = Organization::create([
        'name' => 'TestOrg',
        'subdomain' => 'rankorg',
        'industry' => 'Testing',
        'size' => 'small',
    ]);

    $creator = User::create([
        'organization_id' => $org->id,
        'name' => 'Creator',
        'email' => 'creator@rankorg.com',
        'password' => 'password',
        'role' => 'admin',
    ]);

    // Skills y rol requerido
    $skillA = Skill::create(['organization_id' => $org->id, 'name' => 'Skill A', 'category' => 'technical']);
    $skillB = Skill::create(['organization_id' => $org->id, 'name' => 'Skill B', 'category' => 'technical']);
    $skillC = Skill::create(['organization_id' => $org->id, 'name' => 'Skill C', 'category' => 'soft']);

    $role = Role::create([
        'organization_id' => $org->id,
        'name' => 'Role Rank',
        'department' => 'QA',
        'level' => 'mid',
    ]);

    // Requerimientos
    $role->skills()->attach([
        $skillA->id => ['required_level' => 3, 'is_critical' => true],
        $skillB->id => ['required_level' => 2, 'is_critical' => false],
        $skillC->id => ['required_level' => 4, 'is_critical' => true],
    ]);

    // Peopleas con diferentes niveles
    $p1 = People::create([
        'organization_id' => $org->id,
        'first_name' => 'Ana',
        'last_name' => 'Alta',
        'email' => 'ana@rankorg.com',
    ]);
    $p1->skills()->attach([
        $skillA->id => ['level' => 3], // ok
        $skillB->id => ['level' => 2], // ok
        $skillC->id => ['level' => 2], // gap 2
    ]);

    $p2 = People::create([
        'organization_id' => $org->id,
        'first_name' => 'Beto',
        'last_name' => 'Bajo',
        'email' => 'beto@rankorg.com',
    ]);
    $p2->skills()->attach([
        $skillA->id => ['level' => 1], // gap 2
        $skillB->id => ['level' => 1], // gap 1
        // C=0 gap 4
    ]);

    // Vacante
    $opening = JobOpening::create([
        'organization_id' => $org->id,
        'title' => 'QA Rank',
        'role_id' => $role->id,
        'status' => 'open',
        'created_by' => $creator->id,
    ]);

    // Act
    $service = new MatchingService();
    $list = $service->rankCandidatesForOpening($opening);

    // Assert: primero Ana (mayor match), luego Beto
    expect($list)->toBeInstanceOf(Illuminate\Support\Collection::class);
    expect($list->count())->toBeGreaterThanOrEqual(2);
    expect($list->first()['name'])->toContain('Ana');
    expect($list->get(1)['name'])->toContain('Beto');
    expect($list->first()['match_percentage'])->toBeGreaterThan($list->get(1)['match_percentage']);
});
