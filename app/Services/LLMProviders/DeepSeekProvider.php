<?php

namespace App\Services\LLMProviders;

use Illuminate\Support\Facades\Log;

class DeepSeekProvider implements LLMProviderInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function generate(string $prompt, array $options = []): array
    {
        $apiKey = $this->config['api_key'] ?? config('stratos.llm.deepseek_api_key');
        $model = $this->config['model'] ?? 'deepseek-chat';
        $endpoint = $this->config['endpoint'] ?? 'https://api.deepseek.com/chat/completions';

        // MODO SIMULACIÓN si no hay API Key en desarrollo
        if (empty($apiKey)) {
            return [
                'response' => [
                    'raw_text' => "DeepSeek Simulation: Analizando con expertise '".
                                 implode(', ', $options['expertise'] ?? ['general']).
                                 "' para la tarea: ".substr($prompt, 0, 50).'...',
                ],
                'confidence' => 0.95,
                'model_version' => $model.'-mock',
            ];
        }

        return $this->executeCall($prompt, $options, $apiKey, $model, $endpoint);
    }

    private function executeCall($prompt, $options, $apiKey, $model, $endpoint): array
    {
        $messages = [];
        $systemPrompt = $options['system_prompt'] ?? 'Eres un asistente experto.';
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $payload = json_encode([
            'model' => $model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.2,
            'max_tokens' => $options['max_tokens'] ?? 4096,

        ]);

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '.$apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        Log::debug('DeepSeek Raw Response', ['code' => $httpCode, 'body' => $result, 'error' => $error]);

        if ($httpCode === 402) {
            Log::warning('DeepSeek API Insufficient Balance. Falling back to simulation mode.');
            return [
                'response' => array_merge($this->getMockResponseForPrompt($prompt), [
                    'simulation_metadata' => [
                        'is_simulated' => true,
                        'reason' => 'insufficient_balance',
                        'notice' => 'DeepSeek API: Saldo insuficiente. Generación simulada para fines de prueba estructural.'
                    ]
                ]),
                'confidence' => 0.5,
                'model_version' => $model.'-simulated-balance-empty',
            ];
        }

        if ($httpCode >= 400) {
            throw new \RuntimeException('DeepSeek API Error '.$httpCode.': '.$result);
        }

        $data = json_decode($result, true);
        $content = $data['choices'][0]['message']['content'] ?? '';
        $finishReason = $data['choices'][0]['finish_reason'] ?? 'stop';

        // Detectar truncamiento — el JSON estará incompleto
        if ($finishReason === 'length') {
            Log::warning('DeepSeek response truncated (finish_reason=length)', [
                'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
            ]);
            throw new \RuntimeException(
                'La respuesta del agente fue truncada por límite de tokens. '
                .'Reduce la cantidad de roles o simplifica el escenario.'
            );
        }

        // Limpiar bloques de código markdown si existen (ej: ```json ... ```)
        $cleanContent = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($content));

        // Intentar parsear si el agente pidió JSON
        $json = json_decode($cleanContent, true);

        return [
            'response' => is_array($json) ? $json : ['raw_text' => $content],
            'confidence' => 0.9,
            'model_version' => $model,
        ];
    }

    protected function getMockResponseForPrompt(string $prompt): array
    {
        // Si el prompt pide JSON, devolvemos algo estructurado mínimamente para no romper el front
        if (str_contains(strtolower($prompt), 'json')) {
            return [
                'competency_blueprint' => [
                    [
                        'competency_name' => 'Competencia Simulada (API Down)',
                        'levels' => [
                            ['level' => 1, 'level_name' => 'Básico', 'description' => 'Descripción simulada'],
                            ['level' => 2, 'level_name' => 'Inicial', 'description' => 'Descripción simulada'],
                            ['level' => 3, 'level_name' => 'Intermedio', 'description' => 'Descripción simulada'],
                            ['level' => 4, 'level_name' => 'Avanzado', 'description' => 'Descripción simulada'],
                            ['level' => 5, 'level_name' => 'Experto', 'description' => 'Descripción simulada'],
                        ],
                        'skills' => [
                            [
                                'name' => 'Habilidad Técnica IA',
                                'description' => 'Capacidad de orquestación de modelos cognitivos.',
                                'levels' => [
                                    ['level' => 1, 'learning_unit' => 'Fundamentos', 'performance_criterion' => 'Identifica conceptos básicos'],
                                    ['level' => 2, 'learning_unit' => 'Aplicación', 'performance_criterion' => 'Aplica técnicas estándar'],
                                    ['level' => 3, 'learning_unit' => 'Análisis', 'performance_criterion' => 'Analiza resultados'],
                                    ['level' => 4, 'learning_unit' => 'Evaluación', 'performance_criterion' => 'Evalúa modelos complejos'],
                                    ['level' => 5, 'learning_unit' => 'Creación', 'performance_criterion' => 'Diseña nuevas arquitecturas'],
                                ]
                            ]
                        ]
                    ]
                ],
                'bars' => [
                   'behavior' => 'Simulando comportamiento funcional por falta de saldo en API IA.',
                   'attitude' => 'Simulando actitud proactiva del sistema.',
                   'responsibility' => 'Gestión de riesgos tecnológicos.',
                   'skill' => 'Arquitectura de agentes autónomos.'
                ]
            ];
        }

        return ['raw_text' => 'Simulación de respuesta por falta de saldo en API.'];
    }
}
