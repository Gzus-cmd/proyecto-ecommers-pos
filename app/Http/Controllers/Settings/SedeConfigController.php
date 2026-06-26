<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSedeConfigRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SedeConfigController extends Controller
{
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

    public function update(UpdateSedeConfigRequest $request)
    {
        $validated = $request->validated();

        $this->setEnv('SEDE_NOMBRE', $validated['nombre']);
        $this->setEnv('SEDE_CODIGO', $validated['codigo']);
        $this->setEnv('SEDE_DIRECCION', $validated['direccion'] ?? '');
        $this->setEnv('SEDE_TELEFONO', $validated['telefono'] ?? '');

        // Re-cache config
        Artisan::call('config:cache');

        return redirect()->route('settings.sede.edit')
            ->with('success', 'Configuración de sede actualizada correctamente.');
    }

    private function setEnv(string $key, string $value): void
    {
        $path = app()->environmentFilePath();
        $content = file_get_contents($path);

        $escaped = preg_quote($value, '/');
        $pattern = "/^{$key}=.*/m";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}";
        }

        file_put_contents($path, $content);
    }
}
