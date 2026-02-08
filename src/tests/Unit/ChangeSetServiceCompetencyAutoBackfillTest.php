<?php

namespace Tests\Unit;

use App\Models\ChangeSet;
use App\Models\Competency;
use App\Models\CompetencyVersion;
use App\Models\Organizations;
use App\Models\User;
use App\Services\ChangeSetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeSetServiceCompetencyAutoBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_creates_competency_version_when_missing()
    {
        $org = Organizations::create(['name' => 'BF Org C', 'subdomain' => 'bf-c-'.uniqid()]);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $comp = Competency::create(['organization_id' => $org->id, 'name' => 'Test Competency '.uniqid()]);

        $this->assertFalse(CompetencyVersion::where('competency_id', $comp->id)->exists());

        $cs = ChangeSet::create([
            'organization_id' => $org->id,
            'title' => 'Comp Backfill',
            'status' => 'draft',
            'diff' => [
                'ops' => [
                    [
                        'type' => 'create_competency_version',
                        'competency_id' => $comp->id,
                    ],
                ],
            ],
        ]);

        $service = new ChangeSetService;
        $service->apply($cs, $user);

        $this->assertTrue(CompetencyVersion::where('competency_id', $comp->id)->exists());
    }
}
