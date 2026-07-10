<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreProductoLocalRequest;
use App\Http\Requests\Pos\UpdateProductoLocalRequest;
use App\Models\ProductoLocal;
use Illuminate\Http\Request;

/**
 * Controlador para la gestión de productos del inventario local.
 */
class ProductoLocalController extends Controller
{
    /**
     * Muestra el listado paginado de productos.
     *
     * @param  Request $request  Parámetros de búsqueda
     * @return \Inertia\Response
     */
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

    /**
     * Muestra el formulario para crear un nuevo producto.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('Pos/Productos/Create');
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     *
     * @param  StoreProductoLocalRequest $request  Datos validados del producto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductoLocalRequest $request)
    {
        ProductoLocal::create($request->validated());

        return redirect()->route('pos.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un producto existente.
     *
     * @param  ProductoLocal $producto  Producto a editar
     * @return \Inertia\Response
     */
    public function edit(ProductoLocal $producto)
    {
        return inertia('Pos/Productos/Edit', [
            'producto' => $producto,
        ]);
    }

    /**
     * Actualiza un producto existente en la base de datos.
     *
     * @param  UpdateProductoLocalRequest $request   Datos validados del producto
     * @param  ProductoLocal              $producto  Producto a actualizar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductoLocalRequest $request, ProductoLocal $producto)
    {
        $producto->update($request->validated());

        return redirect()->route('pos.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Muestra los detalles de un producto específico.
     *
     * @param  ProductoLocal $producto  Producto a mostrar
     * @return \Inertia\Response
     */
    public function show(ProductoLocal $producto)
    {
        return inertia('Pos/Productos/Show', [
            'producto' => $producto,
        ]);
    }

    /**
     * Elimina un producto si no tiene lotes o detalles de venta asociados.
     *
     * @param  ProductoLocal $producto  Producto a eliminar
     * @return \Illuminate\Http\RedirectResponse
     */
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
