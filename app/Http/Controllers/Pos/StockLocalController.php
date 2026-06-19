<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\StockLocal;
use Illuminate\Http\Request;

class StockLocalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $stock = StockLocal::with(['sede', 'loteLocal.producto'])
            ->when($search, function ($query, $search) {
                $query->whereHas('loteLocal.producto', function ($q) use ($search) {
                    $q->where('nombre_comercial', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })->orWhereHas('sede', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
            })
            ->orderBy('cantidad_disponible', 'desc')
            ->paginate(10);

        return inertia('Pos/Stock/Index', [
            'stock' => $stock,
            'search' => $search,
        ]);
    }
}
