<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreMetodoPagoRequest;
use App\Http\Requests\Pos\UpdateMetodoPagoRequest;
use App\Models\MetodoPago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

/**
 * Controlador para la gestión de métodos de pago del POS.
 */
class MetodoPagoController extends Controller
{
    /**
     * Muestra el listado paginado de métodos de pago.
     *
     * @param  Request  $request  Parámetros de búsqueda
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $metodos = MetodoPago::query()
            ->when($search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10);

        return inertia('Pos/MetodosPago/Index', [
            'metodos' => $metodos,
            'search' => $search,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo método de pago.
     *
     * @return Response
     */
    public function create()
    {
        return inertia('Pos/MetodosPago/Create');
    }

    /**
     * Almacena un nuevo método de pago en la base de datos.
     *
     * @param  StoreMetodoPagoRequest  $request  Datos validados del método de pago
     * @return RedirectResponse
     */
    public function store(StoreMetodoPagoRequest $request)
    {
        MetodoPago::create($request->validated());

        return redirect()->route('pos.metodos-pago.index')
            ->with('success', 'Método de pago creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un método de pago existente.
     *
     * @param  MetodoPago  $metodoPago  Método de pago a editar
     * @return Response
     */
    public function edit(MetodoPago $metodoPago)
    {
        return inertia('Pos/MetodosPago/Edit', [
            'metodoPago' => $metodoPago,
        ]);
    }

    /**
     * Actualiza un método de pago existente en la base de datos.
     *
     * @param  UpdateMetodoPagoRequest  $request  Datos validados del método de pago
     * @param  MetodoPago  $metodoPago  Método de pago a actualizar
     * @return RedirectResponse
     */
    public function update(UpdateMetodoPagoRequest $request, MetodoPago $metodoPago)
    {
        $metodoPago->update($request->validated());

        return redirect()->route('pos.metodos-pago.index')
            ->with('success', 'Método de pago actualizado correctamente.');
    }

    /**
     * Elimina un método de pago si no tiene ventas asociadas.
     *
     * @param  MetodoPago  $metodoPago  Método de pago a eliminar
     * @return RedirectResponse
     */
    public function destroy(MetodoPago $metodoPago)
    {
        $hasVentas = $metodoPago->ventasFisicas()->exists();

        if ($hasVentas) {
            return redirect()->route('pos.metodos-pago.index')
                ->with('error', 'No se puede eliminar el método de pago porque tiene ventas asociadas.');
        }

        $metodoPago->delete();

        return redirect()->route('pos.metodos-pago.index')
            ->with('success', 'Método de pago eliminado correctamente.');
    }
}
