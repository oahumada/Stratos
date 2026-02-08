<?php

namespace Tests\Feature\Api;

use App\Models\ChangeSet;
use App\Models\Organizations;
use App\Models\Roles;
use App\Models\User;
use Tests\TestCase;

class ChangeSetIgnoredIndexesTest extends TestCase
{
    public function test_apply_respects_ignored_indexes()
    {
        $org = Organizations::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        // create two roles
        $roleA = Roles::create(['organization_id' => $org->id, 'name' => 'Role A']);
        $roleB = Roles::create(['organization_id' => $org->id, 'name' => 'Role B']);

        // build a changeset with two create_role_sunset_mapping ops
        $diff = [
            'ops' => [
                ['type' => 'create_role_sunset_mapping', 'payload' => ['role_id' => $roleA->id, 'sunset_reason' => 'r1', 'mapped_role_id' => null]],
                ['type' => 'create_role_sunset_mapping', 'payload' => ['role_id' => $roleB->id, 'sunset_reason' => 'r2', 'mapped_role_id' => null]],
            ],
        ];

        $cs = ChangeSet::create([
            'organization_id' => $org->id,
            'scenario_id' => null,
            'created_by' => $user->id,
            'title' => 'Test ignore',
            'diff' => $diff,
            'status' => 'approved',
        ]);

        // apply while ignoring the second op (index 1)
        $this->actingAs($user, 'sanctum');
        $res = $this->postJson("/api/strategic-planning/change-sets/{$cs->id}/apply", ['ignored_indexes' => [1]]);
        $res->assertStatus(200);

        // roleA mapping should exist, roleB mapping should not
        $this->assertDatabaseHas('role_sunset_mappings', ['role_id' => $roleA->id, 'sunset_reason' => 'r1']);
        $this->assertDatabaseMissing('role_sunset_mappings', ['role_id' => $roleB->id, 'sunset_reason' => 'r2']);
    }
}
