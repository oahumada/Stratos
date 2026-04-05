<?php

namespace App\Services\Agents;

use App\Models\LmsCohort;
use App\Models\LmsCohortMember;
use App\Models\LmsDiscussion;
use App\Models\LmsUserContent;
use App\Models\LmsLearnerProfile;
use App\Models\LmsCourse;
use App\Models\User;
use App\Services\Lms\CohortService;
use App\Services\Lms\DiscussionService;
use App\Services\Lms\UgcService;
use App\Services\Lms\PeerReviewService;
use App\Services\Lms\AdaptiveLearningService;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Facilitador de Comunidades de Aprendizaje
 *
 * Agente experto en Comunidades de Práctica (Wenger), Community of Inquiry (Garrison),
 * Connectivism (Siemens), Legitimate Peripheral Participation (Lave & Wenger)
 * y Social Learning Theory (Bandura).
 *
 * Diseña, lanza y nutre comunidades de aprendizaje alineadas con brechas de skills.
 * Gestiona la progresión de roles y mide la salud de cada comunidad.
 *
 * Marco teórico: docs/STRATOS_LEARNING_COMMUNITIES_MODEL.md
 */
class CommunityFacilitatorAgent
{
    // Role progression based on Legitimate Peripheral Participation (LPP)
    const ROLE_NOVICE = 'novice';
    const ROLE_MEMBER = 'member';
    const ROLE_CONTRIBUTOR = 'contributor';
    const ROLE_MENTOR = 'mentor';
    const ROLE_EXPERT = 'expert';
    const ROLE_LEADER = 'leader';

    const ROLE_PROGRESSION = [
        self::ROLE_NOVICE,
        self::ROLE_MEMBER,
        self::ROLE_CONTRIBUTOR,
        self::ROLE_MENTOR,
        self::ROLE_EXPERT,
        self::ROLE_LEADER,
    ];

    // Community health thresholds
    const HEALTH_CRITICAL = 'critical';
    const HEALTH_AT_RISK = 'at_risk';
    const HEALTH_HEALTHY = 'healthy';
    const HEALTH_THRIVING = 'thriving';

    protected CohortService $cohortService;
    protected DiscussionService $discussionService;
    protected UgcService $ugcService;
    protected PeerReviewService $peerReviewService;
    protected AdaptiveLearningService $adaptiveService;

    public function __construct(
        CohortService $cohortService,
        DiscussionService $discussionService,
        UgcService $ugcService,
        PeerReviewService $peerReviewService,
        AdaptiveLearningService $adaptiveService,
    ) {
        $this->cohortService = $cohortService;
        $this->discussionService = $discussionService;
        $this->ugcService = $ugcService;
        $this->peerReviewService = $peerReviewService;
        $this->adaptiveService = $adaptiveService;
    }

    /**
     * Design a new learning community based on CoP framework (Domain + Community + Practice).
     *
     * @param int $orgId Organization ID
     * @param array $config {
     *   name: string, description: string,
     *   domain_skills: string[], learning_goals: string[],
     *   course_id: ?int, facilitator_id: ?int,
     *   max_members: ?int
     * }
     * @return LmsCohort The created community (uses Cohort as base entity)
     */
    public function designCommunity(int $orgId, array $config): LmsCohort
    {
        Log::info("CommunityFacilitator: designing community '{$config['name']}' for org {$orgId}");

        $cohort = $this->cohortService->create($orgId, [
            'name' => $config['name'],
            'description' => $this->buildCommunityDescription($config),
            'course_id' => $config['course_id'] ?? null,
            'facilitator_id' => $config['facilitator_id'] ?? null,
            'max_members' => $config['max_members'] ?? null,
            'starts_at' => now(),
            'is_active' => true,
        ]);

        // Add facilitator as leader if specified
        if (!empty($config['facilitator_id'])) {
            $this->cohortService->addMember(
                $cohort->id,
                $config['facilitator_id'],
                'facilitator'
            );
        }

        Log::info("CommunityFacilitator: community {$cohort->id} created with CoP framework");

        return $cohort;
    }

    /**
     * Onboard a new member following LPP (Legitimate Peripheral Participation).
     * New members start as "novice" with guided peripheral activities.
     */
    public function onboardMember(int $cohortId, int $userId, int $orgId): array
    {
        // Add as member with novice role
        $this->cohortService->addMember($cohortId, $userId, 'member');

        // Create/update learner profile for adaptive tracking
        $profile = $this->adaptiveService->getOrCreateProfile($userId, $orgId);

        // Generate onboarding tasks (LPP: start with low-risk, productive tasks)
        $onboardingPlan = $this->generateOnboardingPlan($cohortId, $userId, $profile);

        Log::info("CommunityFacilitator: onboarded user {$userId} to community {$cohortId} as novice");

        return [
            'role' => self::ROLE_NOVICE,
            'onboarding_plan' => $onboardingPlan,
            'message' => 'Bienvenido a la comunidad. Comienza observando las discusiones y completando tu ruta de entrada.',
        ];
    }

    /**
     * Evaluate a member's participation and determine if they should be promoted
     * in the LPP progression (Novice → Member → Contributor → Mentor → Expert → Leader).
     */
    public function evaluateProgression(int $cohortId, int $userId, int $orgId): array
    {
        $metrics = $this->getMemberMetrics($cohortId, $userId, $orgId);
        $currentRole = $this->getCurrentRole($metrics);
        $nextRole = $this->calculateNextRole($currentRole, $metrics);

        $promoted = $nextRole !== $currentRole;

        if ($promoted) {
            Log::info("CommunityFacilitator: promoting user {$userId} from {$currentRole} to {$nextRole} in community {$cohortId}");
        }

        return [
            'current_role' => $currentRole,
            'next_role' => $nextRole,
            'promoted' => $promoted,
            'metrics' => $metrics,
            'criteria' => $this->getProgressionCriteria($nextRole),
        ];
    }

    /**
     * Assess community health based on CoI framework (Cognitive, Social, Teaching presence)
     * and CoP vitality indicators (Domain relevance, Community engagement, Practice evolution).
     */
    public function assessCommunityHealth(int $cohortId, int $orgId): array
    {
        $cohort = LmsCohort::findOrFail($cohortId);
        $memberCount = LmsCohortMember::where('cohort_id', $cohortId)->count();

        // Social Presence (CoI): interaction frequency and quality
        $discussionCount = LmsDiscussion::where('organization_id', $orgId)
            ->where('course_id', $cohort->course_id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Cognitive Presence (CoI): knowledge creation and sharing
        $ugcCount = LmsUserContent::where('organization_id', $orgId)
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Teaching Presence (CoI): facilitation and mentoring activity
        $mentorCount = LmsCohortMember::where('cohort_id', $cohortId)
            ->where('role', 'mentor')
            ->count();

        // Calculate health scores (0-100)
        $socialScore = min(100, ($discussionCount / max($memberCount, 1)) * 25);
        $cognitiveScore = min(100, ($ugcCount / max($memberCount, 1)) * 50);
        $teachingScore = $mentorCount > 0 ? min(100, ($mentorCount / max($memberCount, 1)) * 200) : 0;

        $overallScore = ($socialScore * 0.4) + ($cognitiveScore * 0.35) + ($teachingScore * 0.25);

        $healthStatus = match (true) {
            $overallScore >= 75 => self::HEALTH_THRIVING,
            $overallScore >= 50 => self::HEALTH_HEALTHY,
            $overallScore >= 25 => self::HEALTH_AT_RISK,
            default => self::HEALTH_CRITICAL,
        };

        $recommendations = $this->generateHealthRecommendations($healthStatus, $socialScore, $cognitiveScore, $teachingScore);

        return [
            'community_id' => $cohortId,
            'member_count' => $memberCount,
            'health_status' => $healthStatus,
            'overall_score' => round($overallScore, 1),
            'presence_scores' => [
                'social' => round($socialScore, 1),
                'cognitive' => round($cognitiveScore, 1),
                'teaching' => round($teachingScore, 1),
            ],
            'activity_30d' => [
                'discussions' => $discussionCount,
                'knowledge_articles' => $ugcCount,
                'active_mentors' => $mentorCount,
            ],
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Suggest mentor-mentee pairings based on skill proficiency levels and learner profiles.
     * Implements Connectivism principle: connecting specialized nodes.
     */
    public function suggestMentorships(int $cohortId, int $orgId): array
    {
        $members = LmsCohortMember::where('cohort_id', $cohortId)
            ->with('user')
            ->get();

        $profiles = [];
        foreach ($members as $member) {
            $profile = LmsLearnerProfile::where('user_id', $member->user_id)
                ->where('organization_id', $orgId)
                ->first();

            if ($profile) {
                $profiles[] = [
                    'user_id' => $member->user_id,
                    'role' => $member->role,
                    'proficiency' => $profile->proficiency_level,
                    'strengths' => $profile->strengths ?? [],
                    'weaknesses' => $profile->weaknesses ?? [],
                    'average_score' => $profile->average_score,
                ];
            }
        }

        $pairings = $this->matchMentorMentee($profiles);

        Log::info("CommunityFacilitator: suggested {$pairings['count']} mentorships for community {$cohortId}");

        return $pairings;
    }

    /**
     * Generate a community activity prompt using AI.
     * Leverages CoI framework to design activities that balance all three presences.
     */
    public function generateActivityPrompt(int $cohortId, int $orgId, string $focusArea): array
    {
        $health = $this->assessCommunityHealth($cohortId, $orgId);
        $weakestPresence = $this->identifyWeakestPresence($health['presence_scores']);

        // Design activity that strengthens the weakest presence
        $activity = match ($weakestPresence) {
            'social' => [
                'type' => 'ice_breaker',
                'title' => "Ronda de presentaciones: {$focusArea}",
                'description' => "Cada miembro comparte una experiencia personal relacionada con {$focusArea}. Comenten y encuentren conexiones.",
                'presence_target' => 'social',
                'format' => 'discussion',
                'duration_minutes' => 15,
            ],
            'cognitive' => [
                'type' => 'knowledge_synthesis',
                'title' => "Wiki colectiva: {$focusArea}",
                'description' => "Contribuyan con un artículo, recurso o best practice sobre {$focusArea}. El objetivo es crear una base de conocimiento compartida.",
                'presence_target' => 'cognitive',
                'format' => 'ugc',
                'duration_minutes' => 30,
            ],
            'teaching' => [
                'type' => 'peer_teaching',
                'title' => "Sesión de mentoría cruzada: {$focusArea}",
                'description' => "Los miembros con más experiencia guían a los más nuevos en {$focusArea}. Preparen un mini-taller de 10 minutos.",
                'presence_target' => 'teaching',
                'format' => 'session',
                'duration_minutes' => 45,
            ],
        };

        return [
            'activity' => $activity,
            'community_health' => $health['health_status'],
            'rationale' => "La presencia más débil es '{$weakestPresence}' (score: {$health['presence_scores'][$weakestPresence]}). Esta actividad busca fortalecerla.",
        ];
    }

    /**
     * Measure the impact of a community on skill gap closure.
     * Connects community participation with Skill Intelligence data.
     */
    public function measureSkillImpact(int $cohortId, int $orgId): array
    {
        $members = LmsCohortMember::where('cohort_id', $cohortId)->get();
        $memberIds = $members->pluck('user_id')->toArray();

        $profiles = LmsLearnerProfile::whereIn('user_id', $memberIds)
            ->where('organization_id', $orgId)
            ->get();

        $avgScore = $profiles->avg('average_score') ?? 0;
        $completedAssessments = $profiles->sum('completed_assessments');
        $proficiencyDistribution = $profiles->groupBy('proficiency_level')
            ->map(fn ($group) => $group->count())
            ->toArray();

        return [
            'community_id' => $cohortId,
            'total_members' => count($memberIds),
            'profiles_with_data' => $profiles->count(),
            'average_score' => round($avgScore, 2),
            'total_assessments_completed' => $completedAssessments,
            'proficiency_distribution' => $proficiencyDistribution,
            'interpretation' => $this->interpretImpact($avgScore, $proficiencyDistribution),
        ];
    }

    // --- Private helpers ---

    private function buildCommunityDescription(array $config): string
    {
        $desc = $config['description'] ?? '';
        $skills = implode(', ', $config['domain_skills'] ?? []);
        $goals = implode('; ', $config['learning_goals'] ?? []);

        if ($skills) {
            $desc .= "\n\n📌 **Skills dominio**: {$skills}";
        }
        if ($goals) {
            $desc .= "\n🎯 **Objetivos**: {$goals}";
        }
        $desc .= "\n\n_Comunidad diseñada bajo el modelo Stratos Learning Communities (CoP + CoI + Connectivism + LPP)._";

        return trim($desc);
    }

    private function generateOnboardingPlan(int $cohortId, int $userId, $profile): array
    {
        return [
            ['step' => 1, 'action' => 'observe', 'task' => 'Lee las 5 discusiones más recientes de la comunidad', 'estimated_minutes' => 10],
            ['step' => 2, 'action' => 'introduce', 'task' => 'Preséntate en el hilo de bienvenida: quién eres, qué esperas aprender', 'estimated_minutes' => 5],
            ['step' => 3, 'action' => 'explore', 'task' => 'Revisa la Knowledge Base y marca 3 recursos como útiles', 'estimated_minutes' => 15],
            ['step' => 4, 'action' => 'participate', 'task' => 'Comenta en al menos 2 discusiones existentes', 'estimated_minutes' => 10],
            ['step' => 5, 'action' => 'connect', 'task' => 'Identifica un mentor potencial y envíale un mensaje', 'estimated_minutes' => 5],
        ];
    }

    private function getMemberMetrics(int $cohortId, int $userId, int $orgId): array
    {
        $cohort = LmsCohort::findOrFail($cohortId);

        $discussions = LmsDiscussion::where('organization_id', $orgId)
            ->where('user_id', $userId)
            ->where('course_id', $cohort->course_id)
            ->count();

        $ugcPublished = LmsUserContent::where('organization_id', $orgId)
            ->where('author_id', $userId)
            ->where('status', 'published')
            ->count();

        $profile = LmsLearnerProfile::where('user_id', $userId)
            ->where('organization_id', $orgId)
            ->first();

        return [
            'discussions_count' => $discussions,
            'ugc_published' => $ugcPublished,
            'proficiency_level' => $profile?->proficiency_level ?? 'beginner',
            'average_score' => $profile?->average_score ?? 0,
            'completed_assessments' => $profile?->completed_assessments ?? 0,
        ];
    }

    private function getCurrentRole(array $metrics): string
    {
        if ($metrics['ugc_published'] >= 10 && $metrics['discussions_count'] >= 50) {
            return self::ROLE_EXPERT;
        }
        if ($metrics['ugc_published'] >= 5 && $metrics['discussions_count'] >= 20) {
            return self::ROLE_MENTOR;
        }
        if ($metrics['ugc_published'] >= 2 && $metrics['discussions_count'] >= 10) {
            return self::ROLE_CONTRIBUTOR;
        }
        if ($metrics['discussions_count'] >= 3) {
            return self::ROLE_MEMBER;
        }
        return self::ROLE_NOVICE;
    }

    private function calculateNextRole(string $currentRole, array $metrics): string
    {
        $currentIndex = array_search($currentRole, self::ROLE_PROGRESSION);
        $nextIndex = min($currentIndex + 1, count(self::ROLE_PROGRESSION) - 1);
        $candidateRole = self::ROLE_PROGRESSION[$nextIndex];

        // Check if metrics meet criteria for next role
        $criteria = $this->getProgressionCriteria($candidateRole);
        $meetsCriteria = true;

        foreach ($criteria as $key => $threshold) {
            if (isset($metrics[$key]) && $metrics[$key] < $threshold) {
                $meetsCriteria = false;
                break;
            }
        }

        return $meetsCriteria ? $candidateRole : $currentRole;
    }

    private function getProgressionCriteria(string $role): array
    {
        return match ($role) {
            self::ROLE_MEMBER => ['discussions_count' => 3],
            self::ROLE_CONTRIBUTOR => ['discussions_count' => 10, 'ugc_published' => 2],
            self::ROLE_MENTOR => ['discussions_count' => 20, 'ugc_published' => 5],
            self::ROLE_EXPERT => ['discussions_count' => 50, 'ugc_published' => 10],
            self::ROLE_LEADER => ['discussions_count' => 100, 'ugc_published' => 20],
            default => [],
        };
    }

    private function matchMentorMentee(array $profiles): array
    {
        $mentors = collect($profiles)->filter(fn ($p) => in_array($p['proficiency'], ['advanced', 'expert']));
        $mentees = collect($profiles)->filter(fn ($p) => in_array($p['proficiency'], ['beginner', 'intermediate']));

        $pairings = [];
        foreach ($mentees as $mentee) {
            $bestMentor = $mentors->filter(function ($mentor) use ($mentee) {
                // Mentor's strengths should cover mentee's weaknesses (Connectivism: connecting nodes)
                $menteeWeaknesses = $mentee['weaknesses'] ?? [];
                $mentorStrengths = $mentor['strengths'] ?? [];
                return count(array_intersect($mentorStrengths, $menteeWeaknesses)) > 0;
            })->first();

            if ($bestMentor) {
                $overlap = array_intersect($bestMentor['strengths'] ?? [], $mentee['weaknesses'] ?? []);
                $pairings[] = [
                    'mentor_id' => $bestMentor['user_id'],
                    'mentee_id' => $mentee['user_id'],
                    'matching_skills' => array_values($overlap),
                    'confidence' => min(100, count($overlap) * 25),
                ];
            }
        }

        return [
            'pairings' => $pairings,
            'count' => count($pairings),
            'unmatched_mentees' => $mentees->count() - count($pairings),
        ];
    }

    private function generateHealthRecommendations(string $status, float $social, float $cognitive, float $teaching): array
    {
        $recs = [];

        if ($social < 40) {
            $recs[] = [
                'area' => 'social_presence',
                'priority' => 'high',
                'action' => 'Lanzar una actividad de ice-breaker o ronda de presentaciones para fomentar interacción',
                'framework' => 'CoI (Garrison) — Presencia Social',
            ];
        }

        if ($cognitive < 40) {
            $recs[] = [
                'area' => 'cognitive_presence',
                'priority' => 'high',
                'action' => 'Incentivar la creación de artículos, best practices o recursos compartidos (UGC)',
                'framework' => 'CoP (Wenger) — Práctica compartida',
            ];
        }

        if ($teaching < 40) {
            $recs[] = [
                'area' => 'teaching_presence',
                'priority' => 'medium',
                'action' => 'Identificar y asignar mentores entre los miembros más avanzados',
                'framework' => 'LPP (Lave & Wenger) — Participación periférica legítima',
            ];
        }

        if ($status === self::HEALTH_CRITICAL) {
            $recs[] = [
                'area' => 'revitalization',
                'priority' => 'critical',
                'action' => 'Considerar reorganizar la comunidad, renovar facilitador, o fusionar con otra comunidad activa',
                'framework' => 'PLC (DuFour) — Reflexión sobre resultados',
            ];
        }

        if (empty($recs)) {
            $recs[] = [
                'area' => 'growth',
                'priority' => 'low',
                'action' => 'La comunidad está saludable. Considerar expandir a cross-community connections (Landscape of Practice)',
                'framework' => 'Connectivism (Siemens) — Conexiones entre redes',
            ];
        }

        return $recs;
    }

    private function identifyWeakestPresence(array $scores): string
    {
        $min = min($scores);
        foreach ($scores as $key => $value) {
            if ($value === $min) {
                return $key;
            }
        }
        return 'social';
    }

    private function interpretImpact(float $avgScore, array $distribution): string
    {
        $advanced = ($distribution['advanced'] ?? 0) + ($distribution['expert'] ?? 0);
        $total = array_sum($distribution) ?: 1;
        $advancedPct = round(($advanced / $total) * 100);

        if ($avgScore >= 80) {
            return "Impacto alto: {$advancedPct}% de los miembros han alcanzado nivel avanzado/experto. La comunidad está cerrando brechas efectivamente.";
        }
        if ($avgScore >= 60) {
            return "Impacto moderado: score promedio {$avgScore}. La comunidad contribuye al desarrollo pero hay espacio para mejorar la transferencia de conocimiento.";
        }
        return "Impacto bajo: score promedio {$avgScore}. Revisar si el dominio de la comunidad está alineado con las brechas reales de los miembros.";
    }
}
