<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreLoteLocalRequest;
use App\Http\Requests\Pos\UpdateLoteLocalRequest;
use App\Models\LoteLocal;
use App\Models\ProductoLocal;
use Illuminate\Http\Request;

class LoteLocalController extends Controller
{
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

    public function create()
    {
        $productos = ProductoLocal::activos()->orderBy('nombre_comercial')->get();

        return inertia('Pos/Lotes/Create', [
            'productos' => $productos,
        ]);
    }

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

    public function edit(LoteLocal $lote)
    {
        $productos = ProductoLocal::activos()->orderBy('nombre_comercial')->get();

        return inertia('Pos/Lotes/Edit', [
            'lote' => $lote->load('producto'),
            'productos' => $productos,
        ]);
    }

    public function update(UpdateLoteLocalRequest $request, LoteLocal $lote)
    {
        $lote->update($request->validated());

        return redirect()->route('pos.lotes.index')
            ->with('success', 'Lote actualizado correctamente.');
    }

    public function show(LoteLocal $lote)
    {
        $lote->load(['producto', 'user']);

        return inertia('Pos/Lotes/Show', [
            'lote' => $lote,
        ]);
    }

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
