<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\ProductoLocal;
use App\Models\Sede;
use App\Models\StockLocal;
use App\Models\VentaFisica;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProductos = ProductoLocal::count();
        $ventasHoy = VentaFisica::whereDate('created_at', today())->count();
        $sedesActivas = Sede::where('activo', true)->count();
        $stockBajo = StockLocal::where('cantidad_disponible', '<', 10)->count();

        return inertia('Pos/Dashboard/Index', [
            'totalProductos' => $totalProductos,
            'ventasHoy'      => $ventasHoy,
            'sedesActivas'   => $sedesActivas,
            'stockBajo'      => $stockBajo,
        ]);
    }
}
