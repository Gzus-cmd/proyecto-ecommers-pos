<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\LoteLocal;
use Illuminate\Http\Request;

/**
 * Controlador para la consulta del stock actual del inventario local.
 * Muestra el stock calculado (descontando ventas) de cada lote.
 */
class StockLocalController extends Controller
{
    /**
     * Muestra el listado paginado del stock actual de productos.
     *
     * @param  Request $request  Parámetros de búsqueda
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $lotes = LoteLocal::with('producto')
            ->when($search, function ($query, $search) {
                $query->whereHas('producto', function ($q) use ($search) {
                    $q->where('nombre_comercial', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('fecha_vencimiento')
            ->paginate(10);

        // Transform to include calculated stock
        $lotes->getCollection()->transform(function ($lote) {
            return [
                'id' => $lote->id,
                'sku_producto' => $lote->sku_producto,
                'numero_lote' => $lote->numero_lote,
                'fecha_vencimiento' => $lote->fecha_vencimiento?->format('Y-m-d'),
                'cantidad_inicial' => $lote->cantidad_disponible,
                'stock_actual' => $lote->stock_actual,
                'producto_nombre' => $lote->producto?->nombre_comercial ?? '-',
            ];
        });

        return inertia('Pos/Stock/Index', [
            'stock' => $lotes,
            'search' => $search,
        ]);
    }
}
