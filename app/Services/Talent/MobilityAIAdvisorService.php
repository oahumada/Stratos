<?php

namespace App\Services\Talent;

use App\Models\Departments;
use App\Models\People;
use App\Models\Roles;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class MobilityAIAdvisorService
{
    public function __construct(
        protected AiOrchestratorService $ai,
        protected \App\Services\Talent\Lms\LmsService $lms,
        protected \App\Services\Scenario\DigitalTwinService $digitalTwin
    ) {}

    /**
     * Genera sugerencias estratégicas de movilidad basadas en un objetivo de negocio.
     */
    public function suggestStrategicMovements(string $objective, int $organizationId): array
    {
        // 1. Recopilar Contexto via Digital Twin (Living Graph)
        $organization = \App\Models\Organization::findOrFail($organizationId);
        $digitalTwin = $this->digitalTwin->captureState($organization);

        // 2. Formatear datos para la IA
        $contextData = [
            'organization_metadata' => $digitalTwin['org_metadata'],
            'people' => $digitalTwin['nodes']['people'],
            'roles' => $digitalTwin['nodes']['roles'],
            'hierarchies' => $digitalTwin['edges']['hierarchies'],
            'skill_mesh' => $digitalTwin['edges']['skill_mesh'],
            'learning_catalog' => $this->lms->searchCourses(''), // Obtener catálogo completo para el contexto
        ];

        // 3. Construir el Prompt
        $prompt = $this->buildAdvisorPrompt($objective, $contextData);

        try {
            // Usamos el agente "Simulador Orgánico" definido en los seeders
            $agentResponse = $this->ai->agentThink('Simulador Orgánico', $prompt);
            $proposal = $agentResponse['response'] ?? [];

            // Si el provider no parseó el JSON automáticamente (está envuelto en raw_text), lo hacemos aquí
            if (isset($proposal['raw_text'])) {
                $response = preg_replace('/(^```json\s*)|(```$)/m', '', trim($proposal['raw_text']));
                $proposal = json_decode($response, true);
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException("Error al decodificar JSON de la IA: " . json_last_error_msg());
            }

            return [
                'success' => true,
                'objective' => $objective,
                'proposals' => $proposal['proposals'] ?? [],
                'strategic_rationale' => $proposal['strategic_rationale'] ?? '',
                'global_roi_estimate' => $proposal['global_roi_estimate'] ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('Mobility AI Advisor Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'El Advisor de IA no pudo generar sugerencias: ' . $e->getMessage()
            ];
        }
    }

    protected function buildAdvisorPrompt(string $objective, array $context): string
    {
        return "Actúa como el 'Stratos Strategic Mobility Advisor'. Tu objetivo es diseñar un plan de movimientos internos para cumplir el siguiente objetivo estratégico: '{$objective}'.\n\n" .
               "CONTEXTO DE TALENTO DISPONIBLE Y CATÁLOGO DE APRENDIZAJE:\n" .
               json_encode($context) . "\n\n" .
               "DIRECTRICES:\n" .
               "1. Identifica a las personas cuyo perfil técnico (Skills) mejor resuene con los roles necesarios para el objetivo.\n" .
               "2. Utiliza la jerarquía ('hierarchies') para detectar posibles redundancias o silos que puedan optimizarse.\n" .
               "3. Considera el 'skill_mesh' para entender la fortaleza de las habilidades actuales versus el objetivo.\n" .
               "4. Justifica cada movimiento basándote en la sinergia entre el talento y la meta.\n" .
               "5. Estima un ROI global basado en el ahorro de contratación externa (aprox 20% del salario anual x posición).\n" .
               "6. Para cada persona movida, selecciona cursos específicos del 'learning_catalog' para cerrar sus brechas de habilidades.\n" .
               "   Debes priorizar el contenido 'internal' para procesos críticos y 'linkedin'/'udemy'/'moodle' para habilidades técnicas/blandas.\n\n" .
               "RESPONDE UNICAMENTE EN FORMATO JSON con esta estructura exacta:\n" .
               "{\n" .
               "  \"proposals\": [\n" .
               "    {\n" .
               "      \"person_id\": <number>,\n" .
               "      \"person_name\": \"string\",\n" .
               "      \"target_role_id\": <number>,\n" .
               "      \"target_role_name\": \"string\",\n" .
               "      \"rationale\": \"string (explicación de por qué es el fit perfecto)\",\n" .
               "      \"upskilling_priority\": [\"skill name 1\", \"skill name 2\"],\n" .
               "      \"suggested_courses\": [\n" .
               "        {\"id\": \"id_del_curso\", \"title\": \"titulo_del_curso\", \"provider\": \"internal|linkedin|udemy|moodle|mock\"}\n" .
               "      ]\n" .
               "    }\n" .
               "  ],\n" .
               "  \"strategic_rationale\": \"string (resumen de la estrategia propuesta)\",\n" .
               "  \"global_roi_estimate\": <number>\n" .
               "}";
    }
}
