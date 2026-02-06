<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ChangeSetService;
use App\Models\ChangeSet;
use App\Models\Roles;
use App\Models\RoleVersion;
use App\Models\RoleSunsetMapping;
use App\Models\User;
use App\Models\Organizations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangeSetIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_applying_duplicate_changes_does_not_create_duplicates()
    {
        $org = Organizations::create(['name' => 'ID Org', 'subdomain' => 'id-' . uniqid()]);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $role = Roles::create([
            'organization_id' => $org->id,
            'name' => 'ID Role ' . uniqid(),
            'level' => 'mid'
        ]);

        $op = [
            'type' => 'create_role_sunset_mapping',
            'payload' => [
                'role_id' => $role->id,
                'sunset_reason' => 'redundant',
            ]
        ];

        $cs1 = ChangeSet::create(['organization_id' => $org->id, 'title' => 'CS1', 'status' => 'draft', 'diff' => ['ops' => [$op]]]);
        $cs2 = ChangeSet::create(['organization_id' => $org->id, 'title' => 'CS2', 'status' => 'draft', 'diff' => ['ops' => [$op]]]);

        $s = new ChangeSetService();
        $s->apply($cs1, $user);
        $s->apply($cs2, $user);

        $this->assertEquals(1, RoleSunsetMapping::where('role_id', $role->id)->count());
        $this->assertEquals(1, RoleVersion::where('role_id', $role->id)->count());
    }
}
