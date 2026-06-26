<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreProductoLocalRequest;
use App\Http\Requests\Pos\UpdateProductoLocalRequest;
use App\Models\ProductoLocal;
use Illuminate\Http\Request;

class ProductoLocalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $productos = ProductoLocal::query()
            ->when($search, function ($query, $search) {
                $query->where('nombre_comercial', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('nombre_generico', 'like', "%{$search}%");
            })
            ->orderBy('nombre_comercial')
            ->paginate(10);

        return inertia('Pos/Productos/Index', [
            'productos' => $productos,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return inertia('Pos/Productos/Create');
    }

    public function store(StoreProductoLocalRequest $request)
    {
        ProductoLocal::create($request->validated());

        return redirect()->route('pos.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(ProductoLocal $producto)
    {
        return inertia('Pos/Productos/Edit', [
            'producto' => $producto,
        ]);
    }

    public function update(UpdateProductoLocalRequest $request, ProductoLocal $producto)
    {
        $producto->update($request->validated());

        return redirect()->route('pos.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function show(ProductoLocal $producto)
    {
        return inertia('Pos/Productos/Show', [
            'producto' => $producto,
        ]);
    }

    public function destroy(ProductoLocal $producto)
    {
        $hasLotes = $producto->lotes()->exists();
        $hasDetalles = $producto->detallesVenta()->exists();

        if ($hasLotes || $hasDetalles) {
            return redirect()->route('pos.productos.index')
                ->with('error', 'No se puede eliminar el producto porque tiene lotes o detalles de venta asociados.');
        }

        $producto->delete();

        return redirect()->route('pos.productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
