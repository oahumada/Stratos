<?php

namespace App\Services\Agents;

use App\Models\LmsCommunity;
use App\Models\LmsCommunityMember;
use App\Models\LmsCommunityActivity;
use App\Models\LmsLearnerProfile;
use App\Models\User;
use App\Services\Lms\CommunityService;
use App\Services\Lms\CommunityProgressionService;
use App\Services\Lms\CommunityHealthService;
use App\Services\Lms\MentorMatchingService;
use App\Services\Lms\DiscussionService;
use App\Services\Lms\UgcService;
use App\Services\Lms\PeerReviewService;
use App\Services\Lms\AdaptiveLearningService;
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

    protected CommunityService $communityService;
    protected CommunityProgressionService $progressionService;
    protected CommunityHealthService $healthService;
    protected MentorMatchingService $mentorService;
    protected DiscussionService $discussionService;
    protected UgcService $ugcService;
    protected PeerReviewService $peerReviewService;
    protected AdaptiveLearningService $adaptiveService;

    public function __construct(
        CommunityService $communityService,
        CommunityProgressionService $progressionService,
        CommunityHealthService $healthService,
        MentorMatchingService $mentorService,
        DiscussionService $discussionService,
        UgcService $ugcService,
        PeerReviewService $peerReviewService,
        AdaptiveLearningService $adaptiveService,
    ) {
        $this->communityService = $communityService;
        $this->progressionService = $progressionService;
        $this->healthService = $healthService;
        $this->mentorService = $mentorService;
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
     * @return LmsCommunity The created community
     */
    public function designCommunity(int $orgId, array $config): LmsCommunity
    {
        Log::info("CommunityFacilitator: designing community '{$config['name']}' for org {$orgId}");

        $community = $this->communityService->create($orgId, [
            'name' => $config['name'],
            'description' => $this->buildCommunityDescription($config),
            'type' => $config['type'] ?? 'practice',
            'domain_skills' => $config['domain_skills'] ?? null,
            'learning_goals' => $config['learning_goals'] ?? null,
            'facilitator_id' => $config['facilitator_id'] ?? null,
            'course_id' => $config['course_id'] ?? null,
            'max_members' => $config['max_members'] ?? null,
        ]);

        Log::info("CommunityFacilitator: community {$community->id} created with CoP framework");

        return $community;
    }

    /**
     * Onboard a new member following LPP (Legitimate Peripheral Participation).
     * New members start as "novice" with guided peripheral activities.
     */
    public function onboardMember(int $communityId, int $userId, int $orgId): array
    {
        $community = LmsCommunity::findOrFail($communityId);

        // Add as member with novice role
        $member = $this->communityService->join($community, $userId, self::ROLE_NOVICE);

        // Create/update learner profile for adaptive tracking
        $profile = $this->adaptiveService->getOrCreateProfile($userId, $orgId);

        // Generate onboarding tasks (LPP: start with low-risk, productive tasks)
        $onboardingPlan = $this->generateOnboardingPlan($communityId, $userId, $profile);

        Log::info("CommunityFacilitator: onboarded user {$userId} to community {$communityId} as novice");

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
    public function evaluateProgression(int $communityId, int $userId, int $orgId): array
    {
        $member = LmsCommunityMember::where('community_id', $communityId)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Sync member stats from activities before evaluating
        $this->communityService->updateMemberStats($member);

        $result = $this->progressionService->evaluateProgression($member);

        if ($result['promoted']) {
            Log::info("CommunityFacilitator: promoting user {$userId} from {$result['current_role']} to {$result['next_role']} in community {$communityId}");
        }

        return $result;
    }

    /**
     * Assess community health based on CoI framework (Cognitive, Social, Teaching presence)
     * and CoP vitality indicators (Domain relevance, Community engagement, Practice evolution).
     */
    public function assessCommunityHealth(int $communityId, int $orgId): array
    {
        $community = LmsCommunity::findOrFail($communityId);

        $health = $this->healthService->recalculateAndSave($community);

        $recommendations = $this->generateHealthRecommendations(
            $health['status'],
            $health['social_presence'],
            $health['cognitive_presence'],
            $health['teaching_presence'],
        );

        return [
            'community_id' => $communityId,
            'member_count' => $health['details']['member_count'],
            'health_status' => $health['status'],
            'overall_score' => $health['health_score'],
            'presence_scores' => [
                'social' => $health['social_presence'],
                'cognitive' => $health['cognitive_presence'],
                'teaching' => $health['teaching_presence'],
            ],
            'activity_30d' => $health['details'],
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Suggest mentor-mentee pairings based on skill proficiency levels and learner profiles.
     * Implements Connectivism principle: connecting specialized nodes.
     */
    public function suggestMentorships(int $communityId, int $orgId): array
    {
        $community = LmsCommunity::findOrFail($communityId);

        $pairings = $this->mentorService->suggestMatches($community);

        Log::info("CommunityFacilitator: suggested {$pairings['count']} mentorships for community {$communityId}");

        return $pairings;
    }

    /**
     * Generate a community activity prompt using AI.
     * Leverages CoI framework to design activities that balance all three presences.
     */
    public function generateActivityPrompt(int $communityId, int $orgId, string $focusArea): array
    {
        $health = $this->assessCommunityHealth($communityId, $orgId);
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
    public function measureSkillImpact(int $communityId, int $orgId): array
    {
        $members = LmsCommunityMember::where('community_id', $communityId)->get();
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
            'community_id' => $communityId,
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

    private function generateOnboardingPlan(int $communityId, int $userId, $profile): array
    {
        return [
            ['step' => 1, 'action' => 'observe', 'task' => 'Lee las 5 discusiones más recientes de la comunidad', 'estimated_minutes' => 10],
            ['step' => 2, 'action' => 'introduce', 'task' => 'Preséntate en el hilo de bienvenida: quién eres, qué esperas aprender', 'estimated_minutes' => 5],
            ['step' => 3, 'action' => 'explore', 'task' => 'Revisa la Knowledge Base y marca 3 recursos como útiles', 'estimated_minutes' => 15],
            ['step' => 4, 'action' => 'participate', 'task' => 'Comenta en al menos 2 discusiones existentes', 'estimated_minutes' => 10],
            ['step' => 5, 'action' => 'connect', 'task' => 'Identifica un mentor potencial y envíale un mensaje', 'estimated_minutes' => 5],
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
