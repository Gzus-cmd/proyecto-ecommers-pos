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
        $ventas = VentaFisica::with(['sede', 'user', 'metodoPago'])
            ->when($search, function ($query, $search) {
                $query->whereHas('sede', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
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
        $venta->load(['sede', 'user', 'metodoPago', 'detalles.producto']);

        return inertia('Pos/Ventas/Show', [
            'venta' => $venta,
        ]);
    }
}
