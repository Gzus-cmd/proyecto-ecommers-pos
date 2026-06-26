<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\LoteLocal;
use Illuminate\Http\Request;

class StockLocalController extends Controller
{
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
