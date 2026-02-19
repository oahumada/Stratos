<?php

namespace App\Services\Talent;

use App\Models\People;
use App\Models\PeopleRoleSkills;
use Illuminate\Database\Eloquent\Collection;

class MentorMatchingService
{
    /**
     * Encuentra mentores potenciales para una habilidad específica.
     * Criterios:
     * - PeopleRoleSkills: certified level >= 4 (Highly Competent)
     * - People: Active status
     * - (Opcional) Performance: High KPI score > 80
 *
     * @param int $skillId
     * @param int $minLevel
     * @param int $limit
     * @return Collection
     */
    public function findMentors(int $skillId, int $minLevel = 4, int $limit = 5): Collection
    {
        // 1. Encontrar personas con la habilidad (nivel alto)
        $experts = PeopleRoleSkills::where('skill_id', $skillId)
            ->where('current_level', '>=', $minLevel)
            ->where('verified', true) // Asegura que no sea solo autopercepción
            ->with(['person.organization', 'role'])
            ->limit($limit + 5) // Fetch extra para filtrar por performance después
            ->get();

        // 2. Extraer los perfiles de personas
        $mentors = $experts->map(function ($expert) {
            $person = $expert->person;
            
            // Simulación de Performance Check (hasta que tengamos tabla real)
            // En un sistema real, cruzaríamos con PerformanceRating
            $person->mentor_rating = rand(85, 99); // Mock quality score
            $person->expertise_level = $expert->current_level;
            $person->role_name = $expert->role->name ?? 'N/A';
            
            return $person;
        });

        // 3. Filtrar empleados inactivos y ordenar por expertise + performance
        return $mentors->filter(function ($p) {
            return $p->status === 'active';
        })->sortByDesc('expertise_level')
          ->sortByDesc('mentor_rating')
          ->take($limit);
    }
}
