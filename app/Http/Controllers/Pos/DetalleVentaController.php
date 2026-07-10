<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Inertia\Response;

/**
 * Controlador para la consulta de detalles de ventas.
 */
class DetalleVentaController extends Controller
{
    /**
     * Muestra el listado paginado de detalles de venta.
     *
     * @param  Request  $request  Parámetros de búsqueda
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $detalles = DetalleVenta::with(['venta', 'producto'])
            ->when($search, function ($query, $search) {
                $query->whereHas('producto', function ($q) use ($search) {
                    $q->where('nombre_comercial', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })->orWhereHas('venta', function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return inertia('Pos/DetalleVentas/Index', [
            'detalles' => $detalles,
            'search' => $search,
        ]);
    }
}
