<?php

namespace Tests\Feature\Services;

use App\Models\AssessmentFeedback;
use App\Models\AssessmentRequest;
use App\Models\Competency;
use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Skill;
use App\Services\AiOrchestratorService;
use App\Services\Assessment\Stratos360TriangulationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class Stratos360TriangulationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $orchestratorMock;
    protected $org;

    protected function setUp(): void
    {
        parent::setUp();
        $this->org = Organization::factory()->create();
        $this->orchestratorMock = Mockery::mock(AiOrchestratorService::class);
        $this->service = new Stratos360TriangulationService($this->orchestratorMock);
    }

    public function test_triangulate_gathers_data_and_persists_calibrated_results()
    {
        // 1. Arrange
        $subject = People::factory()->create(['organization_id' => $this->org->id]);
        $skill = Skill::factory()->create(['organization_id' => $this->org->id, 'name' => 'Leadership']);
        
        $competency = Competency::create([
            'organization_id' => $this->org->id,
            'name' => 'Management',
            'status' => 'active'
        ]);
        
        // Link skill to competency
        DB::table('competency_skills')->insert([
            'competency_id' => $competency->id,
            'skill_id' => $skill->id,
            'weight' => 100,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create 2 completed requests with feedback
        $manager = People::factory()->create(['organization_id' => $this->org->id]);
        $peer = People::factory()->create(['organization_id' => $this->org->id]);

        $req1 = AssessmentRequest::create(['organization_id' => $this->org->id, 'subject_id' => $subject->id, 'evaluator_id' => $manager->id, 'relationship' => 'manager', 'status' => 'completed']);
        $req1->feedback()->create(['skill_id' => $skill->id, 'score' => 2, 'answer' => 'Very strict', 'question' => 'How is their leadership?']);

        $req2 = AssessmentRequest::create(['organization_id' => $this->org->id, 'subject_id' => $subject->id, 'evaluator_id' => $peer->id, 'relationship' => 'peer', 'status' => 'completed']);
        $req2->feedback()->create(['skill_id' => $skill->id, 'score' => 5, 'answer' => 'Excellent peer', 'question' => 'How is their leadership?']);

        // Mock AI response
        $mockAiResponse = [
            'overall_bias_detected' => 'Detected severity from manager.',
            'triangulated_competencies' => [
                [
                    'competency_id' => $competency->id,
                    'competency_name' => 'Management',
                    'competency_score' => 4.0,
                    'skills' => [
                        [
                            'skill_id' => $skill->id,
                            'raw_score' => 3.5,
                            'stratos_score' => 4,
                            'bias_flag' => 'manager_severity',
                            'ai_justification' => 'Adjusted based on peer evidence.'
                        ]
                    ]
                ]
            ]
        ];

        $this->orchestratorMock->shouldReceive('agentThink')
            ->once()
            ->with('Orquestador 360', Mockery::on(fn($prompt) => str_contains($prompt, 'Leadership')))
            ->andReturn(['response' => json_encode($mockAiResponse)]);

        // 2. Act
        $result = $this->service->triangulate($subject->id);

        // 3. Assert
        $this->assertEquals('success', $result['status']);
        $this->assertDatabaseHas('people_role_skills', [
            'people_id' => $subject->id,
            'skill_id' => $skill->id,
            'current_level' => 4,
            'evidence_source' => 'Stratos360_IA_Triangulated'
        ]);
        
        $this->assertStringContainsString('Adjusted based on peer evidence', PeopleRoleSkills::where('people_id', $subject->id)->first()->notes);
    }
}
