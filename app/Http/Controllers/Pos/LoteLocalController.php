<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreLoteLocalRequest;
use App\Http\Requests\Pos\UpdateLoteLocalRequest;
use App\Models\LoteLocal;
use App\Models\ProductoLocal;
use Illuminate\Http\Request;

/**
 * Controlador para la gestión de lotes del inventario local.
 */
class LoteLocalController extends Controller
{
    /**
     * Muestra el listado paginado de lotes.
     *
     * @param  Request $request  Parámetros de búsqueda
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $lotes = LoteLocal::with(['producto', 'user'])
            ->when($search, function ($query, $search) {
                $query->where('numero_lote', 'like', "%{$search}%")
                    ->orWhere('sku_producto', 'like', "%{$search}%");
            })
            ->orderBy('fecha_vencimiento')
            ->paginate(10);

        return inertia('Pos/Lotes/Index', [
            'lotes' => $lotes,
            'search' => $search,
        ]);
    }

    /**
     * Muestra el formulario para crear uno o múltiples lotes.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $productos = ProductoLocal::activos()->orderBy('nombre_comercial')->get();

        return inertia('Pos/Lotes/Create', [
            'productos' => $productos,
        ]);
    }

    /**
     * Almacena uno o múltiples lotes en la base de datos.
     *
     * @param  StoreLoteLocalRequest $request  Datos validados de los lotes
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreLoteLocalRequest $request)
    {
        $data = $request->validated();

        // Si se envía un array de lotes (recepción múltiple)
        if (isset($data['lotes']) && is_array($data['lotes'])) {
            \DB::transaction(function () use ($data) {
                foreach ($data['lotes'] as $loteData) {
                    $loteData['user_id'] = auth()->id();
                    LoteLocal::create($loteData);
                }
            });

            $cantidad = count($data['lotes']);
            return redirect()->route('pos.lotes.index')
                ->with('success', "{$cantidad} lotes creados correctamente.");
        }

        // Compatibilidad con formulario simple (un solo lote)
        $data['user_id'] = auth()->id();
        LoteLocal::create($data);

        return redirect()->route('pos.lotes.index')
            ->with('success', 'Lote creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un lote existente.
     *
     * @param  LoteLocal $lote  Lote a editar
     * @return \Inertia\Response
     */
    public function edit(LoteLocal $lote)
    {
        $productos = ProductoLocal::activos()->orderBy('nombre_comercial')->get();

        return inertia('Pos/Lotes/Edit', [
            'lote' => $lote->load('producto'),
            'productos' => $productos,
        ]);
    }

    /**
     * Actualiza un lote existente en la base de datos.
     *
     * @param  UpdateLoteLocalRequest $request  Datos validados del lote
     * @param  LoteLocal              $lote     Lote a actualizar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateLoteLocalRequest $request, LoteLocal $lote)
    {
        $lote->update($request->validated());

        return redirect()->route('pos.lotes.index')
            ->with('success', 'Lote actualizado correctamente.');
    }

    /**
     * Muestra los detalles de un lote específico.
     *
     * @param  LoteLocal $lote  Lote a mostrar
     * @return \Inertia\Response
     */
    public function show(LoteLocal $lote)
    {
        $lote->load(['producto', 'user']);

        return inertia('Pos/Lotes/Show', [
            'lote' => $lote,
        ]);
    }

    /**
     * Retira el stock de un lote (establece cantidad_disponible en 0).
     *
     * @param  LoteLocal $lote  Lote del cual retirar stock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function retirar(LoteLocal $lote)
    {
        $lote->update(['cantidad_disponible' => 0]);

        return redirect()->route('pos.stock.index')
            ->with('success', 'Stock retirado correctamente.');
    }

    /**
     * Elimina un lote si no tiene stock asociado.
     *
     * @param  LoteLocal $lote  Lote a eliminar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LoteLocal $lote)
    {
        $hasStock = $lote->stockLocal()->exists();

        if ($hasStock) {
            return redirect()->route('pos.lotes.index')
                ->with('error', 'No se puede eliminar el lote porque tiene stock asociado.');
        }

        $lote->delete();

        return redirect()->route('pos.lotes.index')
            ->with('success', 'Lote eliminado correctamente.');
    }
}
