<?php

namespace App\Services\Competency;

use App\Models\Skill;
use App\Models\BarsLevel;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class CompetencyCuratorService
{
    protected AiOrchestratorService $orchestrator;

    public function __construct(AiOrchestratorService $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * Genera y guarda las definiciones BARS, contenidos de aprendizaje e indicadores para una skill.
     */
    public function curateSkill(int $skillId): array
    {
        $skill = Skill::findOrFail($skillId);

        $prompt = "Actúa como Curador de Competencias de Stratos. Necesito definir los 5 niveles de dominio (estilo BARS) para la habilidad: '{$skill->name}'.

        Contexto de la habilidad: {$skill->description}
        Categoría: {$skill->category}

        Para cada uno de los 5 niveles (1: Novato, 2: Principiante, 3: Competente, 4: Avanzado, 5: Experto), proporciona:
        1. level_name: El nombre del nivel.
        2. behavioral_description: Descripción detallada del comportamiento observable.
        3. learning_content: Contenido de aprendizaje clave para alcanzar o dominar este nivel.
        4. performance_indicator: Un indicador clave de desempeño (KPI) objetivo para validar este nivel.

        Responde estrictamente en formato JSON con la siguiente estructura:
        {
          \"levels\": [
            {
              \"level\": 1,
              \"level_name\": \"...\",
              \"behavioral_description\": \"...\",
              \"learning_content\": \"...\",
              \"performance_indicator\": \"...\"
            },
            ...
          ]
        }";

        try {
            $result = $this->orchestrator->agentThink('Curador de Competencias', $prompt);
            $data = $result['response'];

            if (!isset($data['levels']) || !is_array($data['levels'])) {
                // Fallback si no devolvió el formato esperado pero el texto raw contiene algo útil
                throw new \InvalidArgumentException("Formato de respuesta inválido del agente.");
            }

            $curatedLevels = [];
            foreach ($data['levels'] as $levelData) {
                $level = BarsLevel::updateOrCreate(
                    ['skill_id' => $skill->id, 'level' => $levelData['level']],
                    [
                        'level_name' => $levelData['level_name'],
                        'behavioral_description' => $levelData['behavioral_description'],
                        'learning_content' => $levelData['learning_content'],
                        'performance_indicator' => $levelData['performance_indicator'],
                    ]
                );
                $curatedLevels[] = $level;
            }

            return [
                'status' => 'success',
                'skill' => $skill->name,
                'levels_count' => count($curatedLevels)
            ];

        } catch (\Exception $e) {
            Log::error("Error curando skill #{$skillId}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Genera preguntas de entrevista (técnicas/situacionales) basadas en el Indicador de Desempeño de cada nivel.
     */
    public function generateQuestions(int $skillId): array
    {
        $skill = Skill::with('barsLevels')->findOrFail($skillId);

        if ($skill->barsLevels->isEmpty()) {
            return ['status' => 'error', 'message' => 'No existen niveles BARS definidos para esta skill. Ejecute curateSkill primero.'];
        }

        $prompt = "Actúa como Curador de Competencias y Experto en Entrevistas. Necesito generar preguntas de validación para la habilidad: '{$skill->name}'.

        Para cada uno de los 5 niveles, basándote estrictamente en su 'Performance Indicator', genera 1 pregunta clave para validar si el candidato realmente cumple con ese nivel.
        La pregunta debe ser conductual o situacional (ej: 'Cuéntame de una vez que...').

        Definiciones por nivel:
        ";

        foreach ($skill->barsLevels as $level) {
            $prompt .= "- Nivel {$level->level} ({$level->level_name}): KPI = {$level->performance_indicator}\n";
        }

        $prompt .= "\nResponde estrictamente en formato JSON:
        {
          \"questions\": [
            { \"level\": 1, \"question\": \"...\", \"type\": \"situational\" },
            ...
          ]
        }";

        try {
            $result = $this->orchestrator->agentThink('Curador de Competencias', $prompt);
            $data = $result['response'];

            $questionsCreated = [];

            foreach ($data['questions'] ?? [] as $q) {
                \App\Models\SkillQuestionBank::updateOrCreate(
                    [
                        'skill_id' => $skill->id,
                        'level' => $q['level'],
                        'question' => $q['question'] // Evitar duplicados exactos
                    ],
                    [
                        'question_type' => $q['type'] ?? 'situational',
                        'is_global' => true,
                        'archetype' => 'general'
                    ]
                );
                $questionsCreated[] = $q;
            }

            return [
                'status' => 'success',
                'skill' => $skill->name,
                'questions_generated' => count($questionsCreated)
            ];

        } catch (\Exception $e) {
            Log::error("Error generando preguntas para skill #{$skillId}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
