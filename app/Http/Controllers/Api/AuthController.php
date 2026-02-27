<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Devuelve el usuario autenticado junto a sus permisos del sistema.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        // Si no hay usuario, retornar vacío (manejado por auth:sanctum de todos modos)
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'collaborator',
                'role_display' => $user->getRoleDisplayName(),
                'is_admin' => $user->isAdmin(),
                'permissions' => $user->getPermissions(),
            ],
        ]);
    }
}
