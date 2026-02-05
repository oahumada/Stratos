<?php

namespace App\Services\Scenario;

class RoleMutationService
{
    /**
     * Determina el tipo de mutación de rol comparando habilidades actuales vs objetivo.
     * - 'enrichment' si >=50% de las skills objetivo son nuevas
     * - 'specialization' si el objetivo tiene significativamente menos skills (<=80% del actual)
     * - 'stable' en caso contrario
     *
     * @param array $currentSkills Array de identificadores/string de skills actuales
     * @param array $targetSkills Array de identificadores/string de skills objetivo
     * @return string
     */
    public function calculateRoleMutation(array $currentSkills, array $targetSkills): string
    {
        $current = array_values(array_unique($currentSkills));
        $target = array_values(array_unique($targetSkills));

        $currentCount = count($current);
        $targetCount = count($target);

        if ($targetCount === 0) {
            return 'stable';
        }

        $intersection = array_intersect($current, $target);
        $sharedCount = count($intersection);

        $newCount = $targetCount - $sharedCount;
        $newRatio = $newCount / $targetCount; // proporción de nuevas en objetivo

        if ($newRatio >= 0.5) {
            return 'enrichment';
        }

        if ($currentCount > 0 && $targetCount <= max(1, intval(0.8 * $currentCount))) {
            return 'specialization';
        }

        return 'stable';
    }

    /**
     * Sugiere un arquetipo de rol según perfil simple.
     * - 'specialist' si la proporción de skills especializadas es alta (>0.6)
     * - 'generalist' si el breadth > 10 skills
     * - 'hybrid' en caso contrario
     *
     * @param array $roleProfile ['skills_count'=>int, 'specialized_skills'=>int]
     * @return string
     */
    public function suggestArchetype(array $roleProfile): string
    {
        $skillsCount = max(0, intval($roleProfile['skills_count'] ?? 0));
        $specialized = max(0, intval($roleProfile['specialized_skills'] ?? 0));

        if ($skillsCount === 0) {
            return 'unknown';
        }

        $specRatio = $specialized / max(1, $skillsCount);

        if ($specRatio > 0.6) {
            return 'specialist';
        }

        if ($skillsCount > 10) {
            return 'generalist';
        }

        return 'hybrid';
    }
}
