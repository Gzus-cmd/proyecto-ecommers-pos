<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Controlador para la gestión del perfil del usuario autenticado.
 */
class ProfileController extends Controller
{
    /**
     * Muestra el formulario de edición del perfil.
     *
     * @param  Request $request  Petición actual
     * @return \Inertia\Response
     */
    public function edit(Request $request)
    {
        return inertia('Settings/Profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualiza los datos del perfil (nombre y email).
     *
     * @param  Request $request  Datos validados del perfil
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return redirect()->route('settings.profile')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Actualiza la contraseña del usuario autenticado.
     *
     * @param  Request $request  Contraseña actual y nueva contraseña
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password'      => ['required', 'current_password'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('settings.profile')
            ->with('success', 'Contraseña actualizada correctamente.');
    }
}
