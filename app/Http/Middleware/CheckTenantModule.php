<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $module
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (!$user || !$user->organization) {
            return response()->json(['message' => 'Unauthorized or no organization associated.'], 403);
        }

        if (!$user->organization->hasModule($module)) {
            return response()->json([
                'message' => 'Módulo inactivo. Contacta a soporte para habilitar esta característica.',
                'required_module' => $module
            ], 403); // Forbidden
        }

        return $next($request);
    }
}
