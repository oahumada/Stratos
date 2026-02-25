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

    /**
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function generate(string $prompt, array $options = []): array
    {
        $apiKey = $this->config['api_key'] ?? env('DEEPSEEK_API_KEY');
        $model = $this->config['model'] ?? 'deepseek-chat';
        $endpoint = $this->config['endpoint'] ?? 'https://api.deepseek.com/chat/completions';

        // MODO SIMULACIÓN si no hay API Key en desarrollo
        if (empty($apiKey)) {
            return [
                'response' => [
                    'raw_text' => "DeepSeek Simulation: Analizando con expertise '" . 
                                 implode(', ', $options['expertise'] ?? ['general']) . 
                                 "' para la tarea: " . substr($prompt, 0, 50) . "..."
                ],
                'confidence' => 0.95,
                'model_version' => $model . '-mock',
            ];
        }

        return $this->executeCall($prompt, $options, $apiKey, $model, $endpoint);
    }

    private function executeCall($prompt, $options, $apiKey, $model, $endpoint): array
    {
        $messages = [];
        $systemPrompt = $options['system_prompt'] ?? "Eres un asistente experto.";
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $payload = json_encode([
            'model' => $model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.2,
            'max_tokens' => $options['max_tokens'] ?? 2048,

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

        Log::debug("DeepSeek Raw Response", ['code' => $httpCode, 'body' => $result, 'error' => $error]);

        if ($httpCode >= 400) {
            throw new \RuntimeException('DeepSeek API Error '.$httpCode.': '.$result);
        }

        $data = json_decode($result, true);
        $content = $data['choices'][0]['message']['content'] ?? '';
        $finishReason = $data['choices'][0]['finish_reason'] ?? 'stop';

        // Detectar truncamiento — el JSON estará incompleto
        if ($finishReason === 'length') {
            Log::warning('DeepSeek response truncated (finish_reason=length)', [
                'prompt_tokens'     => $data['usage']['prompt_tokens'] ?? 0,
                'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
            ]);
            throw new \RuntimeException(
                'La respuesta del agente fue truncada por límite de tokens. '
                . 'Reduce la cantidad de roles o simplifica el escenario.'
            );
        }

        // Limpiar bloques de código markdown si existen (ej: ```json ... ```)
        $cleanContent = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($content));

        // Intentar parsear si el agente pidió JSON
        $json = json_decode($cleanContent, true);

        return [
            'response'      => is_array($json) ? $json : ['raw_text' => $content],
            'confidence'    => 0.9,
            'model_version' => $model,
        ];
    }
}
