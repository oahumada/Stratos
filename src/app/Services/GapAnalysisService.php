<?php

namespace App\Services;

use App\Models\People;
use App\Models\Role;

class GapAnalysisService
{
    /**
     * Calcula la brecha entre las competencias de una peoplea y las requeridas por un rol.
     *
     * Retorna un arreglo con:
     * - match_percentage (0-100)
     * - summary: categoría general y conteos
     * - gaps: lista de brechas por skill
     */
    public function calculate(People $people, Role $role): array
    {
        // Cargar skills del rol con datos de pivot (required_level, is_critical)
        $roleSkills = $role->skills()->withPivot(['required_level', 'is_critical'])->get();

        $totalSkills = $roleSkills->count();
        $skillsOk = 0;
        $gaps = [];

        foreach ($roleSkills as $roleSkill) {
            // Buscar el nivel actual de la peoplea para esta skill
            $peopleSkill = $people->skills()
                ->where('skill_id', $roleSkill->id)
                ->first();

            $currentLevel = $peopleSkill ? (int) ($peopleSkill->pivot->level ?? 0) : 0;
            $requiredLevel = (int) ($roleSkill->pivot->required_level ?? 0);
            $gap = max(0, $requiredLevel - $currentLevel);

            $status = $gap === 0
                ? 'ok'
                : ($gap <= 1 ? 'developing' : 'critical');

            if ($gap === 0) {
                $skillsOk++;
            }

            $gaps[] = [
                'skill_id' => $roleSkill->id,
                'skill_name' => $roleSkill->name,
                'current_level' => $currentLevel,
                'required_level' => $requiredLevel,
                'gap' => $gap,
                'status' => $status,
                'is_critical' => (bool) ($roleSkill->pivot->is_critical ?? false),
            ];
        }

        $matchPercentage = $totalSkills > 0
            ? round(($skillsOk / $totalSkills) * 100, 2)
            : 0.0;

        $category = $matchPercentage >= 90
            ? 'ready'
            : ($matchPercentage >= 70
                ? 'potential'
                : ($matchPercentage >= 50 ? 'significant-gap' : 'not-recommended'));

        return [
            'match_percentage' => $matchPercentage,
            'summary' => [
                'category' => $category,
                'skills_ok' => $skillsOk,
                'total_skills' => $totalSkills,
            ],
            'gaps' => $gaps,
        ];
    }
}
