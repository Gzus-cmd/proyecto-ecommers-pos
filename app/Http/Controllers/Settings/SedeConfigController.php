<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSedeConfigRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * Controlador para la configuración de la sede activa.
 * Lee y escribe las variables de entorno SEDE_* en el archivo .env.
 */
class SedeConfigController extends Controller
{
    /**
     * Muestra el formulario de configuración de la sede.
     *
     * @param  Request $request  Petición actual
     * @return \Inertia\Response
     */
    public function edit(Request $request)
    {
        return inertia('Settings/SedeConfig', [
            'config' => [
                'nombre' => config('sede.nombre'),
                'codigo' => config('sede.codigo'),
                'direccion' => config('sede.direccion'),
                'telefono' => config('sede.telefono'),
            ],
        ]);
    }

    /**
     * Actualiza la configuración de la sede en el archivo .env.
     * También refresca la caché de configuración de Laravel.
     *
     * @param  UpdateSedeConfigRequest $request  Datos validados de la sede
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSedeConfigRequest $request)
    {
        $data = $request->validated();

        $envContent = file_get_contents(base_path('.env'));

        foreach ($data as $key => $value) {
            $key = 'SEDE_' . strtoupper(Str::snake($key));

            // Envolver en comillas si tiene espacios o caracteres especiales
            if (preg_match('/\s/', $value) || preg_match('/[^a-zA-Z0-9_\.\-]/', $value)) {
                $value = '"' . $value . '"';
            }

            // Reemplazar o agregar
            if (Str::contains($envContent, $key . '=')) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents(base_path('.env'), $envContent);

        // Re-cache config
        Artisan::call('config:cache');

        return redirect()->route('settings.sede.edit')
            ->with('success', 'Configuración de sede actualizada correctamente.');
    }
}
