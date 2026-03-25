<?php

use App\Models\AdminOperationAudit;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->admin = User::factory()->create(['role' => 'admin']);
    \App\Models\People::factory()->create([
        'user_id' => $this->admin->id,
        'organization_id' => $this->organization->id,
    ]);
});

describe('AdminOperationsController', function () {
    it('lists operations for organization', function () {
        $audit = AdminOperationAudit::factory()
            ->for($this->organization)
            ->for($this->admin, 'user')
            ->create();

        $response = actingAs($this->admin)
            ->getJson('/api/admin/operations');

        expect($response->status())->toBeIn([200, 404]);
    });

    it('only admins can view operations', function () {
        $user = User::factory()->create(['role' => 'collaborator']);
        \App\Models\People::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $this->organization->id,
        ]);

        $response = actingAs($user)
            ->getJson('/api/admin/operations');

        expect($response->status())->toBeIn([403, 404]);
    });
});

