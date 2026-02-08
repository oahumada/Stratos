<?php

namespace Tests\Unit;

use App\Models\ChangeSet;
use App\Models\Organizations;
use App\Models\User;
use Tests\TestCase;

class ChangeSetPolicyTest extends TestCase
{
    public function test_admin_user_can_apply_and_approve()
    {
        $org = Organizations::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id, 'role' => 'admin']);
        $cs = ChangeSet::create([
            'organization_id' => $org->id,
            'scenario_id' => null,
            'title' => 'policy test',
            'description' => 'policy',
            'diff' => ['ops' => []],
            'status' => 'draft',
            'created_by' => null,
        ]);

        $this->assertTrue($user->can('apply', $cs));
        $this->assertTrue($user->can('approve', $cs));
    }

    public function test_creator_can_apply_even_if_not_admin()
    {
        $org = Organizations::factory()->create();
        $creator = User::factory()->create(['organization_id' => $org->id, 'role' => 'user']);
        $cs = ChangeSet::create([
            'organization_id' => $org->id,
            'scenario_id' => null,
            'title' => 'policy test',
            'description' => 'policy',
            'diff' => ['ops' => []],
            'status' => 'draft',
            'created_by' => $creator->id,
        ]);

        $this->assertTrue($creator->can('apply', $cs));
    }

    public function test_other_user_cannot_apply()
    {
        $org = Organizations::factory()->create();
        $creator = User::factory()->create(['organization_id' => $org->id, 'role' => 'user']);
        $other = User::factory()->create(['organization_id' => $org->id, 'role' => 'user']);
        $cs = ChangeSet::create([
            'organization_id' => $org->id,
            'scenario_id' => null,
            'title' => 'policy test',
            'description' => 'policy',
            'diff' => ['ops' => []],
            'status' => 'draft',
            'created_by' => $creator->id,
        ]);

        $this->assertFalse($other->can('apply', $cs));
    }

    public function test_different_organization_cannot_apply()
    {
        $org1 = Organizations::factory()->create();
        $org2 = Organizations::factory()->create();
        $user = User::factory()->create(['organization_id' => $org1->id, 'role' => 'admin']);
        $cs = ChangeSet::create([
            'organization_id' => $org2->id,
            'scenario_id' => null,
            'title' => 'policy test',
            'description' => 'policy',
            'diff' => ['ops' => []],
            'status' => 'draft',
            'created_by' => null,
        ]);

        $this->assertFalse($user->can('apply', $cs));
    }
}
