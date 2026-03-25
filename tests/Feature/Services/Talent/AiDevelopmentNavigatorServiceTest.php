<?php

namespace Tests\Feature\Services\Talent;

use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\Skill;
use App\Services\AiOrchestratorService;
use App\Services\Talent\AiDevelopmentNavigatorService;
use App\Services\Talent\MentorMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AiDevelopmentNavigatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $org;

    protected $user;

    protected $person;

    protected function setUp(): void
    {
        parent::setUp();
        $this->org = Organization::factory()->create();

        $role = Roles::factory()->create(['organization_id' => $this->org->id]);
        $this->person = People::factory()->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
            'status' => 'active',
        ]);
    }

    public function test_generates_ai_development_path_and_actions()
    {
        // 1. Arrange
        $skill = Skill::factory()->create(['organization_id' => $this->org->id]);

        // Mock MentorMatchingService to return empty for now
        $mentorService = Mockery::mock(MentorMatchingService::class);
        $mentorService->shouldReceive('findMentors')->andReturn(new \Illuminate\Database\Eloquent\Collection);

        // Mock AiOrchestratorService
        $orchestrator = Mockery::mock(AiOrchestratorService::class);
        $orchestrator->shouldReceive('agentThink')->andReturn([
            'response' => json_encode([
                'path_title' => 'Especialista en IA Estratégica',
                'estimated_duration_months' => 4,
                'actions' => [
                    [
                        'title' => 'Curso de Prompt Engineering',
                        'description' => 'Aprender técnicas avanzadas de prompting.',
                        'type' => 'training',
                        'strategy' => 'buy',
                        'estimated_hours' => 15,
                        'impact_weight' => 0.20,
                        'mentor_id' => null,
                    ],
                    [
                        'title' => 'Proyecto: Refactor de Reportes',
                        'description' => 'Implementar IA en el módulo de reportes.',
                        'type' => 'project',
                        'strategy' => 'build',
                        'estimated_hours' => 40,
                        'impact_weight' => 0.80,
                        'mentor_id' => null,
                    ],
                ],
            ]),
        ]);

        $service = new AiDevelopmentNavigatorService($orchestrator, $mentorService, app(\App\Services\GapAnalysisService::class));

        // 2. Act
        $path = $service->generateAiPath($this->person->id, $skill->id, 4);

        // 3. Assert
        $this->assertDatabaseHas('development_paths', [
            'id' => $path->id,
            'people_id' => $this->person->id,
            'action_title' => 'Especialista en IA Estratégica',
            'status' => 'active',
        ]);

        $this->assertCount(2, $path->actions);
        $this->assertDatabaseHas('development_actions', [
            'development_path_id' => $path->id,
            'title' => 'Curso de Prompt Engineering',
            'type' => 'training',
        ]);

        $this->assertDatabaseHas('development_actions', [
            'development_path_id' => $path->id,
            'title' => 'Proyecto: Refactor de Reportes',
            'type' => 'project',
        ]);
    }
}
