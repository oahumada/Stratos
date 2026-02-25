<?php

namespace App\Services;

use App\Models\Agent;
use App\Services\LLMProviders\DeepSeekProvider;
use App\Services\LLMProviders\OpenAIProvider;
use Illuminate\Support\Facades\Log;

class AiOrchestratorService
{
    /**
     * Hace que un agente específico "piense" y responda a una tarea.
     */
    public function agentThink(string $agentName, string $taskPrompt, ?string $systemPromptOverride = null): array
    {
        $agent = Agent::where('name', $agentName)->first();

        if (!$agent) {
            throw new \InvalidArgumentException("Agente '{$agentName}' no encontrado.");
        }

        Log::info("Iniciando razonamiento de Agente", [
            'agent' => $agent->name,
            'task' => substr($taskPrompt, 0, 100) . '...'
        ]);

        $provider = $this->getProvider($agent);
        
        $options = [
            'persona' => $agent->persona,
            'system_prompt' => $systemPromptOverride ?? $this->buildSystemPrompt($agent),
            'temperature' => $agent->capabilities_config['temperature'] ?? 0.6,
        ];

        return $provider->generate($taskPrompt, $options);
    }

    protected function getProvider(Agent $agent)
    {
        // Por ahora, si es deepseek, usamos nuestro nuevo DeepSeekProvider
        if ($agent->provider === 'deepseek') {
            return new DeepSeekProvider([
                'api_key' => env('DEEPSEEK_API_KEY'),
                'model' => $agent->model
            ]);
        }

        // Fallback a OpenAI si es necesario
        return new OpenAIProvider([
            'api_key' => env('LLM_API_KEY'),
            'model' => $agent->model
        ]);
    }

    protected function buildSystemPrompt(Agent $agent): string
    {
        return "Actúa como {$agent->name}, con el rol de {$agent->role_description}. " .
               "Tu personalidad es: {$agent->persona}. " .
               "Tus áreas de expertise son: " . implode(', ', $agent->expertise_areas ?? []) . ". " .
               "Responde SIEMPRE en formato JSON si se solicita una estructura, o como texto claro y profesional si es una consulta directa. " .
               "CONTEXTO DE PLATAFORMA: Estás operando dentro de Stratos, una plataforma de gestión de talento estratégica.";
    }
}
