<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreSedeRequest;
use App\Http\Requests\Pos\UpdateSedeRequest;
use App\Models\Sede;
use Illuminate\Http\Request;

/**
 * Controlador para la gestión de sedes/sucursales del POS.
 */
class SedeController extends Controller
{
    /**
     * Muestra el listado paginado de sedes.
     *
     * @param  Request $request  Parámetros de búsqueda
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sedes = Sede::query()
            ->when($search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10);

        return inertia('Pos/Sedes/Index', [
            'sedes' => $sedes,
            'search' => $search,
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva sede.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('Pos/Sedes/Create');
    }

    /**
     * Almacena una nueva sede en la base de datos.
     *
     * @param  StoreSedeRequest $request  Datos validados de la sede
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSedeRequest $request)
    {
        Sede::create($request->validated());

        return redirect()->route('pos.sedes.index')
            ->with('success', 'Sede creada correctamente.');
    }

    /**
     * Muestra el formulario para editar una sede existente.
     *
     * @param  Sede $sede  Sede a editar
     * @return \Inertia\Response
     */
    public function edit(Sede $sede)
    {
        return inertia('Pos/Sedes/Edit', [
            'sede' => $sede,
        ]);
    }

    /**
     * Actualiza una sede existente en la base de datos.
     *
     * @param  UpdateSedeRequest $request  Datos validados de la sede
     * @param  Sede              $sede     Sede a actualizar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSedeRequest $request, Sede $sede)
    {
        $sede->update($request->validated());

        return redirect()->route('pos.sedes.index')
            ->with('success', 'Sede actualizada correctamente.');
    }

    /**
     * Elimina (soft delete / hard delete) una sede si no tiene ventas asociadas.
     *
     * @param  Sede $sede  Sede a eliminar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Sede $sede)
    {
        $hasVentas = $sede->ventasFisicas()->exists();

        if ($hasVentas) {
            return redirect()->route('pos.sedes.index')
                ->with('error', 'No se puede eliminar la sede porque tiene ventas asociadas.');
        }

        $sede->delete();

        return redirect()->route('pos.sedes.index')
            ->with('success', 'Sede eliminada correctamente.');
    }
}
