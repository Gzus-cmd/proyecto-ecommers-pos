<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\ProductoLocal;
use App\Models\Sede;
use App\Models\StockLocal;
use App\Models\VentaFisica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProductos = ProductoLocal::count();
        $ventasHoy = VentaFisica::whereDate('created_at', today())->count();
        $sedesActivas = Sede::where('activo', true)->count();
        $stockBajo = StockLocal::where('cantidad_disponible', '<', 10)->count();

        // Ventas por día (últimos 7 días)
        $ventasPorDia = VentaFisica::query()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total, SUM(total) as monto')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->keyBy('fecha');

        // Asegurar que los 7 días tengan datos (rellenar con 0)
        $dias = collect();
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $dia = $ventasPorDia->get($fecha);
            $dias->push([
                'fecha' => $fecha,
                'total' => $dia ? (int) $dia->total : 0,
                'monto' => $dia ? (float) $dia->monto : 0,
            ]);
        }

        // Productos por estado
        $activos = ProductoLocal::where('activo', true)->count();
        $inactivos = ProductoLocal::where('activo', false)->count();

        return inertia('Pos/Dashboard/Index', [
            'totalProductos'  => $totalProductos,
            'ventasHoy'       => $ventasHoy,
            'sedesActivas'    => $sedesActivas,
            'stockBajo'       => $stockBajo,
            'ventasPorDia'    => $dias,
            'productosPorEstado' => [
                'activos'   => $activos,
                'inactivos' => $inactivos,
            ],
        ]);
    }
}
