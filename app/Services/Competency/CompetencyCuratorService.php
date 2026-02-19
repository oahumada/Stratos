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
                throw new \RuntimeException("Formato de respuesta inválido del agente.");
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
}
