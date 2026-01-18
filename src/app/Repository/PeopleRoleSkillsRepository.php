<?php

namespace App\Repository;

use App\Models\PeopleRoleSkills;
use Illuminate\Database\Eloquent\Builder;

class PeopleRoleSkillsRepository extends Repository
{
    public function __construct(PeopleRoleSkills $model)
    {
        $this->model = $model;
    }

    /**
     * Configurar query para búsqueda con relaciones eager loading
     */
    public function getSearchQuery(): Builder
    {
        return $this->model->query()
            ->with([
                'person:id,name,email,role_id,organization_id',
                'role:id,name,level,organization_id',
                'skill:id,name,category,is_critical,organization_id',
                'evaluator:id,name,email',
            ]);
    }

    /**
     * Obtener skills activas de una persona
     */
    public function getActiveSkillsForPerson(int $personId)
    {
        return $this->model->query()
            ->with(['role', 'skill', 'evaluator'])
            ->where('people_id', $personId)
            ->where('is_active', true)
            ->orderBy('skill_id')
            ->get();
    }

    /**
     * Obtener historial de skills de una persona
     */
    public function getSkillHistoryForPerson(int $personId)
    {
        return $this->model->query()
            ->with(['role', 'skill', 'evaluator'])
            ->where('people_id', $personId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener skills que requieren reevaluación
     */
    public function getSkillsNeedingReevaluation(?int $organizationId = null)
    {
        $query = $this->model->query()
            ->with(['person', 'role', 'skill', 'evaluator'])
            ->where('is_active', true)
            ->where('expires_at', '<', now()->addDays(30));

        if ($organizationId) {
            $query->whereHas('person', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });
        }

        return $query->orderBy('expires_at')->get();
    }

    /**
     * Obtener skills expiradas
     */
    public function getExpiredSkills(?int $organizationId = null)
    {
        $query = $this->model->query()
            ->with(['person', 'role', 'skill', 'evaluator'])
            ->where('is_active', true)
            ->where('expires_at', '<', now());

        if ($organizationId) {
            $query->whereHas('person', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });
        }

        return $query->orderBy('expires_at')->get();
    }

    /**
     * Marcar skills antiguas como inactivas al cambiar de rol
     */
    public function deactivateSkillsForPerson(int $personId, ?int $exceptRoleId = null): int
    {
        $query = $this->model->query()
            ->where('people_id', $personId)
            ->where('is_active', true);

        if ($exceptRoleId) {
            $query->where('role_id', '!=', $exceptRoleId);
        }

        return $query->update(['is_active' => false]);
    }

    /**
     * Sincronizar skills de persona con las de un rol
     */
    public function syncSkillsFromRole(int $personId, int $roleId, ?int $evaluatedBy = null): array
    {
        // Obtener skills requeridas por el rol
        $roleSkills = \App\Models\RoleSkill::where('role_id', $roleId)
            ->with('skill')
            ->get();

        $synced = [];
        $expiresAt = now()->addMonths(6); // Validez por defecto: 6 meses

        foreach ($roleSkills as $roleSkill) {
            // Buscar si ya existe una evaluación activa de esta persona para esta skill
            $existingSkill = $this->model->query()
                ->where('people_id', $personId)
                ->where('skill_id', $roleSkill->skill_id)
                ->where('is_active', true)
                ->first();

            if ($existingSkill) {
                // Si existe, actualizar nivel requerido (puede haber cambiado con el nuevo rol)
                $existingSkill->update([
                    'role_id' => $roleId,
                    'required_level' => $roleSkill->required_level,
                    'evaluated_at' => now(),
                    'expires_at' => $expiresAt,
                    'evaluated_by' => $evaluatedBy,
                ]);
                $synced[] = $existingSkill;
            } else {
                // Si no existe, crear nueva entrada
                $newSkill = $this->model->create([
                    'people_id' => $personId,
                    'role_id' => $roleId,
                    'skill_id' => $roleSkill->skill_id,
                    'current_level' => 1, // Nivel inicial
                    'required_level' => $roleSkill->required_level,
                    'is_active' => true,
                    'evaluated_at' => now(),
                    'expires_at' => $expiresAt,
                    'evaluated_by' => $evaluatedBy,
                    'notes' => 'Asignada automáticamente desde rol: ' . $roleSkill->role->name,
                ]);
                $synced[] = $newSkill;
            }
        }

        return $synced;
    }

    /**
     * Obtener gap de skills (diferencia entre requerido y actual)
     */
    public function getSkillGapsForPerson(int $personId)
    {
        return $this->model->query()
            ->with(['skill', 'role'])
            ->where('people_id', $personId)
            ->where('is_active', true)
            ->whereColumn('current_level', '<', 'required_level')
            ->orderByRaw('(required_level - current_level) DESC')
            ->get();
    }

    /**
     * Estadísticas de skills por persona
     */
    public function getStatsForPerson(int $personId): array
    {
        $skills = $this->getActiveSkillsForPerson($personId);

        return [
            'total_skills' => $skills->count(),
            'met_requirements' => $skills->filter(fn($s) => $s->current_level >= $s->required_level)->count(),
            'below_requirements' => $skills->filter(fn($s) => $s->current_level < $s->required_level)->count(),
            'expired' => $skills->filter(fn($s) => $s->isExpired())->count(),
            'needs_reevaluation' => $skills->filter(fn($s) => $s->needsReevaluation())->count(),
            'average_level' => round($skills->avg('current_level'), 2),
            'average_gap' => round($skills->avg(fn($s) => $s->getLevelGap()), 2),
        ];
    }
}
