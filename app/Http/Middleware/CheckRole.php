<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if the authenticated user has one of the required system roles.
 *
 * Usage in routes:
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,hr_leader')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user->hasAnyRole($roles)) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a este recurso.',
                'required_roles' => $roles,
                'your_role' => $user->role ?? 'collaborator',
            ], 403);
        }

        return $next($request);
    }
}
