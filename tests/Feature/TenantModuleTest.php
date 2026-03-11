<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->orgWithModule = Organization::factory()->create([
        'name' => 'Org With Module',
        'active_modules' => ['strategic_simulation']
    ]);
    
    $this->orgWithoutModule = Organization::factory()->create([
        'name' => 'Org Without Module',
        'active_modules' => ['core']
    ]);

    $this->userWith = User::factory()->create(['organization_id' => $this->orgWithModule->id]);
    $this->userWithout = User::factory()->create(['organization_id' => $this->orgWithoutModule->id]);

    // Define a test route using the middleware
    Route::middleware(['web', 'auth', 'module:strategic_simulation'])->get('/_test/module', function () {
        return response()->json(['success' => true]);
    });
});

it('allows access when organization has the required module', function () {
    $response = $this->actingAs($this->userWith)
        ->getJson('/_test/module');

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

it('denies access when organization does not have the required module', function () {
    $response = $this->actingAs($this->userWithout)
        ->getJson('/_test/module');

    $response->assertStatus(403);
    $response->assertJsonPath('message', 'Módulo inactivo. Contacta a soporte para habilitar esta característica.');
});

it('always allows access to core module', function () {
    Route::middleware(['web', 'auth', 'module:core'])->get('/_test/core', function () {
        return response()->json(['success' => true]);
    });

    $response = $this->actingAs($this->userWithout)
        ->getJson('/_test/core');

    $response->assertStatus(200);
});
