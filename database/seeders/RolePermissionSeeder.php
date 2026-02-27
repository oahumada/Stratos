<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definir los permisos base del sistema
        $permissions = [
            // Escenarios Estratégicos
            ['name' => 'scenarios.view', 'module' => 'scenarios', 'action' => 'view', 'description' => 'Ver escenarios estratégicos'],
            ['name' => 'scenarios.create', 'module' => 'scenarios', 'action' => 'create', 'description' => 'Crear escenarios'],
            ['name' => 'scenarios.edit', 'module' => 'scenarios', 'action' => 'edit', 'description' => 'Editar escenarios'],
            ['name' => 'scenarios.delete', 'module' => 'scenarios', 'action' => 'delete', 'description' => 'Eliminar escenarios'],

            // Roles y Competencias
            ['name' => 'roles.view', 'module' => 'roles', 'action' => 'view', 'description' => 'Ver roles del negocio'],
            ['name' => 'roles.manage', 'module' => 'roles', 'action' => 'manage', 'description' => 'Crear/Editar roles del negocio'],
            ['name' => 'competencies.view', 'module' => 'competencies', 'action' => 'view', 'description' => 'Ver diccionario de competencias'],
            ['name' => 'competencies.manage', 'module' => 'competencies', 'action' => 'manage', 'description' => 'Gestionar competencias y BARS'],

            // Evaluación 360
            ['name' => 'assessments.view', 'module' => 'assessments', 'action' => 'view', 'description' => 'Ver evaluaciones'],
            ['name' => 'assessments.manage', 'module' => 'assessments', 'action' => 'manage', 'description' => 'Configurar ciclos 360'],
            ['name' => 'assessments.respond', 'module' => 'assessments', 'action' => 'respond', 'description' => 'Responder a encuestas asignadas'],

            // Talento / Personal
            ['name' => 'people.view', 'module' => 'people', 'action' => 'view', 'description' => 'Ver directorio de talento'],
            ['name' => 'people.manage', 'module' => 'people', 'action' => 'manage', 'description' => 'Añadir/editar personas'],
            ['name' => 'people.view_my_profile', 'module' => 'people', 'action' => 'view_my_profile', 'description' => 'Ver propio perfil (Mi Stratos)'],

            // Agentes AI
            ['name' => 'agents.view', 'module' => 'agents', 'action' => 'view', 'description' => 'Ver configuración de agentes AI'],
            ['name' => 'agents.manage', 'module' => 'agents', 'action' => 'manage', 'description' => 'Configurar prompts de agentes'],

            // Settings 
            ['name' => 'settings.view', 'module' => 'settings', 'action' => 'view', 'description' => 'Ver configuración del sistema'],
            ['name' => 'settings.manage', 'module' => 'settings', 'action' => 'manage', 'description' => 'Editar configuración del sistema'],
        ];

        // Insertar o actualizar permisos
        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        // 2. Mapear qué roles tienen qué permisos
        $rolePermissions = [
            'admin' => [
                // Adicionalmente admin tiene acceso por código, pero mapeamos explícitamente si se consulta BD
                '*' // Helper virtual manejado en el trait (true) pero insertaremos todos
            ],
            'hr_leader' => [
                'scenarios.view', 'scenarios.create', 'scenarios.edit', 'scenarios.delete',
                'roles.view', 'roles.manage',
                'competencies.view', 'competencies.manage',
                'assessments.view', 'assessments.manage', 'assessments.respond',
                'people.view', 'people.manage', 'people.view_my_profile',
                'agents.view', // No manage agents
                // No settings
            ],
            'manager' => [
                'scenarios.view', // Only read
                'roles.view',
                'competencies.view',
                'assessments.view', 'assessments.respond', // Can't start a whole cycle
                'people.view', 'people.view_my_profile', // See team
            ],
            'collaborator' => [
                'assessments.respond',
                'people.view_my_profile', // El core portal Mi Stratos
            ],
            'observer' => [
                'scenarios.view',
                'assessments.view',
                'people.view',
            ],
        ];

        DB::table('role_permissions')->truncate();

        $allPermissions = Permission::all()->pluck('id', 'name');

        foreach ($rolePermissions as $role => $perms) {
            if ($perms === ['*']) {
                $perms = $allPermissions->keys()->toArray();
            }

            foreach ($perms as $permName) {
                if ($permissionId = $allPermissions->get($permName)) {
                    DB::table('role_permissions')->insert([
                        'role' => $role,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
