<?php

namespace App\Services\Competency;

use App\Models\Skill;
use App\Models\Competency;
use App\Models\BarsLevel;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class CompetencyCuratorService
{
    const DEFAULT_AGENT = 'Ingeniero de Talento';

    protected AiOrchestratorService $orchestrator;

    public function __construct(AiOrchestratorService $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * Curar una Competencia completa: define descripción si falta, define skills, los asocia y los cura.
     */
    public function curateCompetency(int $competencyId): array
    {
        $competency = Competency::with(['agent', 'skills'])->findOrFail($competencyId);
        $agentName = $competency->agent ? $competency->agent->name : self::DEFAULT_AGENT;

        $prompt = "Actúa como {$agentName} de Stratos. Necesitamos diseñar la competencia clave de talento: '{$competency->name}'.
        
        Dado el título de la competencia y cualquier contexto previo ('{$competency->description}'), realiza lo siguiente:
        1. Mejora la descripción de la competencia.
        2. Identifica y proporciona entre 2 y 4 habilidades subyacentes (skills) críticas para esta competencia.
        
        Responde estrictamente en formato JSON con esta estructura:
        {
          \"description\": \"Mejor descripción detallada\",
          \"skills\": [
            { \"name\": \"Nombre de Habilidad 1\", \"category\": \"Técnica/Blanda\", \"description\": \"...\" },
            { \"name\": \"...\", \"category\": \"...\", \"description\": \"...\" }
          ]
        }";

        try {
            // 1. Obtener la arquitectura de la competencia desde IA
            $result = $this->orchestrator->agentThink($agentName, $prompt);
            $data = $result['response'];

            if (!isset($data['skills'])) {
                throw new \InvalidArgumentException("Formato de respuesta inválido de {$agentName}.");
            }

            // 2. Almacenar la descripción mejorada
            $competency->description = $data['description'] ?? $competency->description;
            $competency->save();

            $skillIdsCreatedOrFound = [];

            // 3. Crear las habilidades (Skills) si no existen y asociarlas
            foreach ($data['skills'] as $sData) {
                // Buscamos si ya existe por nombre, de lo contrario la creamos
                $skill = Skill::firstOrCreate(
                    ['name' => $sData['name']],
                    [
                        'organization_id' => $competency->organization_id,
                        'description' => $sData['description'],
                        'category' => $sData['category']
                    ]
                );
                
                $skillIdsCreatedOrFound[] = $skill->id;

                // Relacionamos la Skill a la Competencia
                if (!$competency->skills()->where('skill_id', $skill->id)->exists()) {
                    $competency->skills()->attach($skill->id, [
                        'weight' => 100,
                        'priority' => 'high',
                        'required_level' => 3,
                        'is_required' => true,
                    ]);
                }
            }

            // 4. Iniciar la curaduría de cada Skill (generación de BARS, unidades, etc) interactuando con el propio orquestador
            $barsCuratedCount = 0;
            foreach ($skillIdsCreatedOrFound as $sId) {
                // Here we call the existing curateSkill logic
                $curateResult = $this->curateSkill($sId);
                if ($curateResult['status'] === 'success') {
                    $barsCuratedCount++;
                }
            }

            return [
                'status' => 'success',
                'competency' => $competency->name,
                'skills_analyzed' => count($skillIdsCreatedOrFound),
                'skills_curated' => $barsCuratedCount
            ];

        } catch (\Exception $e) {
            Log::error("Error curando competencia #{$competencyId}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
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
            $result = $this->orchestrator->agentThink(self::DEFAULT_AGENT, $prompt);
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
            $result = $this->orchestrator->agentThink(self::DEFAULT_AGENT, $prompt);
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
