<?php

namespace App\Services\Lms;

use App\Models\LmsCommunity;
use Illuminate\Support\Facades\Log;

/**
 * AI-driven community formation — suggests/creates communities
 * when Workforce Planning detects critical skill gaps.
 */
class CommunityFormationService
{
    public function __construct(
        protected CommunityService $communityService,
    ) {}

    /**
     * Analyze skill gaps and suggest community formation.
     *
     * @param  array  $gaps  Array of ['skill_name' => string, 'gap_size' => float, 'affected_count' => int]
     * @return array Suggestions with action (existing|suggest)
     */
    public function analyzeAndSuggest(int $orgId, array $gaps, float $threshold = 2.0, int $minAffected = 3): array
    {
        $suggestions = [];

        foreach ($gaps as $gap) {
            if (($gap['gap_size'] ?? 0) < $threshold || ($gap['affected_count'] ?? 0) < $minAffected) {
                continue;
            }

            $existing = LmsCommunity::forOrganization($orgId)
                ->active()
                ->where(function ($q) use ($gap) {
                    $q->where('practice_domain', $gap['skill_name'])
                        ->orWhereJsonContains('domain_skills', $gap['skill_name']);
                })
                ->first();

            if ($existing) {
                $suggestions[] = [
                    'action' => 'existing',
                    'skill' => $gap['skill_name'],
                    'gap_size' => $gap['gap_size'],
                    'affected_count' => $gap['affected_count'],
                    'community_id' => $existing->id,
                    'community_name' => $existing->name,
                    'message' => "Comunidad existente '{$existing->name}' cubre esta brecha.",
                ];
                continue;
            }

            $suggestions[] = [
                'action' => 'suggest',
                'skill' => $gap['skill_name'],
                'gap_size' => $gap['gap_size'],
                'affected_count' => $gap['affected_count'],
                'suggested_name' => "Comunidad de {$gap['skill_name']}",
                'suggested_type' => 'practice',
                'message' => "Brecha crítica: {$gap['affected_count']} personas con gap de {$gap['gap_size']} en {$gap['skill_name']}. Se recomienda crear una comunidad de práctica.",
            ];
        }

        Log::info("CommunityFormation: org {$orgId}, ".count($suggestions).' suggestions from '.count($gaps).' gaps');

        return $suggestions;
    }

    /**
     * Auto-create a community from a detected gap.
     */
    public function createFromGap(int $orgId, array $gap, ?int $facilitatorId = null): LmsCommunity
    {
        return $this->communityService->create($orgId, [
            'name' => "Comunidad de {$gap['skill_name']}",
            'description' => "Comunidad de práctica creada automáticamente para cerrar la brecha en {$gap['skill_name']}. ".
                "Detectada por Workforce Planning: {$gap['affected_count']} personas con gap promedio de {$gap['gap_size']}.",
            'type' => 'practice',
            'practice_domain' => $gap['skill_name'],
            'domain_skills' => [$gap['skill_name']],
            'learning_goals' => ["Cerrar brecha de {$gap['skill_name']}", 'Compartir mejores prácticas'],
            'facilitator_id' => $facilitatorId,
        ]);
    }
}
