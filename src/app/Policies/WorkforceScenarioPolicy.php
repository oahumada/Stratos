<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkforcePlanningScenario;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkforceScenarioPolicy
{
    use HandlesAuthorization;

    /**
     * Ver escenarios (solo si pertenece a su org)
     */
    public function view(User $user, WorkforcePlanningScenario $scenario): bool
    {
        return $user->organization_id === $scenario->organization_id;
    }

    /**
     * Crear escenario nuevo (permite si tiene permisos y org coincide)
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('workforce_planning.create');
    }

    /**
     * Actualizar escenario:
     * - BLOQUEADO si decision_status === 'approved'
     * - Permitido solo en 'draft' o 'pending_approval'
     */
    public function update(User $user, WorkforcePlanningScenario $scenario): bool
    {
        if ($user->organization_id !== $scenario->organization_id) {
            return false;
        }

        if (!$user->hasPermissionTo('workforce_planning.update')) {
            return false;
        }

        // REGLA CRÍTICA: No se puede editar escenario aprobado
        if ($scenario->decision_status === 'approved') {
            return false;
        }

        return true;
    }

    /**
     * Eliminar escenario:
     * - BLOQUEADO si decision_status === 'approved'
     * - BLOQUEADO si tiene hijos (parent_id is null pero tiene children)
     */
    public function delete(User $user, WorkforcePlanningScenario $scenario): bool
    {
        if ($user->organization_id !== $scenario->organization_id) {
            return false;
        }

        if (!$user->hasPermissionTo('workforce_planning.delete')) {
            return false;
        }

        // No eliminar escenarios aprobados
        if ($scenario->decision_status === 'approved') {
            return false;
        }

        // No eliminar padres con hijos activos
        if ($scenario->children()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Crear nueva versión de escenario:
     * - Solo permitido en escenarios aprobados (inmutabilidad)
     * - Usuario debe pertenecer a la misma org
     */
    public function createNewVersion(User $user, WorkforcePlanningScenario $scenario): bool
    {
        if ($user->organization_id !== $scenario->organization_id) {
            return false;
        }

        if (!$user->hasPermissionTo('workforce_planning.create')) {
            return false;
        }

        // Solo puedes crear versión de un escenario aprobado
        return $scenario->decision_status === 'approved';
    }

    /**
     * Transicionar estado de decisión:
     * - draft -> pending_approval: cualquier usuario con update
     * - pending_approval -> approved: requiere permiso approve
     * - pending_approval -> rejected: requiere permiso approve
     * - rejected -> draft: cualquier usuario con update
     */
    public function transitionDecisionStatus(
        User $user,
        WorkforcePlanningScenario $scenario,
        string $toStatus
    ): bool {
        if ($user->organization_id !== $scenario->organization_id) {
            return false;
        }

        $from = $scenario->decision_status;

        // Transiciones que requieren permiso especial de aprobación
        if (in_array($toStatus, ['approved', 'rejected']) && $from === 'pending_approval') {
            return $user->hasPermissionTo('workforce_planning.approve');
        }

        // Transiciones normales (draft -> pending, rejected -> draft)
        return $user->hasPermissionTo('workforce_planning.update');
    }

    /**
     * Iniciar ejecución:
     * - Solo escenarios aprobados pueden ejecutarse
     * - execution_status debe ser 'planned' o 'paused'
     */
    public function startExecution(User $user, WorkforcePlanningScenario $scenario): bool
    {
        if ($user->organization_id !== $scenario->organization_id) {
            return false;
        }

        if (!$user->hasPermissionTo('workforce_planning.execute')) {
            return false;
        }

        // Solo iniciar si está aprobado
        if ($scenario->decision_status !== 'approved') {
            return false;
        }

        // Solo si está planned o paused
        return in_array($scenario->execution_status, ['planned', 'paused']);
    }

    /**
     * Pausar ejecución
     */
    public function pauseExecution(User $user, WorkforcePlanningScenario $scenario): bool
    {
        if ($user->organization_id !== $scenario->organization_id) {
            return false;
        }

        if (!$user->hasPermissionTo('workforce_planning.execute')) {
            return false;
        }

        // Solo pausar si está en ejecución
        return $scenario->execution_status === 'in_progress';
    }

    /**
     * Completar ejecución
     */
    public function completeExecution(User $user, WorkforcePlanningScenario $scenario): bool
    {
        if ($user->organization_id !== $scenario->organization_id) {
            return false;
        }

        if (!$user->hasPermissionTo('workforce_planning.execute')) {
            return false;
        }

        // Solo completar si está en ejecución
        return $scenario->execution_status === 'in_progress';
    }

    /**
     * Sincronizar skills desde padre:
     * - Solo escenarios hijo (parent_id !== null)
     * - Usuario debe tener update
     */
    public function syncFromParent(User $user, WorkforcePlanningScenario $scenario): bool
    {
        if ($user->organization_id !== $scenario->organization_id) {
            return false;
        }

        if (!$user->hasPermissionTo('workforce_planning.update')) {
            return false;
        }

        // Solo escenarios hijos pueden sincronizar
        return $scenario->parent_id !== null;
    }
}
