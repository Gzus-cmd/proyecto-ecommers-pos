<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controlador de autenticación de usuarios.
 * Maneja el inicio y cierre de sesión vía Inertia.
 */
class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Procesa el intento de inicio de sesión.
     *
     * @param  LoginRequest  $request  Credenciales del usuario
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('pos.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión del usuario e invalida la sesión.
     *
     * @param  Request  $request  Petición actual
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
