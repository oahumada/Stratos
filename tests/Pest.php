<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHP unit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature');
uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Unit');
uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Browser');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "matchers" that you can use to assert different
| things. Of course, you may extend this set of "matchers" with your own custom functions.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can register helper functions.
|
*/

function actingAs($user, $driver = null)
{
    return test()->actingAs($user, $driver);
}

function createUserForOrganizationWithRole(
    \App\Models\Organization $organization,
    string $role,
): \App\Models\User {
    return \App\Models\User::factory()->create([
        'organization_id' => $organization->id,
        'current_organization_id' => $organization->id,
        'role' => $role,
        'email' => sprintf('test+%s@example.test', \Illuminate\Support\Str::uuid()),
    ]);
}

function grantPermissionToRole(
    string $role,
    string $permissionName,
    string $module = 'system',
    string $action = 'view',
    ?string $description = null,
): void {
    $permission = \App\Models\Permission::query()->firstOrCreate(
        ['name' => $permissionName],
        [
            'module' => $module,
            'action' => $action,
            'description' => $description,
        ],
    );

    \Illuminate\Support\Facades\DB::table('role_permissions')->updateOrInsert(
        [
            'role' => $role,
            'permission_id' => $permission->id,
        ],
        [
            'created_at' => now(),
            'updated_at' => now(),
        ],
    );
}
