<?php

namespace App\Services;

use App\Models\Agent;
use App\Services\LLMProviders\DeepSeekProvider;
use App\Services\LLMProviders\OpenAIProvider;
use App\Traits\LogsPrompts;
use Illuminate\Support\Facades\Log;

class AiOrchestratorService
{
    use LogsPrompts;

    /**
     * Hace que un agente específico "piense" y responda a una tarea.
     * Logs are PII-safe using LogsPrompts trait.
     */
    public function agentThink(string $agentName, string $taskPrompt, ?string $systemPromptOverride = null): array
    {
        $agent = Agent::where('name', $agentName)->first();

        if (! $agent) {
            throw new \InvalidArgumentException("Agente '{$agentName}' no encontrado.");
        }

        Log::info('Iniciando razonamiento de Agente', [
            'agent' => $agent->name,
            'task' => substr($taskPrompt, 0, 100).'...',
        ]);

        $provider = $this->getProvider($agent);

        $options = [
            'persona' => $agent->persona,
            'system_prompt' => $systemPromptOverride ?? $this->buildSystemPrompt($agent),
            'temperature' => $agent->capabilities_config['temperature'] ?? 0.6,
        ];

        try {
            $output = $provider->generate($taskPrompt, $options);

            // Log the prompt and output with PII protection
            $this->logPrompt($taskPrompt, $output, [
                'agent' => $agent->name,
                'model' => $agent->model,
                'provider' => $agent->provider,
                'organization_id' => $agent->organization_id ?? null,
            ]);

            return $output;
        } catch (\Throwable $e) {
            // Log error maintaining PII safety
            $this->logPromptError($taskPrompt, $e, [
                'agent' => $agent->name,
                'model' => $agent->model,
                'provider' => $agent->provider,
                'organization_id' => $agent->organization_id ?? null,
            ]);

            throw $e;
        }
    }

    protected function getProvider(Agent $agent)
    {
        // Por ahora, si es deepseek, usamos nuestro nuevo DeepSeekProvider
        if ($agent->provider === 'deepseek') {
            return new DeepSeekProvider([
                'api_key' => config('stratos.llm.deepseek_api_key'),
                'model' => $agent->model,
            ]);
        }

        // Fallback a OpenAI si es necesario
        return new OpenAIProvider([
            'api_key' => config('stratos.llm.api_key'),
            'model' => $agent->model,
        ]);
    }

    protected function buildSystemPrompt(Agent $agent): string
    {
        return "Actúa como {$agent->name}, con el rol de {$agent->role_description}. ".
               "Tu personalidad es: {$agent->persona}. ".
               'Tus áreas de expertise son: '.implode(', ', $agent->expertise_areas ?? []).'. '.
               'Responde SIEMPRE en formato JSON si se solicita una estructura, o como texto claro y profesional si es una consulta directa. '.
               'CONTEXTO DE PLATAFORMA: Estás operando dentro de Stratos, una plataforma de gestión de talento estratégica.';
    }
}
