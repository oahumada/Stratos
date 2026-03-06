<?php

namespace App\Services\Assessment;

use App\Models\AssessmentFeedback;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Stratos360TriangulationService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator
    ) {}

    /**
     * Triangular y calibrar las evaluaciones cruzadas de un talento usando el Orquestador AI.
     */
    public function triangulate(int $subjectId, ?int $cycleId = null): array
    {
        $person = People::findOrFail($subjectId);
        
        // 1. Recopilar feedbacks completados (con comentario o score).
        $query = AssessmentFeedback::join('assessment_requests', 'assessment_feedback.assessment_request_id', '=', 'assessment_requests.id')
            ->join('skills', 'assessment_feedback.skill_id', '=', 'skills.id')
            ->leftJoin('competency_skills', 'skills.id', '=', 'competency_skills.skill_id')
            ->leftJoin('competencies', 'competency_skills.competency_id', '=', 'competencies.id')
            ->where('assessment_requests.subject_id', $subjectId)
            ->where('assessment_requests.status', 'completed')
            ->whereNotNull('assessment_feedback.skill_id')
            ->select(
                'competencies.id as competency_id',
                'competencies.name as competency_name',
                'skills.id as skill_id',
                'skills.name as skill_name',
                'assessment_requests.relationship',
                'assessment_feedback.score',
                'assessment_feedback.answer as qualitative_feedback'
            );
            
        if ($cycleId) {
            $query->where('assessment_requests.assessment_cycle_id', $cycleId);
        }

        $feedbacks = $query->get();

        if ($feedbacks->isEmpty()) {
            return [
                'status' => 'error',
                'message' => 'No hay feedbacks cruzados suficientes para triangular.'
            ];
        }

        // 2. Agrupar por Competencia -> Skill
        $groupedByCompetency = $feedbacks->groupBy(function($item) {
            return $item->competency_id ?? 'ungrouped';
        });

        $contextPayload = [];
        foreach ($groupedByCompetency as $compId => $compItems) {
            $competencyName = $compId === 'ungrouped' ? 'Skills Independientes' : $compItems->first()->competency_name;
            
            $skillsPayload = [];
            $groupedBySkill = $compItems->groupBy('skill_id');
            
            foreach ($groupedBySkill as $skillId => $skillItems) {
                $evaluations = $skillItems->map(function ($f) {
                    return [
                        'source' => $f->relationship,
                        'raw_score' => (float) $f->score,
                        'qualitative_comment' => $f->qualitative_feedback,
                    ];
                })->toArray();
                
                $rawAverage = collect($evaluations)->avg('raw_score');
                $skillsPayload[] = [
                    'skill_id' => $skillId,
                    'skill_name' => $skillItems->first()->skill_name,
                    'evaluations' => $evaluations,
                    'raw_average_score' => round($rawAverage, 2),
                ];
            }

            $contextPayload[] = [
                'competency_id' => $compId === 'ungrouped' ? null : $compId,
                'competency_name' => $competencyName,
                'skills' => $skillsPayload
            ];
        }

        // 3. Diseño de Prompt "Anti-Sesgo" (Triangulación Híbrida Molecular)
        $prompt = "Actúa como un Analista Experto de Talento (Stratos 360). Vas a triangular las evaluaciones cruzadas del colaborador clave: {$person->full_name}.\n\n";
        $prompt .= "🧠 CONTEXTO METODOLÓGICO:\n";
        $prompt .= "1. Las 'Skills' (átomos) se agrupan en 'Competencias' (moléculas).\n";
        $prompt .= "2. Tu trabajo es NEUTRALIZAR sesgos (Manager severo, Pares complacientes) en cada Skill y luego calcular un puntaje global para la Competencia.\n\n";
        $prompt .= "⚙️ REGLAS DE TRIANGULACIÓN:\n";
        $prompt .= "1. Para cada 'Skill', lee los comentarios cualitativos. Ajusta el 'stratos_score' si detectas sesgos evidentes (Ej: Manager puso 2 sin contexto, Pares pusieron 5 con evidencias contundentes -> Triangulación a 4).\n";
        $prompt .= "2. Los puntajes de Skill ('stratos_score') deben ser enteros 1-5.\n";
        $prompt .= "3. El 'competency_score' será un ponderado de sus Skills (puede tener decimales, max 5.0).\n\n";

        $prompt .= "📦 DATOS RAW:\n" . json_encode($contextPayload, JSON_UNESCAPED_UNICODE) . "\n\n";
        
        $prompt .= "🛑 REQUERIMIENTO TÉCNICO:\nDEBES DEVOLVER EXCLUSIVAMENTE UN JSON con la siguiente estructura y NADA MÁS:\n";
        $prompt .= '{
  "overall_bias_detected": "Resumen macro (1 párrafo) de los sesgos generales detectados.",
  "triangulated_competencies": [
     {
       "competency_id": 4,
       "competency_name": "Liderazgo Estratégico",
       "competency_score": 4.2,
       "competency_justification": "Sólido liderazgo detectado. El puntaje se ajustó al alza mitigando la extrema severidad del manager.",
       "skills": [
          {
            "skill_id": 123,
            "raw_score": 3.75,
            "stratos_score": 4,
            "bias_flag": "severity_from_manager",
            "ai_justification": "Se neutralizó severidad del manager al no tener evidencia, apoyado por consenso de pares."
          }
       ]
     }
  ]
}';

        // 4. Invocación al Cerebro de Stratos (LLM)
        try {
            $agentResponse = $this->orchestrator->agentThink('Orquestador 360', $prompt);
            
            $analysisString = $agentResponse['response'] ?? '{}';
            $analysisString = str_replace('```json', '', $analysisString);
            $analysisString = str_replace('```', '', $analysisString);
            $analysis = json_decode(trim($analysisString), true);

            if (!$analysis || !isset($analysis['triangulated_competencies'])) {
                Log::error("Stratos Triangulation Parse Error", ['string' => $analysisString]);
                return [
                    'status' => 'error',
                    'message' => 'Fallo al parsear la respuesta JSON de Triangulación.',
                ];
            }

            // 5. Estampado en Base de Datos (Level Skill Update)
            DB::transaction(function () use ($person, $analysis) {
                foreach ($analysis['triangulated_competencies'] as $comp) {
                    foreach ($comp['skills'] as $calibratedSkill) {
                        PeopleRoleSkills::updateOrCreate(
                            [
                                'people_id' => $person->id,
                                'skill_id' => $calibratedSkill['skill_id'],
                                'is_active' => true
                            ],
                            [
                                'current_level' => collect([1, 2, 3, 4, 5])->contains($calibratedSkill['stratos_score']) ? $calibratedSkill['stratos_score'] : round($calibratedSkill['raw_score']),
                                'notes' => "[Stratos AI]: " . ($calibratedSkill['ai_justification'] ?? 'Corregido') . " | Bias: " . ($calibratedSkill['bias_flag'] ?? 'None'),
                                'verified' => true,
                                'evaluated_at' => now(),
                                'evidence_source' => 'Stratos360_IA_Triangulated'
                            ]
                        );
                    }
                }
                Log::info("Triangulación Molecular completada para {$person->full_name}");
            });

            return [
                'status' => 'success',
                'report' => $analysis,
                'context' => $contextPayload
            ];

        } catch (\Exception $e) {
            Log::error('Triangulation Error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Excepción durante la triangulación.',
                'error' => $e->getMessage()
            ];
        }
    }
}
