<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreEmpleadoRequest;
use App\Http\Requests\Pos\UpdateEmpleadoRequest;
use App\Models\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $empleados = Empleado::query()
            ->when($search, function ($query, $search) {
                $query->where('nombres', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%");
            })
            ->orderBy('apellidos')
            ->paginate(10);

        return inertia('Pos/Empleados/Index', [
            'empleados' => $empleados,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return inertia('Pos/Empleados/Create');
    }

    public function store(StoreEmpleadoRequest $request)
    {
        Empleado::create($request->validated());

        return redirect()->route('pos.empleados.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function edit(Empleado $empleado)
    {
        return inertia('Pos/Empleados/Edit', [
            'empleado' => $empleado,
        ]);
    }

    public function update(UpdateEmpleadoRequest $request, Empleado $empleado)
    {
        $empleado->update($request->validated());

        return redirect()->route('pos.empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $hasVentas = $empleado->ventasFisicas()->exists();

        if ($hasVentas) {
            return redirect()->route('pos.empleados.index')
                ->with('error', 'No se puede eliminar el empleado porque tiene ventas asociadas.');
        }

        $empleado->delete();

        return redirect()->route('pos.empleados.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }
}
