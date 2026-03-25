<?php

namespace App\Services\Talent;

use App\Models\BarsLevel;
use App\Models\Competency;
use App\Models\Skill;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompetencyMaterializerService
{
    protected AiOrchestratorService $orchestrator;

    public function __construct(AiOrchestratorService $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * Genera un blueprint detallado de Skills para una Competencia.
     */
    public function generateBlueprint(Competency $competency): array
    {
        $prompt = "Eres un Arquitecto de Talento experto en la metodología SFIA, BARS y en la Gestión de la Fuerza de Trabajo Híbrida (Humano + IA).
        Tu tarea es desglosar la competencia estratégica: \"{$competency->name}\" en habilidades (skills) operativas.

        Descripción de la competencia: \"{$competency->description}\"

        CRITERIOS ADICIONALES DE REVOLUCIÓN TECNOLÓGICA:
        Debes evaluar cada habilidad bajo el modelo de 'Cubo de Skill' (Talento Sintético e Híbrido):

        1. MODO DE TALENTO (talent_mode):
           - 'human_centric': Requiere juicio humano, empatía o presencia física.
           - 'hybrid_augmented': El humano usa la IA como copiloto (Ej: n8n, GPT).
           - 'synthetic_autonomous': La tarea puede ser realizada 100% por un agente digital/bot.

        2. MADUREZ 4D (AI_Fluency):
           - 'delegation_potential': Puntuación (1-5) sobre qué tanto se puede delegar a una máquina.
           - 'description_clarity': Puntuación (1-5) sobre qué tan fácil es describir la tarea en un prompt.
           - 'discernment_criticality': Puntuación (1-5) sobre qué tan crítico es el juicio humano para validar el resultado.
        
        Para esta competencia, identifica 3 habilidades clave.
        Para CADA habilidad, define 5 niveles de maestría (del 1 al 5) y su configuración de Cubo.
        
        Responde estrictamente en formato JSON:
        {
          \"competency_id\": {$competency->id},
          \"skills\": [
            {
              \"name\": \"...\",
              \"description\": \"...\",
              \"talent_mode\": \"human_centric|hybrid_augmented|synthetic_autonomous\",
              \"ai_fluency\": {
                 \"delegation\": 5,
                 \"description\": 4,
                 \"discernment\": 2,
                 \"diligence\": 4
              },
              \"levels\": [
                {
                  \"level\": 1,
                  \"level_name\": \"...\",
                  \"behavioral_description\": \"...\",
                  \"learning_content\": \"...\",
                  \"performance_indicator\": \"...\"
                },
                ... (niveles 1 al 5)
              ]
            },
            ...
          ]
        }";

        try {
            $result = $this->orchestrator->agentThink('Arquitecto de Talento Híbrido', $prompt);

            return $result['response'];
        } catch (\Exception $e) {
            Log::error('Blueprint Generation Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Materializa (persiste) el blueprint en la base de datos.
     */
    public function materialize(Competency $competency, array $blueprintData): array
    {
        DB::beginTransaction();
        try {
            foreach ($blueprintData['skills'] as $skillData) {
                // 1. Crear o actualizar la Skill
                $skill = Skill::updateOrCreate(
                    [
                        'organization_id' => $competency->organization_id,
                        'name' => $skillData['name'],
                    ],
                    [
                        'description' => $skillData['description'],
                        'status' => 'active',
                        'category' => 'technical',
                        'cube_dimensions' => [
                            'talent_mode' => $skillData['talent_mode'] ?? 'human_centric',
                            'ai_fluency' => $skillData['ai_fluency'] ?? null,
                            'hybrid_dna' => true,
                        ],
                    ]
                );

                // 2. Vincular a la Competencia
                $competency->skills()->syncWithoutDetaching([$skill->id => ['weight' => 10]]);

                // 3. Crear Niveles BARS/SFIA
                foreach ($skillData['levels'] as $lvl) {
                    BarsLevel::updateOrCreate(
                        [
                            'skill_id' => $skill->id,
                            'level' => $lvl['level'],
                        ],
                        [
                            'level_name' => $lvl['level_name'] ?? "Nivel {$lvl['level']}",
                            'behavioral_description' => $lvl['behavioral_description'],
                            'learning_content' => $lvl['learning_content'] ?? null,
                            'performance_indicator' => $lvl['performance_indicator'] ?? null,
                        ]
                    );
                }
            }

            // 4. Sello Digital y Activación
            $competency->status = 'active';
            $competency->seal(); // Aplica firma criptográfica del sistema
            $competency->save();

            DB::commit();

            return ['status' => 'success', 'message' => 'Competencia materializada correctamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Materialization Error: '.$e->getMessage());

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
