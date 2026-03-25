<?php

namespace Tests\Feature\Services\Talent;

use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Skill;
use App\Services\Talent\MentorMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorMatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MentorMatchingService $service;

    protected $org;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MentorMatchingService::class);
        $this->org = Organization::factory()->create();
    }

    public function test_find_mentors_returns_qualified_experts()
    {
        // 1. Arrange
        $skill = Skill::factory()->create(['organization_id' => $this->org->id]);
        $role = Roles::factory()->create(['organization_id' => $this->org->id]);

        // Expert 1: Level 5, Verified
        $expert1 = People::factory()->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
            'status' => 'active',
        ]);
        PeopleRoleSkills::create([
            'people_id' => $expert1->id,
            'skill_id' => $skill->id,
            'role_id' => $role->id,
            'current_level' => 5,
            'verified' => true,
            'is_active' => true,
        ]);

        // Expert 2: Level 4, Verified
        $expert2 = People::factory()->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
            'status' => 'active',
        ]);
        PeopleRoleSkills::create([
            'people_id' => $expert2->id,
            'skill_id' => $skill->id,
            'role_id' => $role->id,
            'current_level' => 4,
            'verified' => true,
            'is_active' => true,
        ]);

        // Non-Expert: Level 2
        $lowLevel = People::factory()->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
            'status' => 'active',
        ]);
        PeopleRoleSkills::create([
            'people_id' => $lowLevel->id,
            'skill_id' => $skill->id,
            'role_id' => $role->id,
            'current_level' => 2,
            'verified' => true,
            'is_active' => true,
        ]);

        // Inactive Expert: Level 5, Verified BUT inactive
        $inactive = People::factory()->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
            'status' => 'inactive',
        ]);
        PeopleRoleSkills::create([
            'people_id' => $inactive->id,
            'skill_id' => $skill->id,
            'role_id' => $role->id,
            'current_level' => 5,
            'verified' => true,
            'is_active' => true,
        ]);

        // 2. Act
        $mentors = $this->service->findMentors($skill->id);

        // 3. Assert
        $this->assertCount(2, $mentors);
        $this->assertEquals($expert1->id, $mentors->first()->id);
        $this->assertContains($expert2->id, $mentors->pluck('id'));
        $this->assertNotContains($lowLevel->id, $mentors->pluck('id'));
        $this->assertNotContains($inactive->id, $mentors->pluck('id'));
    }
}
