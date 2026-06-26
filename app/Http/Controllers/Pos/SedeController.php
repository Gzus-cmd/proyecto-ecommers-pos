<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreSedeRequest;
use App\Http\Requests\Pos\UpdateSedeRequest;
use App\Models\Sede;
use Illuminate\Http\Request;

class SedeController extends Controller
{
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

    public function create()
    {
        return inertia('Pos/Sedes/Create');
    }

    public function store(StoreSedeRequest $request)
    {
        Sede::create($request->validated());

        return redirect()->route('pos.sedes.index')
            ->with('success', 'Sede creada correctamente.');
    }

    public function edit(Sede $sede)
    {
        return inertia('Pos/Sedes/Edit', [
            'sede' => $sede,
        ]);
    }

    public function update(UpdateSedeRequest $request, Sede $sede)
    {
        $sede->update($request->validated());

        return redirect()->route('pos.sedes.index')
            ->with('success', 'Sede actualizada correctamente.');
    }

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
