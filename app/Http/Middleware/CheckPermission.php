<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if the authenticated user has a specific permission.
 *
 * Usage in routes:
 *   ->middleware('permission:scenarios.create')
 *   ->middleware('permission:evaluations.manage')
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user->hasPermission($permission)) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta acción.',
                'required_permission' => $permission,
                'your_role' => $user->role ?? 'collaborator',
            ], 403);
        }

        return $next($request);
    }
}
