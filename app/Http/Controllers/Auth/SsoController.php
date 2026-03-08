<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;

class SsoController extends Controller
{
    /**
     * Redirecciona al proveedor de OAuth (Google, Microsoft, etc).
     */
    public function redirect(string $provider): RedirectResponse
    {
        if (!in_array($provider, ['google', 'microsoft'])) {
            return redirect('/login')->with('error', 'Proveedor no soportado.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Maneja el callback del proveedor tras la autenticación.
     */
    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            \Log::error("SSO Error with $provider: " . $e->getMessage());
            return redirect('/login')->with('error', 'Error al autenticar con ' . ucfirst($provider));
        }

        // 1. Buscar usuario por ID de proveedor
        $user = User::where('provider_id', $socialUser->getId())
            ->where('provider_name', $provider)
            ->first();

        if (!$user) {
            // 2. Si no existe por ID, buscar por email (vincular cuenta)
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'provider_id' => $socialUser->getId(),
                    'provider_name' => $provider,
                ]);
            } else {
                // 3. Auto-crear usuario (Estilo "Bridge" para nuevas organizaciones o invitados)
                // En un entorno riguroso se validaría el dominio del email.
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Usuario Stratos',
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(Str::random(24)), // Password random, login es via SSO
                    'provider_id' => $socialUser->getId(),
                    'provider_name' => $provider,
                    'email_verified_at' => now(),
                    'role' => 'user', // Rol por defecto
                ]);
            }
        }

        // Actualizar último login
        $user->update(['last_login_at' => now()]);
        
        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}
