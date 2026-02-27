<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Traits\HasSystemRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RBACController extends Controller
{
    /**
     * Get all roles and their permissions.
     */
    public function index()
    {
        $roles = HasSystemRole::systemRoles();
        $allPermissions = Permission::orderBy('module')->get();
        
        $roleMappings = [];
        foreach (array_keys($roles) as $role) {
            $roleMappings[$role] = DB::table('role_permissions')
                ->where('role', $role)
                ->pluck('permission_id')
                ->toArray();
        }

        return response()->json([
            'roles' => $roles,
            'permissions' => $allPermissions,
            'mappings' => $roleMappings
        ]);
    }

    /**
     * Sync permissions for a specific role.
     */
    public function update(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
            'permissions' => 'required|array'
        ]);

        $role = $request->role;
        $permissions = $request->permissions;

        // Use a transaction to ensure atomic update
        DB::transaction(function () use ($role, $permissions) {
            DB::table('role_permissions')->where('role', $role)->delete();
            
            $data = array_map(function ($pId) use ($role) {
                return [
                    'role' => $role,
                    'permission_id' => $pId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }, $permissions);

            if (!empty($data)) {
                DB::table('role_permissions')->insert($data);
            }
        });

        // Clear cache for this role
        // Since clearPermissionCache is an instance method on User, 
        // and we want to clear it globally for the role:
        \Illuminate\Support\Facades\Cache::forget("rbac.permissions.{$role}");

        return response()->json([
            'success' => true,
            'message' => "Permisos actualizados para el rol: {$role}"
        ]);
    }
}
