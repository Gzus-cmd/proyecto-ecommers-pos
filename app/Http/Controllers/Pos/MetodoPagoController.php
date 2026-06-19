<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreMetodoPagoRequest;
use App\Http\Requests\Pos\UpdateMetodoPagoRequest;
use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $metodos = MetodoPago::query()
            ->when($search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10);

        return inertia('Pos/MetodosPago/Index', [
            'metodos' => $metodos,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return inertia('Pos/MetodosPago/Create');
    }

    public function store(StoreMetodoPagoRequest $request)
    {
        MetodoPago::create($request->validated());

        return redirect()->route('pos.metodos-pago.index')
            ->with('success', 'Método de pago creado correctamente.');
    }

    public function edit(MetodoPago $metodoPago)
    {
        return inertia('Pos/MetodosPago/Edit', [
            'metodoPago' => $metodoPago,
        ]);
    }

    public function update(UpdateMetodoPagoRequest $request, MetodoPago $metodoPago)
    {
        $metodoPago->update($request->validated());

        return redirect()->route('pos.metodos-pago.index')
            ->with('success', 'Método de pago actualizado correctamente.');
    }

    public function destroy(MetodoPago $metodoPago)
    {
        $hasVentas = $metodoPago->ventasFisicas()->exists();

        if ($hasVentas) {
            return redirect()->route('pos.metodos-pago.index')
                ->with('error', 'No se puede eliminar el método de pago porque tiene ventas asociadas.');
        }

        $metodoPago->delete();

        return redirect()->route('pos.metodos-pago.index')
            ->with('success', 'Método de pago eliminado correctamente.');
    }
}
