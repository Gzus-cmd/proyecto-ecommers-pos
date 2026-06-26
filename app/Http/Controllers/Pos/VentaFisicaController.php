<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreVentaFisicaRequest;
use App\Models\Cliente;
use App\Models\LoteLocal;
use App\Models\MetodoPago;
use App\Models\ProductoLocal;
use App\Models\VentaFisica;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function create(Request $request)
    {
        $productos = ProductoLocal::activos()->orderBy('nombre_comercial')->get();
        $metodosPago = MetodoPago::activos()->orderBy('nombre')->get();
        $clientes = Cliente::orderBy('apellidos')->get();
        $lotes = LoteLocal::with('producto')
            ->orderBy('fecha_vencimiento')
            ->get();

        return inertia('Pos/Ventas/Create', [
            'productos' => $productos,
            'metodosPago' => $metodosPago,
            'clientes' => $clientes,
            'lotes' => $lotes,
        ]);
    }

    public function store(StoreVentaFisicaRequest $request)
    {
        $sedeId = auth()->user()->sede_id ?? 1;

        DB::transaction(function () use ($request, $sedeId) {
            $venta = VentaFisica::create([
                'sede_id' => $sedeId,
                'user_id' => auth()->id(),
                'cliente_id' => $request->cliente_id,
                'fecha_venta' => now(),
                'subtotal' => $request->subtotal,
                'impuesto' => $request->impuesto,
                'total' => $request->total,
                'metodo_pago_id' => $request->metodo_pago_id,
            ]);

            foreach ($request->detalles as $detalle) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_sku' => $detalle['producto_sku'],
                    'lote_local_id' => $detalle['lote_local_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['subtotal'],
                ]);
            }
        });

        return redirect()->route('pos.ventas.index')
            ->with('success', 'Venta registrada correctamente.');
    }

    public function show(VentaFisica $venta)
    {
        $venta->load(['sede', 'user', 'metodoPago', 'detalles.producto', 'cliente']);

        return inertia('Pos/Ventas/Show', [
            'venta' => $venta,
        ]);
    }
}
