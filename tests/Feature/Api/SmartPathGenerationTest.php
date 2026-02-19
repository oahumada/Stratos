<?php

namespace Tests\Feature\Api;

use App\Models\DevelopmentPath;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Skill;
use App\Services\Talent\MentorMatchingService;
use App\Services\Talent\SmartPathGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartPathGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected $person;
    protected $skill;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup basic data
        $org = \App\Models\Organization::factory()->create();
        $role = \App\Models\Roles::factory()->create();
        $this->person = People::factory()->create([
            'organization_id' => $org->id,
            'role_id' => $role->id
        ]);
        $this->skill = Skill::factory()->create(['name' => 'Python Programming']);
    }

    public function test_can_generate_a_smart_path_for_a_skill_gap()
    {
        $service = app(SmartPathGeneratorService::class);

        $path = $service->generatePath(
            $this->person->id,
            $this->skill->id,
            1, // Current Level
            4  // Target Level
        );

        // Assert Path
        $this->assertDatabaseHas('development_paths', [
            'id' => $path->id,
            'people_id' => $this->person->id,
            'action_title' => 'Plan de Cierre de Brechas: Python Programming'
        ]);

        // Assert Actions (70-20-10)
        $this->assertCount(3, $path->actions);
        
        $this->assertDatabaseHas('development_actions', [
            'development_path_id' => $path->id,
            'type' => 'training',
            'strategy' => 'build'
        ]);

        $this->assertDatabaseHas('development_actions', [
            'development_path_id' => $path->id,
            'type' => 'mentoring',
            'strategy' => 'borrow'
        ]);

        $this->assertDatabaseHas('development_actions', [
            'development_path_id' => $path->id,
            'type' => 'project',
            'strategy' => 'build'
        ]);
    }

    public function test_matches_a_mentor_if_available()
    {
        // Create an expert
        $expert = People::factory()->create([
            'first_name' => 'Guido', 
            'last_name' => 'Van Rossum', 
            'status' => 'active',
            'organization_id' => $this->person->organization_id
        ]);
        $role = \App\Models\Roles::factory()->create();
        
        // Assign expert skill
        PeopleRoleSkills::create([
            'people_id' => $expert->id,
            'role_id' => $role->id,
            'skill_id' => $this->skill->id,
            'current_level' => 5,
            'verified' => true
        ]);

        $service = app(SmartPathGeneratorService::class);
        $path = $service->generatePath($this->person->id, $this->skill->id, 1, 3);
        
        $actions = $path->actions;
        $mentorAction = $actions->where('type', 'mentoring')->first();

        // Should mention the expert
        $this->assertStringContainsString('Guido Van Rossum', $mentorAction->title);
    }
}
