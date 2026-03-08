<?php

namespace Tests\Feature\Services;

use App\Models\Agent;
use App\Models\BarsLevel;
use App\Models\Competency;
use App\Models\Organization;
use App\Models\Skill;
use App\Services\AiOrchestratorService;
use App\Services\Competency\CompetencyCuratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CompetencyCuratorServiceTest extends TestCase
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
        $this->service = new CompetencyCuratorService($this->orchestratorMock);
    }

    public function test_curate_skill_generates_and_persists_bars_levels()
    {
        // 1. Arrange
        $skill = Skill::factory()->create([
            'organization_id' => $this->org->id,
            'name' => 'Python Programming',
            'description' => 'Ability to write clean Python code',
        ]);

        $mockResponse = [
            'levels' => [
                [
                    'level' => 1,
                    'level_name' => 'Ayuda',
                    'behavioral_description' => 'Realiza tareas básicas bajo supervisión.',
                    'learning_content' => 'Sintaxis básica, tipos de datos.',
                    'performance_indicator' => 'Completa scripts de < 50 líneas.',
                    'agentic_rationale' => 'Nivel inicial de entrada.'
                ],
                [
                    'level' => 2,
                    'level_name' => 'Aplica',
                    'behavioral_description' => 'Desarrolla módulos funcionales con autonomía.',
                    'learning_content' => 'POE, Decoradores, Manejo de errores.',
                    'performance_indicator' => 'Implementa APIs REST básicas.',
                    'agentic_rationale' => 'Demuestra autonomía técnica.'
                ]
            ]
        ];

        $this->orchestratorMock->shouldReceive('agentThink')
            ->once()
            ->with('Ingeniero de Talento', Mockery::on(fn($prompt) => str_contains($prompt, 'Python Programming')))
            ->andReturn(['response' => $mockResponse]);

        // 2. Act
        $result = $this->service->curateSkill($skill->id);

        // 3. Assert
        $this->assertEquals('success', $result['status']);
        $this->assertEquals(2, $result['levels_count']);
        
        $this->assertDatabaseHas('bars_levels', [
            'skill_id' => $skill->id,
            'level' => 1,
            'level_name' => 'Ayuda'
        ]);
        
        $this->assertDatabaseHas('bars_levels', [
            'skill_id' => $skill->id,
            'level' => 2,
            'level_name' => 'Aplica'
        ]);
    }

    public function test_curate_competency_creates_skills_and_curates_them()
    {
        // 1. Arrange
        $competency = Competency::create([
            'organization_id' => $this->org->id,
            'name' => 'Fullstack Development',
            'description' => 'Desarrollo web completo.',
            'status' => 'active'
        ]);

        $mockCompetencyResponse = [
            'description' => 'Dominio de tecnologías frontend y backend.',
            'skills' => [
                ['name' => 'React', 'category' => 'Frontend', 'description' => 'Building UIs with React'],
                ['name' => 'Node.js', 'category' => 'Backend', 'description' => 'Server-side JS']
            ]
        ];

        $this->orchestratorMock->shouldReceive('agentThink')
            ->once()
            ->with('Ingeniero de Talento', Mockery::on(fn($prompt) => str_contains($prompt, 'Fullstack Development')))
            ->andReturn(['response' => $mockCompetencyResponse]);

        // Mock for curateSkill calls (2 times)
        $this->orchestratorMock->shouldReceive('agentThink')
            ->twice()
            ->with('Ingeniero de Talento', Mockery::on(fn($prompt) => str_contains($prompt, 'levels')))
            ->andReturn(['response' => ['levels' => []]]); // Empty levels for simplicity in this integration-like unit test

        // 2. Act
        $result = $this->service->curateCompetency($competency->id);

        // 3. Assert
        $this->assertEquals('success', $result['status']);
        $this->assertEquals(2, $result['skills_analyzed']);
        $this->assertDatabaseHas('skills', ['name' => 'React']);
        $this->assertDatabaseHas('skills', ['name' => 'Node.js']);
        $this->assertCount(2, $competency->fresh()->skills);
    }
}
