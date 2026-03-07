<?php

namespace App\Services\Talent;

use App\Models\People;
use App\Models\Skill;
use App\Models\Departments;
use App\Services\AiOrchestratorService;
use App\Services\Intelligence\RetentionDeepPredictorService;
use Illuminate\Support\Collection;

class SocialLearningService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
        protected RetentionDeepPredictorService $retentionService,
        protected MentorMatchingService $mentorMatching
    ) {}

    /**
     * Identifica "Knowledge Silos" críticos: Skills clave poseídas solo por personas en riesgo.
     */
    public function identifyContinuityRisks(): Collection
    {
        // 1. Obtener todas las personas con skills críticas
        $criticalPeople = People::whereHas('roleSkills', function($q) {
            $q->where('is_critical', true);
        })->get();

        $risks = collect();

        foreach ($criticalPeople as $person) {
            /** @var People $person */
            // 2. Predecir riesgo de fuga
            $prediction = $this->retentionService->predict($person->id);
            
            if ($prediction['strategic_metrics']['business_continuity_risk'] === 'Critical' && $prediction['flight_risk_score'] > 60) {
                // 3. Esta persona es un "Silo de Conocimiento" en riesgo
                foreach ($person->roleSkills()->whereHas('skill', function($q) {
                    $q->where('is_critical', true);
                })->get() as $prs) {
                    $risks->push([
                        'person' => $person,
                        'skill' => $prs->skill,
                        'risk_score' => $prediction['flight_risk_score'],
                        'reason' => 'Único portador de skill crítica con alta probabilidad de salida.'
                    ]);
                }
            }
        }

        return $risks;
    }

    /**
     * Genera emparejamientos de aprendizaje social basados en "Cross-Pollination".
     */
    public function suggestMatches(int $skillId): Collection
    {
        $mentors = $this->mentorMatching->findMentors($skillId, 4, 3);
        
        // Buscamos potenciales aprendices (personas con gap en esa skill)
        $mentees = People::whereHas('peopleRoleSkills', function($q) use ($skillId) {
            $q->where('skill_id', $skillId)
              ->whereColumn('current_level', '<', 'required_level');
        })->take(5)->get();

        $suggestions = collect();

        foreach ($mentors as $mentor) {
            foreach ($mentees as $mentee) {
                // Evitamos mentoría dentro del mismo equipo directo para fomentar cross-pollination
                $isCrossDept = $mentor->department_id !== $mentee->department_id;
                
                $score = 50;
                if ($isCrossDept) {
                    $score += 30;
                }
                if ($mentor->is_high_potential) {
                    $score += 20;
                }

                $suggestions->push([
                    'mentor' => $mentor,
                    'mentee' => $mentee,
                    'match_score' => $score,
                    'type' => $isCrossDept ? 'Cross-Pollination' : 'Peer-to-Peer'
                ]);
            }
        }

        return $suggestions->sortByDesc('match_score')->take(5)->values();
    }

    /**
     * Usa IA para diseñar un "Learning Blueprint" específico para una dupla de mentoría.
     */
    public function createMentorshipBlueprint(int $mentorId, int $menteeId, int $skillId): array
    {
        $mentor = People::with('role')->findOrFail($mentorId);
        $mentee = People::with('role')->findOrFail($menteeId);
        $skill = Skill::findOrFail($skillId);

        $prompt = "Diseña un plan de Social Learning de 4 semanas. 
        Mentor: {$mentor->full_name} ({$mentor->role->name}). 
        Aprendiz: {$mentee->full_name} ({$mentee->role->name}). 
        Skill a transferir: {$skill->name}.
        
        El plan debe incluir:
        1. 4 objetivos semanales (uno por semana).
        2. Actividad práctica sugerida para cada semana.
        3. Indicador de éxito (KPI) para el mentor.
        
        Formato: JSON puro con estructura: { 'blueprint_name', 'weekly_milestones': [ { 'week', 'objective', 'activity' } ], 'success_indicator' }";

        $response = $this->orchestrator->agentThink('Social Learning Architect', $prompt);
        
        return json_decode($this->cleanJson($response['response']), true);
    }

    private function cleanJson($string)
    {
        return preg_replace('/```json|```/', '', $string);
    }
}
