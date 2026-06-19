<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\VentaFisica;
use Illuminate\Http\Request;

class VentaFisicaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $ventas = VentaFisica::with(['sede', 'empleado', 'metodoPago'])
            ->when($search, function ($query, $search) {
                $query->whereHas('sede', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                })->orWhereHas('empleado', function ($q) use ($search) {
                    $q->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%");
                });
            })
            ->orderBy('fecha_venta', 'desc')
            ->paginate(10);

        return inertia('Pos/Ventas/Index', [
            'ventas' => $ventas,
            'search' => $search,
        ]);
    }

    public function show(VentaFisica $venta)
    {
        $venta->load(['sede', 'empleado', 'metodoPago', 'detalles.producto']);

        return inertia('Pos/Ventas/Show', [
            'venta' => $venta,
        ]);
    }
}
