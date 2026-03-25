<?php

use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('lists conversations for authenticated user', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $people = People::factory()->for($org)->for($user, 'user')->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/messaging/conversations');

    $response->assertSuccessful();
    expect($response->json())->toHaveKey('data');
});

it('prevents unauthenticated access', function () {
    $response = $this->getJson('/api/messaging/conversations');

    $response->assertUnauthorized();
});
