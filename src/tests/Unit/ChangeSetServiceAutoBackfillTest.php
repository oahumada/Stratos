<?php

namespace Tests\Unit;

use App\Models\ChangeSet;
use App\Models\Organizations;
use App\Models\Roles;
use App\Models\RoleVersion;
use App\Models\User;
use App\Services\ChangeSetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeSetServiceAutoBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_auto_creates_role_version_when_missing()
    {
        $org = Organizations::create(['name' => 'BF Org', 'subdomain' => 'bf-'.uniqid()]);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $role = Roles::create([
            'organization_id' => $org->id,
            'name' => 'BF Role '.uniqid(),
            'level' => 'mid',
        ]);

        $this->assertFalse(RoleVersion::where('role_id', $role->id)->exists());

        $cs = ChangeSet::create([
            'organization_id' => $org->id,
            'title' => 'Backfill test',
            'status' => 'draft',
            'diff' => [
                'ops' => [
                    [
                        'type' => 'create_role_sunset_mapping',
                        'payload' => [
                            'role_id' => $role->id,
                            'sunset_reason' => 'redundant',
                        ],
                    ],
                ],
            ],
        ]);

        $service = new ChangeSetService;
        $service->apply($cs, $user);

        $this->assertDatabaseHas('role_sunset_mappings', ['role_id' => $role->id]);
        $this->assertTrue(RoleVersion::where('role_id', $role->id)->exists());
    }
}
