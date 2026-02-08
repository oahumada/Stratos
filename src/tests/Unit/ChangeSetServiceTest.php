<?php

namespace Tests\Unit;

use App\Models\Organizations;
use App\Models\StrategicPlanningScenarios;
use App\Models\User;
use App\Services\ChangeSetService;
use Tests\TestCase;

class ChangeSetServiceTest extends TestCase
{
    public function test_build_and_apply_changeset(): void
    {
        $org = Organizations::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);
        $scenario = StrategicPlanningScenarios::factory()->create(['organization_id' => $org->id]);

        $service = new ChangeSetService;

        $payload = [
            'organization_id' => $org->id,
            'scenario_id' => $scenario->id,
            'title' => 'Test ChangeSet',
            'description' => 'Unit test payload',
            'diff' => ['ops' => []],
            'created_by' => $user->id,
            'status' => 'draft',
        ];

        $cs = $service->build($payload);

        $this->assertDatabaseHas('change_sets', ['id' => $cs->id, 'title' => 'Test ChangeSet']);

        $applied = $service->apply($cs, $user);

        $this->assertEquals('applied', $applied->status);
        $this->assertNotNull($applied->applied_at);
    }
}
