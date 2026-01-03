<?php

namespace App\Services;

use App\Models\People;
use App\Models\Roles;

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
    public function calculate(People $people, Roles $role): array
    {
        // Cargar skills del rol con datos de pivot (required_level, is_critical)
        $roleSkills = $role->skills()->withPivot(['required_level', 'is_critical'])->get();

        $totalSkills = $roleSkills->count();
        $skillsOk = 0;
        $gaps = [];

        foreach ($roleSkills as $roleSkill) {
            // Buscar el nivel actual de la persona para esta skill en people_role_skills
            // Se busca la skill activa de la persona, independiente del rol
            $peopleRoleSkill = $people->roleSkills()
                ->where('skill_id', $roleSkill->id)
                ->where('is_active', true)
                ->first();

            $currentLevel = $peopleRoleSkill ? (int) ($peopleRoleSkill->current_level ?? 0) : 0;
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
                ? 'potencial'
                : ($matchPercentage >= 50 ? 'Gap significativo' : 'no recomendado'));

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
