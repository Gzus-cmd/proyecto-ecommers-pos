<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * Middleware que comparte datos globales con todas las respuestas Inertia.
 * Incluye el usuario autenticado, mensajes flash y configuración de la sede.
 */
class HandleInertiaRequests extends Middleware
{
    /**
     * La plantilla raíz que se carga en la primera visita a la página.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Define las propiedades compartidas por defecto con todas las páginas Inertia.
     * Incluye datos del usuario autenticado, roles, mensajes flash y config de sede.
     *
     * @param  Request $request  Petición actual
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()
                    ? [
                        'id'    => $request->user()->id,
                        'name'  => $request->user()->name,
                        'email' => $request->user()->email,
                        'roles' => $request->user()->getRoleNames(),
                    ]
                    : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'sede' => [
                'nombre' => config('sede.nombre'),
                'codigo' => config('sede.codigo'),
            ],
        ];
    }
}
