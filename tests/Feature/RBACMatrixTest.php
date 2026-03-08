<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
    $this->org = Organization::factory()->create();
});

test('role hierarchy and permissions', function ($role, $permission, $shouldHave) {
    $user = User::factory()->create([
        'organization_id' => $this->org->id,
        'role' => $role
    ]);

    $has = $user->hasPermission($permission);

    expect($has)->toBe($shouldHave, "Role '{$role}' should " . ($shouldHave ? '' : 'NOT ') . "have '{$permission}'");
})->with([
    'Admin scenarios' => ['admin', 'scenarios.create', true],
    'Admin settings' => ['admin', 'settings.manage', true],
    'HR scenarios' => ['hr_leader', 'scenarios.create', true],
    'HR settings' => ['hr_leader', 'settings.manage', false],
    'Manager see scenarios' => ['manager', 'scenarios.view', true],
    'Manager create scenarios' => ['manager', 'scenarios.create', false],
    'Collaborator respond' => ['collaborator', 'assessments.respond', true],
    'Collaborator view scenarios' => ['collaborator', 'scenarios.view', false],
    'Observer view' => ['observer', 'scenarios.view', true],
    'Observer manage' => ['observer', 'people.manage', false],
]);

test('admin can access rbac endpoints', function () {
    $admin = User::factory()->create(['role' => 'admin', 'organization_id' => $this->org->id]);
    $this->actingAs($admin, 'sanctum')
        ->getJson('/api/rbac')
        ->assertStatus(200);
});

test('hr leader cannot access rbac endpoints', function () {
    $hr = User::factory()->create(['role' => 'hr_leader', 'organization_id' => $this->org->id]);
    $this->actingAs($hr, 'sanctum')
        ->getJson('/api/rbac')
        ->assertStatus(403);
});
