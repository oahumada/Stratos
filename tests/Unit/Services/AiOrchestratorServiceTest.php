<?php

namespace Tests\Unit\Services;

use App\Models\Agent;
use App\Services\AiOrchestratorService;
use App\Services\LLMProviders\DeepSeekProvider;
use App\Services\LLMProviders\OpenAIProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class AiOrchestratorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_think_throws_exception_if_agent_not_found()
    {
        $service = new AiOrchestratorService();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Agente 'Inexistente' no encontrado.");

        $service->agentThink('Inexistente', 'tarea');
    }

    public function test_agent_think_uses_correct_provider_and_returns_response()
    {
        // 1. Crear el agente en la DB
        $agent = Agent::create([
            'name' => 'TestAgent',
            'role_description' => 'Test Role',
            'persona' => 'Test Persona',
            'provider' => 'openai',
            'model' => 'gpt-4o',
            'expertise_areas' => ['testing'],
            'capabilities_config' => ['temperature' => 0.7]
        ]);

        // 2. Mockear el proveedor de OpenAI
        $mockProvider = Mockery::mock(OpenAIProvider::class);
        $mockProvider->shouldReceive('generate')
            ->once()
            ->with('Haz algo', Mockery::on(function ($options) {
                return $options['temperature'] === 0.7 && str_contains($options['system_prompt'], 'Test Persona');
            }))
            ->andReturn(['response' => 'OK']);

        // 3. Inyectar el mock en el servicio (usando un mock parcial)
        $service = Mockery::mock(AiOrchestratorService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('getProvider')
            ->with(Mockery::on(fn($a) => $a->id === $agent->id))
            ->andReturn($mockProvider);

        // 4. Ejecutar
        $result = $service->agentThink('TestAgent', 'Haz algo');

        $this->assertEquals(['response' => 'OK'], $result);
    }
}
