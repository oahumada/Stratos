<?php

namespace App\Traits;

use App\Models\Permission;
use Illuminate\Support\Facades\Cache;

/**
 * Trait HasSystemRole — RBAC para el modelo User.
 *
 * Roles del sistema (almacenados en users.role):
 * - admin:        Full CRUD en todo, configuración del sistema.
 * - hr_leader:    CRUD en talento, evaluaciones, escenarios. Sin config del sistema.
 * - manager:      Read de su equipo, create/read evaluaciones.
 * - collaborator: Read de su perfil, responder evaluaciones. Portal "Mi Stratos".
 * - observer:     Read-only de dashboards. Para inversionistas / socios.
 */
trait HasSystemRole
{
    /**
     * Check if the user has a specific system role.
     */
    public function hasRole(string $role): bool
    {
        return ($this->role ?? 'collaborator') === $role;
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role ?? 'collaborator', $roles, true);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is at least an HR leader (admin or hr_leader).
     */
    public function isHrOrAbove(): bool
    {
        return $this->hasAnyRole(['admin', 'hr_leader']);
    }

    /**
     * Check if the user has a specific permission (e.g. 'scenarios.create').
     * Results are cached per user role for 1 hour.
     */
    public function can($ability, $arguments = []): bool
    {
        // If it's a dotted permission name (module.action), check RBAC
        if (is_string($ability) && str_contains($ability, '.')) {
            return $this->hasPermission($ability);
        }

        // Fall back to Laravel's default Gate/Policy authorization
        return parent::can($ability, $arguments);
    }

    /**
     * Check if user's role has the given permission.
     */
    public function hasPermission(string $permissionName): bool
    {
        // Admins can do everything
        if ($this->isAdmin()) {
            return true;
        }

        $role = $this->role ?? 'collaborator';
        $permissions = $this->getCachedPermissions($role);

        return $permissions->contains('name', $permissionName);
    }

    /**
     * Get all permission names for the user's role (cached).
     */
    public function getPermissions(): \Illuminate\Support\Collection
    {
        if ($this->isAdmin()) {
            return Permission::all()->pluck('name');
        }

        $role = $this->role ?? 'collaborator';

        return $this->getCachedPermissions($role)->pluck('name');
    }

    /**
     * Get cached permissions for a role.
     */
    protected function getCachedPermissions(string $role): \Illuminate\Support\Collection
    {
        return Cache::remember(
            "rbac.permissions.{$role}",
            3600, // 1 hour
            fn () => Permission::forRole($role)
        );
    }

    /**
     * Clear the RBAC cache for the user's role.
     */
    public function clearPermissionCache(): void
    {
        $role = $this->role ?? 'collaborator';
        Cache::forget("rbac.permissions.{$role}");
    }

    /**
     * Get the system role display name.
     */
    public function getRoleDisplayName(): string
    {
        return match ($this->role) {
            'admin' => 'Administrador',
            'hr_leader' => 'Líder RRHH',
            'manager' => 'Jefe de Equipo',
            'collaborator' => 'Colaborador',
            'observer' => 'Observador',
            default => ucfirst($this->role ?? 'Colaborador'),
        };
    }

    /**
     * Get available system roles.
     */
    public static function systemRoles(): array
    {
        return [
            'admin' => 'Administrador — Full CRUD, configuración del sistema',
            'hr_leader' => 'Líder RRHH — CRUD talento, evaluaciones, escenarios',
            'manager' => 'Jefe de Equipo — Read equipo, gestionar evaluaciones',
            'collaborator' => 'Colaborador — Su perfil, responder evaluaciones',
            'observer' => 'Observador — Read-only dashboards',
        ];
    }
}
