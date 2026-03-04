<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\MagicLinkEmail;

class MagicLinkController extends Controller
{
    /**
     * Request a new magic link for the given email.
     */
    public function requestLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // We return success even if user not found to prevent email enumeration,
            // but in an internal tool, we might want to tell them.
            // For now, let's return an error so the user knows.
            return back()->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
        }

        // Generate a signed URL that expires in 30 minutes
        $url = URL::temporarySignedRoute(
            'magic.login',
            now()->addMinutes(30),
            ['user' => $user->id]
        );

        // Send the email
        Mail::to($user->email)->send(new MagicLinkEmail($url));

        // For demo/dev purposes if mail is not configured
        \Log::info("Magic Link URL for {$user->email}: {$url}");

        return back()->with('status', '¡Magic Link enviado! Revisa tu bandeja de entrada.');
    }

    /**
     * Authenticate the user from the signed magic link.
     */
    public function authenticate(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'El enlace ha expirado o es inválido.');
        }

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}
