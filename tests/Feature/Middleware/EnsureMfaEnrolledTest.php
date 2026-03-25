<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();

    // Crear una ruta de prueba con el middleware (soporta GET y POST)
    \Illuminate\Support\Facades\Route::match(['get', 'post'], '/test-mfa-required', function () {
        return response()->json(['message' => 'success']);
    })->middleware('mfa.required');
});

// ──────── MFA not required for low-privilege roles ─────────

it('allows collaborator without MFA to pass', function () {
    $user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'collaborator',
        'two_factor_confirmed_at' => null,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/test-mfa-required')
        ->assertSuccessful()
        ->assertJsonPath('message', 'success');
});

it('allows manager without MFA to pass', function () {
    $user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'manager',
        'two_factor_confirmed_at' => null,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/test-mfa-required')
        ->assertSuccessful();
});

// ──────── MFA required for high-privilege roles ──────────

it('blocks admin without MFA from passing', function () {
    $user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
        'two_factor_confirmed_at' => null,
    ]);

    // API request: should return JSON 403
    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/test-mfa-required');

    $response->assertForbidden();
    $response->assertJsonPath('action', 'enable_mfa');
});

it('blocks hr_leader without MFA from passing', function () {
    $user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'hr_leader',
        'two_factor_confirmed_at' => null,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/test-mfa-required');

    $response->assertForbidden();
    $response->assertJsonPath('action', 'enable_mfa');
});

// ──────── Admin/HR Leader WITH MFA ──────────────────────

it('allows admin with MFA to pass', function () {
    $user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
        'two_factor_confirmed_at' => now(),
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/test-mfa-required')
        ->assertSuccessful()
        ->assertJsonPath('message', 'success');
});

it('allows hr_leader with MFA to pass', function () {
    $user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'hr_leader',
        'two_factor_confirmed_at' => now(),
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/test-mfa-required')
        ->assertSuccessful();
});

// ──────── Unauthenticated access ────────────────────────

it('allows unauthenticated requests to pass (no user to check)', function () {
    $this->postJson('/test-mfa-required')
        ->assertSuccessful();
});
