<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\DetalleVenta;
use App\Models\LoteLocal;
use App\Models\ProductoLocal;
use App\Models\VentaFisica;
use Illuminate\Http\Request;
use Inertia\Response;

/**
 * Controlador del dashboard principal del POS.
 * Proporciona métricas en tiempo real: ventas del día, stock bajo,
 * productos por vencer, top ventas y gráficos por día.
 */
class DashboardController extends Controller
{
    /**
     * Muestra el dashboard con las métricas del sistema.
     *
     * @param  Request  $request  Filtros opcionales de fecha_inicio y fecha_fin
     * @return Response
     */
    public function index(Request $request)
    {
        $totalProductos = ProductoLocal::count();
        $ventasHoy = VentaFisica::whereDate('created_at', today())->count();
        $ventasHoyMonto = (float) VentaFisica::whereDate('created_at', today())->sum('total');

        // ── Filtro de fechas ──────────────────────────────────────────
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(29)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $inicio = $fechaInicio.' 00:00:00';
        $fin = $fechaFin.' 23:59:59';

        // ── Stock bajo (stock_actual < 10) + agotados (stock_actual = 0) ──
        $lotes = LoteLocal::with('producto')->get();
        $stockBajo = 0;
        $stockBajoProductos = collect();
        $stockAgotado = 0;
        $stockAgotadoProductos = collect();

        foreach ($lotes as $lote) {
            $stockActual = $lote->stock_actual;
            if ($stockActual === 0) {
                $stockAgotado++;
                $stockAgotadoProductos->push([
                    'producto' => $lote->producto?->nombre_comercial ?? '-',
                    'sku' => $lote->sku_producto,
                    'cantidad' => $stockActual,
                    'lote' => $lote->numero_lote,
                ]);
            } elseif ($stockActual > 0 && $stockActual < 10) {
                $stockBajo++;
                $stockBajoProductos->push([
                    'producto' => $lote->producto?->nombre_comercial ?? '-',
                    'sku' => $lote->sku_producto,
                    'cantidad' => $stockActual,
                    'lote' => $lote->numero_lote,
                ]);
            }
        }

        // ── Productos por vencer (<= 90 días) desde lotes_local ────────
        $fechaLimite = now()->addDays(90);
        $productosPorVencer = LoteLocal::with('producto')
            ->where('fecha_vencimiento', '<=', $fechaLimite)
            ->where('cantidad_disponible', '>', 0)
            ->orderBy('fecha_vencimiento')
            ->get()
            ->map(function ($lote) {
                $diasRestantes = now()->diffInDays($lote->fecha_vencimiento, false);

                return [
                    'sku' => $lote->sku_producto,
                    'nombre_comercial' => $lote->producto?->nombre_comercial ?? '-',
                    'fecha_vencimiento' => $lote->fecha_vencimiento->format('Y-m-d'),
                    'dias_restantes' => (int) $diasRestantes,
                    'lote' => $lote->numero_lote,
                ];
            });

        $productosPorVencerCount = $productosPorVencer->count();

        // ── Ventas por día (según rango de fechas) ────────────────────
        $ventasPorDia = VentaFisica::query()
            ->whereBetween('created_at', [$inicio, $fin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total, SUM(total) as monto')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->keyBy('fecha');

        // Rellenar todos los días del rango con 0
        $dias = collect();
        $diferenciaDias = now()->parse($fechaInicio)->diffInDays(now()->parse($fechaFin));
        for ($i = $diferenciaDias; $i >= 0; $i--) {
            $fecha = now()->parse($fechaFin)->subDays($i)->format('Y-m-d');
            $dia = $ventasPorDia->get($fecha);
            $dias->push([
                'fecha' => $fecha,
                'total' => $dia ? (int) $dia->total : 0,
                'monto' => $dia ? (float) $dia->monto : 0,
            ]);
        }

        // ── Top 10 productos más vendidos en el rango ────────────────
        $topProductos = DetalleVenta::query()
            ->join('ventas_fisicas', 'detalle_ventas.venta_id', '=', 'ventas_fisicas.id')
            ->whereBetween('ventas_fisicas.created_at', [$inicio, $fin])
            ->selectRaw('producto_sku, SUM(cantidad) as total_vendido, SUM(detalle_ventas.subtotal) as total_monto')
            ->groupBy('producto_sku')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $producto = ProductoLocal::find($item->producto_sku);

                return [
                    'sku' => $item->producto_sku,
                    'nombre_comercial' => $producto?->nombre_comercial ?? '-',
                    'total_vendido' => (int) $item->total_vendido,
                    'total_monto' => (float) $item->total_monto,
                ];
            });

        // ── Productos por estado ──────────────────────────────────────
        $activos = ProductoLocal::where('activo', true)->count();
        $inactivos = ProductoLocal::where('activo', false)->count();

        return inertia('Pos/Dashboard/Index', [
            'totalProductos' => $totalProductos,
            'ventasHoy' => $ventasHoy,
            'ventasHoyMonto' => $ventasHoyMonto,
            'stockBajo' => $stockBajo,
            'stockBajoProductos' => $stockBajoProductos,
            'stockAgotado' => $stockAgotado,
            'stockAgotadoProductos' => $stockAgotadoProductos,
            'productosPorVencer' => $productosPorVencer,
            'productosPorVencerCount' => $productosPorVencerCount,
            'ventasPorDia' => $dias,
            'productosPorEstado' => [
                'activos' => $activos,
                'inactivos' => $inactivos,
            ],
            'topProductos' => $topProductos,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ]);
    }
}
