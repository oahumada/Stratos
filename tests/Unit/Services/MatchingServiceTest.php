<?php

namespace Tests\Unit\Services;

use App\Models\JobOpening;
use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Skill;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;

    protected \App\Models\User $admin;

    protected MatchingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MatchingService;
        $this->org = Organization::create([
            'name' => 'Matching Org',
            'subdomain' => 'match',
        ]);
        $this->admin = \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin_match@test.com',
            'password' => bcrypt('password'),
            'organization_id' => $this->org->id,
            'role' => 'admin',
        ]);
    }

    public function test_rank_candidates_returns_ordered_list()
    {
        // 1. Crear el Rol y la Vacante
        $role = Roles::create([
            'organization_id' => $this->org->id,
            'name' => 'Backend Wizard',
            'level' => 'mid',
            'status' => 'active',
        ]);

        $opening = JobOpening::create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
            'title' => 'Open Positioning',
            'status' => 'open',
            'created_by' => $this->admin->id,
        ]);

        $skill = Skill::create(['name' => 'Go', 'organization_id' => $this->org->id]);
        $role->skills()->attach($skill->id, ['required_level' => 3]);

        // 2. Crear 2 Personas con diferentes niveles
        // Candidato A: Match 100%
        $personA = People::create(['organization_id' => $this->org->id, 'first_name' => 'Perfect', 'last_name' => 'Match', 'email' => 'perfect@test.com']);
        PeopleRoleSkills::create(['people_id' => $personA->id, 'skill_id' => $skill->id, 'current_level' => 3, 'is_active' => true]);

        // Candidato B: Match 0%
        $personB = People::create(['organization_id' => $this->org->id, 'first_name' => 'No', 'last_name' => 'Clue', 'email' => 'noclue@test.com']);
        PeopleRoleSkills::create(['people_id' => $personB->id, 'skill_id' => $skill->id, 'current_level' => 0, 'is_active' => true]);

        // 3. Ejecutar ranking
        $results = $this->service->rankCandidatesForOpening($opening);

        // 4. Validaciones
        $this->assertCount(2, $results);
        $this->assertEquals($personA->id, $results->first()['people_id']);
        $this->assertEquals(100, $results->first()['match_percentage']);
        $this->assertEquals(0, $results->last()['match_percentage']);
        $this->assertEquals($personB->id, $results->last()['people_id']);
    }

    public function test_calculates_risk_factor_and_time_to_productivity()
    {
        $role = Roles::create([
            'organization_id' => $this->org->id,
            'name' => 'Fullstack',
            'level' => 'mid',
            'status' => 'active',
        ]);
        $skill1 = Skill::create(['name' => 'Vue', 'organization_id' => $this->org->id]);
        $skill2 = Skill::create(['name' => 'Laravel', 'organization_id' => $this->org->id]);

        $role->skills()->attach($skill1->id, ['required_level' => 5]); // Critical gap if level 0
        $role->skills()->attach($skill2->id, ['required_level' => 2]); // Medium gap if level 0

        $opening = JobOpening::create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
            'title' => 'Job',
            'created_by' => $this->admin->id,
        ]);

        People::create(['organization_id' => $this->org->id, 'first_name' => 'Risky', 'last_name' => 'Dev', 'email' => 'risky@test.com']);
        // No skills assigned -> Level 0 for both

        $results = $this->service->rankCandidatesForOpening($opening);
        $match = $results->first();

        // 1 critical gap (level 5-0=5 > 2) -> 25 points
        // 1 medium gap (level 2-0=2 matches 1..2 range) -> 10 points
        // Total risk: 35
        $this->assertEquals(35, $match['risk_factor']);

        // Time to productivity: 1.0 + (2 gaps * 0.5) = 2.0 months
        $this->assertEquals(2.0, $match['time_to_productivity_months']);
        $this->assertContains('Vue', $match['missing_skills']);
        $this->assertContains('Laravel', $match['missing_skills']);
    }
}
