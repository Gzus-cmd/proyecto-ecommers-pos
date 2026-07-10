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

/**
 * Controlador para la gestión de ventas físicas (transacciones en punto de venta).
 */
class VentaFisicaController extends Controller
{
    /**
     * Muestra el listado paginado de ventas.
     *
     * @param  Request $request  Parámetros de búsqueda
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $ventas = VentaFisica::with(['user', 'metodoPago'])
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
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

    /**
     * Muestra el formulario para registrar una nueva venta.
     * Carga productos activos, métodos de pago, clientes y lotes disponibles.
     *
     * @param  Request $request
     * @return \Inertia\Response
     */
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

    /**
     * Registra una nueva venta y sus detalles en una transacción.
     * Soporta la creación inline de clientes por DNI.
     *
     * @param  StoreVentaFisicaRequest $request  Datos validados de la venta y sus detalles
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreVentaFisicaRequest $request)
    {
        $sedeId = auth()->user()->sede_id ?? 1;

        DB::transaction(function () use ($request, $sedeId) {
            // Crear cliente inline si se envió nuevo_cliente_dni
            $clienteId = $request->cliente_id;
            if ($request->filled('nuevo_cliente_dni')) {
                $dni = $request->nuevo_cliente_dni;
                // Buscar si ya existe por DNI
                $clienteExistente = Cliente::where('dni', $dni)->first();
                if ($clienteExistente) {
                    $clienteId = $clienteExistente->id;
                } else {
                    $cliente = Cliente::create([
                        'dni' => $dni,
                        'nombres' => '',
                        'apellidos' => '',
                    ]);
                    $clienteId = $cliente->id;
                }
            }

            $venta = VentaFisica::create([
                'sede_id' => $sedeId,
                'user_id' => auth()->id(),
                'cliente_id' => $clienteId,
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

    /**
     * Exporta las ventas en formato CSV, con filtro opcional por fechas.
     *
     * @param  Request $request  Filtros de fecha_inicio y fecha_fin
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportar(Request $request)
    {
        $ventas = VentaFisica::with(['cliente', 'user', 'detalles.producto'])
            ->when($request->filled('fecha_inicio'), fn($q) => $q->whereDate('created_at', '>=', $request->fecha_inicio))
            ->when($request->filled('fecha_fin'), fn($q) => $q->whereDate('created_at', '<=', $request->fecha_fin))
            ->orderBy('created_at', 'desc')
            ->get();

        $csv = "ID,Venta,Fecha,Cliente,Usuario,Subtotal,Impuesto,Total,Productos\n";
        foreach ($ventas as $v) {
            $csv .= "{$v->id},Venta #{$v->id}," . $v->created_at->format('Y-m-d') . ",";
            $csv .= "{$v->cliente?->dni} {$v->cliente?->nombres} {$v->cliente?->apellidos},";
            $csv .= "{$v->user->name},";
            $csv .= number_format($v->subtotal, 2) . "," . number_format($v->impuesto, 2) . "," . number_format($v->total, 2) . ",";
            $csv .= "\"{$v->detalles->map(fn($d) => $d->producto?->nombre_comercial . ' x' . $d->cantidad)->implode(', ')}\"";
            $csv .= "\n";
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'ventas-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Muestra los detalles de una venta específica.
     *
     * @param  VentaFisica $venta  Venta a mostrar
     * @return \Inertia\Response
     */
    public function show(VentaFisica $venta)
    {
        $venta->load(['user', 'metodoPago', 'detalles.producto', 'cliente']);

        return inertia('Pos/Ventas/Show', [
            'venta' => $venta,
        ]);
    }
}
