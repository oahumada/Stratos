<?php

namespace Tests\Feature\Api;

use App\Models\ChangeSet;
use App\Models\Organizations;
use App\Models\User;
use Tests\TestCase;

class ChangeSetApprovalApiTest extends TestCase
{
    public function test_can_apply_and_approve_endpoints_for_admin_and_creator()
    {
        $org = Organizations::factory()->create();
        $admin = User::factory()->create(['organization_id' => $org->id, 'role' => 'admin']);
        $creator = User::factory()->create(['organization_id' => $org->id, 'role' => 'user']);

        $cs = ChangeSet::create([
            'organization_id' => $org->id,
            'scenario_id' => null,
            'title' => 'approval test',
            'description' => 'approval flows',
            'diff' => ['ops' => []],
            'status' => 'draft',
            'created_by' => $creator->id,
        ]);

        // Admin can apply
        $this->actingAs($admin, 'sanctum')
            ->getJson("/api/strategic-planning/change-sets/{$cs->id}/can-apply")
            ->assertOk()
            ->assertJson(['can_apply' => true]);

        // Creator can also apply (creator fallback)
        $this->actingAs($creator, 'sanctum')
            ->getJson("/api/strategic-planning/change-sets/{$cs->id}/can-apply")
            ->assertOk()
            ->assertJson(['can_apply' => true]);

        // Admin approves
        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/strategic-planning/change-sets/{$cs->id}/approve")
            ->assertOk()
            ->assertJson(['success' => true]);

        $cs->refresh();
        $this->assertEquals('approved', $cs->status);
        $this->assertEquals($admin->id, $cs->approved_by);
    }

    public function test_reject_and_forbidden_cases()
    {
        $org1 = Organizations::factory()->create();
        $org2 = Organizations::factory()->create();
        $creator = User::factory()->create(['organization_id' => $org1->id, 'role' => 'user']);
        $other = User::factory()->create(['organization_id' => $org1->id, 'role' => 'user']);
        $adminOtherOrg = User::factory()->create(['organization_id' => $org2->id, 'role' => 'admin']);

        $cs = ChangeSet::create([
            'organization_id' => $org1->id,
            'scenario_id' => null,
            'title' => 'reject test',
            'description' => 'reject flows',
            'diff' => ['ops' => []],
            'status' => 'draft',
            'created_by' => $creator->id,
        ]);

        // Non-approver cannot approve
        $this->actingAs($other, 'sanctum')
            ->postJson("/api/strategic-planning/change-sets/{$cs->id}/approve")
            ->assertStatus(403);

        // Admin from different org cannot can-apply or approve
        $this->actingAs($adminOtherOrg, 'sanctum')
            ->getJson("/api/strategic-planning/change-sets/{$cs->id}/can-apply")
            ->assertStatus(403);

        $this->actingAs($adminOtherOrg, 'sanctum')
            ->postJson("/api/strategic-planning/change-sets/{$cs->id}/reject")
            ->assertStatus(403);

        // Now have a valid approver reject
        $approver = User::factory()->create(['organization_id' => $org1->id, 'role' => 'admin']);
        $this->actingAs($approver, 'sanctum')
            ->postJson("/api/strategic-planning/change-sets/{$cs->id}/reject")
            ->assertOk()
            ->assertJson(['success' => true]);

        $cs->refresh();
        $this->assertEquals('rejected', $cs->status);
        $this->assertEquals($approver->id, $cs->approved_by);
    }
}
