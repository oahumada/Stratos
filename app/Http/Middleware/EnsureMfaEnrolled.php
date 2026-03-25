<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces mandatory MFA enrollment for high-privilege roles.
 *
 * Roles that require MFA (configurable via REQUIRE_MFA_ROLES env):
 *   - admin
 *   - hr_leader
 *
 * If the authenticated user has one of these roles but has NOT enabled
 * two-factor authentication, they are redirected to the 2FA settings page
 * with a flash warning.  API requests receive a 403 JSON response instead.
 *
 * Usage in routes/web.php or bootstrap/app.php:
 *   ->middleware('mfa.required')
 */
class EnsureMfaEnrolled
{
    /**
     * Roles that require mandatory MFA enrollment.
     *
     * @var list<string>
     */
    protected array $requiredForRoles = ['admin', 'hr_leader'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $userRole = $user->role ?? 'collaborator';

        // Only enforce for privileged roles
        if (! in_array($userRole, $this->requiredForRoles, true)) {
            return $next($request);
        }

        // Check if MFA is enrolled
        if ($user->hasEnabledTwoFactorAuthentication()) {
            return $next($request);
        }

        // MFA is NOT enabled — block the request
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tu rol requiere autenticación de dos factores habilitada.',
                'action' => 'enable_mfa',
                'redirect' => route('two-factor.show'),
            ], 403);
        }

        return redirect()->route('two-factor.show')
            ->with('warning', 'Tu rol ('.$userRole.') requiere configurar la autenticación de dos factores para continuar.');
    }
}
