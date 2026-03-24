<?php

namespace App\Services;

use App\DTOs\VerificationAction;
use App\Exceptions\VerificationFailedException;
use App\Models\Agent;
use App\Models\AgentInteraction;
use App\Services\LLMProviders\DeepSeekProvider;
use App\Services\LLMProviders\OpenAIProvider;
use App\Traits\LogsPrompts;
use Illuminate\Support\Facades\Log;

class AiOrchestratorService
{
    use LogsPrompts;

    public function __construct(
        protected ?VerificationIntegrationService $verificationIntegration = null,
    ) {
        $this->verificationIntegration ??= app(VerificationIntegrationService::class);
    }

    /**
     * Hace que un agente específico "piense" y responda a una tarea.
     * Logs are PII-safe using LogsPrompts trait.
     * Auto-tracks interaction metrics.
     * NEW: Verifies output against business rules validators.
     */
    public function agentThink(string $agentName, string $taskPrompt, ?string $systemPromptOverride = null): array
    {
        $agent = Agent::where('name', $agentName)->first();

        if (! $agent) {
            throw new \InvalidArgumentException("Agente '{$agentName}' no encontrado.");
        }

        $taskPreview = substr($taskPrompt, 0, 100).'...';
        $taskPreview = RedactionService::redactText($taskPreview);

        Log::info('Iniciando razonamiento de Agente', [
            'agent' => $agent->name,
            'task' => $taskPreview,
        ]);

        $provider = $this->getProvider($agent);

        $options = [
            'persona' => $agent->persona,
            'system_prompt' => $systemPromptOverride ?? $this->buildSystemPrompt($agent),
            'temperature' => $agent->capabilities_config['temperature'] ?? 0.6,
        ];

        $startTime = now();
        $startMicrotime = microtime(true);

        try {
            $output = $provider->generate($taskPrompt, $options);
            $latency = intval((microtime(true) - $startMicrotime) * 1000); // milliseconds

            // NEW: Verify agent output against business rules
            if (config('verification.enabled', true)) {
                $verificationIntegration = $this->verificationIntegration ?? app(VerificationIntegrationService::class);
                
                $verificationResult = $verificationIntegration->verifyAgentOutput(
                    agentName: $agentName,
                    output: $output,
                    context: [
                        'organization_id' => $agent->organization_id,
                        'task_prompt' => $taskPrompt,
                        'provider' => $agent->provider,
                        'agent_id' => $agent->id,
                    ]
                );

                // NEW: Decide action based on verification result
                $action = $verificationIntegration->decideAction($verificationResult);

                // NEW: Handle rejection (Phase 3 & 4)
                if ($action->shouldReject()) {
                    throw new VerificationFailedException(
                        violations: $verificationResult->violations,
                        agentName: $agentName,
                        message: $action->errorMessage ?? $verificationResult->getHumanReadableErrors()
                    );
                }

                // NEW: Attach verification metadata to output
                $output['_verification'] = [
                    'valid' => $verificationResult->valid,
                    'recommendation' => $verificationResult->recommendation,
                    'violations_count' => count($verificationResult->violations),
                    'confidence_score' => $verificationResult->confidenceScore,
                    'phase' => $verificationResult->phase,
                    'flagged_for_review' => $action->shouldFlag(),
                ];
            }

            // Log the prompt and output with PII protection
            $promptHash = $this->logPrompt($taskPrompt, $output, [
                'agent' => $agent->name,
                'model' => $agent->model,
                'provider' => $agent->provider,
                'organization_id' => $agent->organization_id ?? null,
            ]);

            // Record interaction metrics
            $this->recordInteraction(
                agentName: $agent->name,
                promptHash: $promptHash,
                latencyMs: $latency,
                tokenCount: $this->estimateTokenCount($taskPrompt, $output),
                status: 'success',
                inputLength: strlen($taskPrompt),
                outputLength: strlen($output['response'] ?? ''),
                provider: $agent->provider,
                model: $agent->model,
                organizationId: $agent->organization_id,
            );

            return $output;
        } catch (VerificationFailedException $e) {
            $latency = intval((microtime(true) - $startMicrotime) * 1000);

            // Log verification failure
            $promptHash = $this->logPromptError($taskPrompt, $e, [
                'agent' => $agent->name,
                'model' => $agent->model,
                'provider' => $agent->provider,
                'organization_id' => $agent->organization_id ?? null,
                'failure_type' => 'verification',
            ]);

            // Record failed interaction (verification failure)
            $this->recordInteraction(
                agentName: $agent->name,
                promptHash: $promptHash,
                latencyMs: $latency,
                tokenCount: 0,
                status: 'verification_failed',
                errorMessage: $e->getMessage(),
                inputLength: strlen($taskPrompt),
                outputLength: 0,
                provider: $agent->provider,
                model: $agent->model,
                organizationId: $agent->organization_id,
            );

            throw $e;
        } catch (\Throwable $e) {
            $latency = intval((microtime(true) - $startMicrotime) * 1000);

            // Log error maintaining PII safety
            $promptHash = $this->logPromptError($taskPrompt, $e, [
                'agent' => $agent->name,
                'model' => $agent->model,
                'provider' => $agent->provider,
                'organization_id' => $agent->organization_id ?? null,
            ]);

            // Record failed interaction
            $this->recordInteraction(
                agentName: $agent->name,
                promptHash: $promptHash,
                latencyMs: $latency,
                tokenCount: 0,
                status: 'error',
                errorMessage: $e->getMessage(),
                inputLength: strlen($taskPrompt),
                outputLength: 0,
                provider: $agent->provider,
                model: $agent->model,
                organizationId: $agent->organization_id,
            );

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

    /**
     * Record an agent interaction for metrics tracking.
     */
    private function recordInteraction(
        string $agentName,
        string $promptHash,
        int $latencyMs,
        int $tokenCount,
        string $status,
        ?string $errorMessage = null,
        int $inputLength = 0,
        int $outputLength = 0,
        ?string $provider = null,
        ?string $model = null,
        ?int $organizationId = null,
    ): void {
        try {
            AgentInteraction::create([
                'agent_name' => $agentName,
                'user_id' => auth()->check() ? auth()->id() : null,
                'organization_id' => $organizationId,
                'prompt_hash' => $promptHash,
                'latency_ms' => $latencyMs,
                'token_count' => $tokenCount,
                'status' => $status,
                'error_message' => $errorMessage,
                'input_length' => $inputLength,
                'output_length' => $outputLength,
                'provider' => $provider,
                'model' => $model,
                'context' => request()->path() ?? 'unknown',
            ]);

            // Invalidate metrics cache for this organization
            if ($organizationId) {
                (new AgentInteractionMetricsService)->invalidateMetricsCache($organizationId);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to record agent interaction', [
                'agent' => $agentName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Estimate token count (simple heuristic: ~4 chars per token).
     *
     * @param  mixed  $output
     */
    private function estimateTokenCount(string $input, $output): int
    {
        $outputStr = is_array($output) ? json_encode($output) : (string) $output;
        $totalChars = strlen($input) + strlen($outputStr);

        return intval($totalChars / 4);
    }
}
