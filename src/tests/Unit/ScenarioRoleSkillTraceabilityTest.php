<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Organizations;
use App\Models\User;
use App\Models\ScenarioRoleSkill;
use App\Models\CompetencyVersion;

class ScenarioRoleSkillTraceabilityTest extends TestCase
{
    public function test_saves_competency_version_and_metadata()
    {
        $org = Organizations::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        // Create a minimal competency and version
        $competency = \App\Models\Competency::create(['organization_id' => $org->id, 'name' => 'Tst Competency']);
        $cv = \App\Models\CompetencyVersion::create([
            'organization_id' => $org->id,
            'competency_id' => $competency->id,
            'version_group_id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => $competency->name,
            'evolution_state' => 'new_embryo',
            'metadata' => ['source' => 'test'],
        ]);

        $scenario = \App\Models\Scenario::create([
            'organization_id' => $org->id,
            'name' => 'Tst Scn',
            'code' => 'TST-SCN-1',
            'description' => 'Test scenario',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(6)->toDateString(),
            'horizon_months' => 6,
            'fiscal_year' => now()->year,
            'scope_type' => 'organization_wide',
            'owner_user_id' => $user->id,
            'created_by' => $user->id,
        ]);
        $role = \App\Models\Roles::create(['organization_id' => $org->id, 'name' => 'Tst Role']);
        $skill = \App\Models\Skill::create(['organization_id' => $org->id, 'name' => 'Tst Skill']);

        $srs = ScenarioRoleSkill::create([
            'scenario_id' => $scenario->id,
            'role_id' => $role->id,
            'skill_id' => $skill->id,
            'required_level' => 3,
            'change_type' => 'maintenance',
            'is_critical' => true,
            'source' => 'manual',
            'competency_id' => $cv->competency_id ?? null,
            'competency_version_id' => $cv->id,
            'metadata' => ['reason' => 'imported'],
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('scenario_role_skills', [
            'id' => $srs->id,
            'competency_version_id' => $cv->id,
        ]);

        $row = ScenarioRoleSkill::find($srs->id);
        $this->assertEquals(['reason' => 'imported'], $row->metadata);
        $this->assertEquals($user->id, $row->created_by);
    }
}
