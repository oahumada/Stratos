<?php

namespace Tests\Unit;

use App\Models\ChangeSet;
use App\Models\Roles;
use App\Models\User;
use App\Services\ChangeSetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeSetServiceRoleSunsetTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_creates_role_sunset_mapping()
    {
        $org = \App\Models\Organizations::create([
            'name' => 'Test Org '.uniqid(),
            'subdomain' => 'test-'.uniqid(),
        ]);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $orgId = $org->id;

        $role = Roles::create([
            'organization_id' => $orgId,
            'name' => 'Test Role '.uniqid(),
            'level' => 'mid',
            'description' => 'Created by test',
        ]);

        $cs = ChangeSet::create([
            'organization_id' => $orgId,
            'scenario_id' => null,
            'title' => 'Test ChangeSet',
            'status' => 'draft',
            'diff' => [
                'ops' => [
                    [
                        'type' => 'create_role_sunset_mapping',
                        'payload' => [
                            'scenario_id' => null,
                            'role_id' => $role->id,
                            'mapped_role_id' => null,
                            'sunset_reason' => 'redundant',
                            'metadata' => ['via' => 'test'],
                        ],
                    ],
                ],
            ],
        ]);

        $service = new ChangeSetService;
        $service->apply($cs, $user);

        $this->assertDatabaseHas('role_sunset_mappings', [
            'role_id' => $role->id,
            'sunset_reason' => 'redundant',
        ]);

        $cs->refresh();
        $this->assertEquals('applied', $cs->status);
        $this->assertNotNull($cs->applied_at);
    }
}
