<?php

namespace Tests\Feature\Integrations;

use App\Models\Organizations;
use App\Models\User;
use App\Models\ScenarioGeneration;
use App\Jobs\GenerateScenarioFromLLMJob;
use App\Services\Intelligence\StratosIntelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScenarioGenerationIntelTest extends TestCase
{
    use RefreshDatabase;

    protected $org;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->org = Organizations::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    }

    public function test_it_dispatches_intel_job_for_scenario_generation()
    {
        $this->actingAs($this->user);

        Queue::fake();

        $response = $this->postJson('/api/strategic-planning/scenarios/generate/intel', [
            'company_name' => 'Test Corp',
            'instruction' => 'Create a digital transformation scenario',
            'instruction_language' => 'es'
        ]);

        $response->assertStatus(202);
        
        $generationId = $response->json('data.id');
        $generation = ScenarioGeneration::find($generationId);
        
        expect($generation->metadata['provider'])->toBe('intel');
        
        Queue::assertPushed(GenerateScenarioFromLLMJob::class, function ($job) use ($generationId) {
            return $job->generationId === $generationId;
        });
    }

    public function test_it_processes_intel_scenario_generation_successfully()
    {
        $generation = ScenarioGeneration::create([
            'organization_id' => $this->org->id,
            'created_by' => $this->user->id,
            'prompt' => 'Digital transformation',
            'status' => 'queued',
            'metadata' => [
                'provider' => 'intel',
                'company_name' => $this->org->name,
                'language' => 'es'
            ]
        ]);

        $mockResponse = [
            'scenario_metadata' => ['name' => 'Digital Transformation'],
            'capabilities' => [
                [
                    'name' => 'Data Analytics',
                    'competencies' => [
                        ['name' => 'Machine Learning', 'skills' => ['Python', 'SciKit-Learn']]
                    ]
                ]
            ],
            'suggested_roles' => [
                [
                    'name' => 'Data Scientist',
                    'talent_composition' => ['human_percentage' => 40, 'synthetic_percentage' => 60]
                ]
            ]
        ];

        Http::fake([
            'http://localhost:8000/generate-scenario' => Http::response($mockResponse, 200),
        ]);

        $job = new GenerateScenarioFromLLMJob($generation->id);
        $job->handle(app(\App\Services\LLMClient::class), null, new StratosIntelService());

        $generation->refresh();

        expect($generation->status)->toBe('complete');
        expect($generation->llm_response['scenario_metadata']['name'])->toBe('Digital Transformation');
        expect($generation->llm_response['suggested_roles'][0]['talent_composition']['synthetic_percentage'])->toBe(60);
    }
}
