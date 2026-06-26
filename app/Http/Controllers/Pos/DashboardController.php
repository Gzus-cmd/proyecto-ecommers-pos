<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\LoteLocal;
use App\Models\ProductoLocal;
use App\Models\StockLocal;
use App\Models\VentaFisica;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProductos = ProductoLocal::count();
        $ventasHoy = VentaFisica::whereDate('created_at', today())->count();
        $ventasHoyMonto = VentaFisica::whereDate('created_at', today())->sum('total');
        $stockBajo = StockLocal::where('cantidad_disponible', '<', 10)->count();

        // Productos por vencer (<= 30 días)
        $fechaLimite = now()->addDays(30);
        $productosPorVencer = ProductoLocal::where('fecha_vencimiento', '<=', $fechaLimite)
            ->whereNotNull('fecha_vencimiento')
            ->orderBy('fecha_vencimiento')
            ->get()
            ->map(function ($producto) {
                $diasRestantes = now()->diffInDays($producto->fecha_vencimiento, false);
                return [
                    'sku' => $producto->sku,
                    'nombre_comercial' => $producto->nombre_comercial,
                    'fecha_vencimiento' => $producto->fecha_vencimiento->format('Y-m-d'),
                    'dias_restantes' => (int) $diasRestantes,
                ];
            });

        $productosPorVencerCount = $productosPorVencer->count();

        // Stock bajo (<= 10 unidades)
        $stockBajoProductos = StockLocal::with(['loteLocal.producto', 'sede'])
            ->where('cantidad_disponible', '<', 10)
            ->where('cantidad_disponible', '>', 0)
            ->orderBy('cantidad_disponible')
            ->limit(50)
            ->get()
            ->map(function ($stock) {
                return [
                    'producto' => $stock->loteLocal?->producto?->nombre_comercial ?? '-',
                    'sku' => $stock->loteLocal?->sku_producto ?? '-',
                    'cantidad' => $stock->cantidad_disponible,
                    'lote' => $stock->loteLocal?->numero_lote ?? '-',
                    'sede' => $stock->sede?->nombre ?? '-',
                ];
            });

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
            'ventasHoyMonto'  => $ventasHoyMonto,
            'stockBajo'       => $stockBajo,
            'stockBajoProductos' => $stockBajoProductos,
            'productosPorVencer' => $productosPorVencer,
            'productosPorVencerCount' => $productosPorVencerCount,
            'ventasPorDia'    => $dias,
            'productosPorEstado' => [
                'activos'   => $activos,
                'inactivos' => $inactivos,
            ],
        ]);
    }
}
