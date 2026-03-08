<?php

namespace Tests\Feature\Services;

use App\Models\JobOpening;
use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Skill;
use App\Services\MatchingService;
;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MatchingService $service;
    protected $org;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MatchingService::class);
        $this->org = Organization::factory()->create();
    }

    public function test_rank_candidates_for_opening()
    {
        // 1. Arrange: Create a Role with 2 required skills
        $role = Roles::factory()->create(['organization_id' => $this->org->id]);
        $skill1 = Skill::factory()->create(['organization_id' => $this->org->id]);
        $skill2 = Skill::factory()->create(['organization_id' => $this->org->id]);

        $role->skills()->attach($skill1->id, ['required_level' => 4, 'is_critical' => true]);
        $role->skills()->attach($skill2->id, ['required_level' => 3, 'is_critical' => false]);

        $user = \App\Models\User::factory()->create(['organization_id' => $this->org->id]);

        $opening = JobOpening::create([
            'organization_id' => $this->org->id,
            'title' => 'Senior Developer',
            'role_id' => $role->id,
            'status' => 'open',
            'created_by' => $user->id
        ]);

        // Candidate A: Perfect Match
        $candidateA = People::factory()->create(['organization_id' => $this->org->id, 'role_id' => $role->id]);
        PeopleRoleSkills::create(['people_id' => $candidateA->id, 'skill_id' => $skill1->id, 'role_id' => $role->id, 'current_level' => 4, 'is_active' => true]);
        PeopleRoleSkills::create(['people_id' => $candidateA->id, 'skill_id' => $skill2->id, 'role_id' => $role->id, 'current_level' => 3, 'is_active' => true]);

        // Candidate B: Partial Match (Minor Gaps)
        $candidateB = People::factory()->create(['organization_id' => $this->org->id, 'role_id' => $role->id]);
        PeopleRoleSkills::create(['people_id' => $candidateB->id, 'skill_id' => $skill1->id, 'role_id' => $role->id, 'current_level' => 3, 'is_active' => true]); // Gap 1
        PeopleRoleSkills::create(['people_id' => $candidateB->id, 'skill_id' => $skill2->id, 'role_id' => $role->id, 'current_level' => 3, 'is_active' => true]);

        // Candidate C: Poor Match (Critical Gap)
        $candidateC = People::factory()->create(['organization_id' => $this->org->id, 'role_id' => $role->id]);
        PeopleRoleSkills::create(['people_id' => $candidateC->id, 'skill_id' => $skill1->id, 'role_id' => $role->id, 'current_level' => 1, 'is_active' => true]); // Large Gap 3 (Critical)
        PeopleRoleSkills::create(['people_id' => $candidateC->id, 'skill_id' => $skill2->id, 'role_id' => $role->id, 'current_level' => 1, 'is_active' => true]); // Gap 2

        // 2. Act
        $results = $this->service->rankCandidatesForOpening($opening);

        // 3. Assert
        $this->assertCount(3, $results);
        
        // Ranking order
        $this->assertEquals($candidateA->id, $results[0]['people_id']);
        $this->assertEquals($candidateB->id, $results[1]['people_id']);
        $this->assertEquals($candidateC->id, $results[2]['people_id']);

        // Correctness of match percentage
        $this->assertEquals(100, $results[0]['match_percentage']);
        $this->assertLessThan(100, $results[1]['match_percentage']);
        $this->assertLessThan($results[1]['match_percentage'], $results[2]['match_percentage']);

        // Check Risk Factor
        $this->assertEquals(0, $results[0]['risk_factor']);
        $this->assertGreaterThan(0, $results[1]['risk_factor']);
        $this->assertGreaterThan($results[1]['risk_factor'], $results[2]['risk_factor']);
    }
}
