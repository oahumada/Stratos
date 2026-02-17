<?php

namespace Database\Seeders;

use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleSkill;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Phase3TestDataSeeder extends Seeder
{
    public const ROLE_AI_LEAD = 'AI Lead';
    public const ROLE_CLOUD_DEV = 'Cloud Dev';

    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,
            UserSeeder::class,
        ]);

        $orgId = \App\Models\Organizations::first()->id;
        $adminUser = User::where('organization_id', $orgId)->first() ?? User::first();

        // 1. Create a Phase 3 specific Scenario
        $scenario = Scenario::updateOrCreate(
            ['organization_id' => $orgId, 'name' => 'Phase 3: Testing Scenario'],
            [
                'description' => 'A complex scenario to test Internal Supply Analysis, Matching and Succession.',
                'status' => 'draft',
                'owner_user_id' => $adminUser->id ?? 1,
                'horizon_months' => 12,
                'fiscal_year' => 2026,
                'created_by' => $adminUser->id ?? 1,
                'updated_by' => $adminUser->id ?? 1,
            ]
        );

        // 2. Clear previous scenario data if any
        ScenarioRole::where('scenario_id', $scenario->id)->delete();
        ScenarioRoleSkill::where('scenario_id', $scenario->id)->delete();

        // 3. Create/Get Skills
        $skills = [
            'GenAI implementation' => Skill::firstOrCreate(['name' => 'GenAI implementation', 'organization_id' => $orgId])->id,
            'Advanced Python' => Skill::firstOrCreate(['name' => 'Advanced Python', 'organization_id' => $orgId])->id,
            'Cloud Architecture' => Skill::firstOrCreate(['name' => 'Cloud Architecture', 'organization_id' => $orgId])->id,
            'Strategic Vision' => Skill::firstOrCreate(['name' => 'Strategic Vision', 'organization_id' => $orgId])->id,
        ];

        // 4. Create Roles
        $roleAI = Roles::firstOrCreate(['name' => self::ROLE_AI_LEAD, 'organization_id' => $orgId]);
        $roleDev = Roles::firstOrCreate(['name' => self::ROLE_CLOUD_DEV, 'organization_id' => $orgId]);

        // 5. Add Roles to Scenario with impact levels
        $sRoleAI = ScenarioRole::create([
            'scenario_id' => $scenario->id,
            'role_id' => $roleAI->id,
            'impact_level' => 'critical',
            'fte' => 2,
            'evolution_type' => 'transformation',
        ]);

        $sRoleDev = ScenarioRole::create([
            'scenario_id' => $scenario->id,
            'role_id' => $roleDev->id,
            'impact_level' => 'medium',
            'fte' => 5,
            'evolution_type' => 'upgrade_skills',
        ]);

        // 6. Assign Required Skills (To-Be)
        // AI Lead needs GenAI (Lvl 5) and Strategic Vision (Lvl 4)
        ScenarioRoleSkill::create([
            'scenario_id' => $scenario->id,
            'role_id' => $sRoleAI->id,
            'skill_id' => $skills['GenAI implementation'],
            'required_level' => 5,
            'current_level' => 2,
            'change_type' => 'new',
            'is_critical' => true,
        ]);
        ScenarioRoleSkill::create([
            'scenario_id' => $scenario->id,
            'role_id' => $sRoleAI->id,
            'skill_id' => $skills['Strategic Vision'],
            'required_level' => 4,
            'current_level' => 3,
            'change_type' => 'evolution',
        ]);

        // Cloud Dev needs Cloud Architecture (Lvl 4) and Python (Lvl 4)
        ScenarioRoleSkill::create([
            'scenario_id' => $scenario->id,
            'role_id' => $sRoleDev->id,
            'skill_id' => $skills['Cloud Architecture'],
            'required_level' => 4,
            'current_level' => 3,
            'change_type' => 'evolution',
        ]);

        // 7. Create/Update People (As-Is supply)
        $peopleData = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Ready',
                'email' => 'alice@ready.com',
                'role_id' => $roleDev->id, // Currently a Dev
                'skills' => [
                    ['id' => $skills['GenAI implementation'], 'level' => 5],
                    ['id' => $skills['Strategic Vision'], 'level' => 4],
                ]
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Developer',
                'email' => 'bob@dev.com',
                'role_id' => $roleDev->id,
                'skills' => [
                    ['id' => $skills['Cloud Architecture'], 'level' => 4],
                    ['id' => $skills['Advanced Python'], 'level' => 3],
                ]
            ],
            [
                'first_name' => 'Charlie',
                'last_name' => 'Holder',
                'email' => 'charlie@holder.com',
                'role_id' => $roleAI->id, // Current holder of AI Lead
                'skills' => [
                    ['id' => $skills['Strategic Vision'], 'level' => 3],
                ]
            ]
        ];

        foreach ($peopleData as $p) {
            $person = People::updateOrCreate(
                ['email' => $p['email'], 'organization_id' => $orgId],
                ['first_name' => $p['first_name'], 'last_name' => $p['last_name'], 'role_id' => $p['role_id']]
            );

            // Clean old skills for consistency in testing
            PeopleRoleSkills::where('people_id', $person->id)->delete();

            foreach ($p['skills'] as $s) {
                PeopleRoleSkills::create([
                    'people_id' => $person->id,
                    'skill_id' => $s['id'],
                    'current_level' => $s['level'],
                    'level' => $s['level'],
                    'is_active' => true,
                    'role_id' => $p['role_id'],
                    'evaluated_by' => $adminUser->id ?? 1
                ]);
            }
        }
    }
}
