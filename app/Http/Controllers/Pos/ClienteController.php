<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreClienteRequest;
use App\Http\Requests\Pos\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

/**
 * Controlador para la gestión de clientes del POS.
 */
class ClienteController extends Controller
{
    /**
     * Busca un cliente por su número de DNI.
     *
     * @param  Request  $request  Contiene el DNI a buscar
     * @return JsonResponse
     */
    public function searchByDni(Request $request)
    {
        $dni = $request->get('dni');

        if (! $dni || strlen($dni) !== 8) {
            return response()->json(['cliente' => null]);
        }

        $cliente = Cliente::where('dni', $dni)->first();

        return response()->json(['cliente' => $cliente]);
    }

    /**
     * Muestra el listado paginado de clientes.
     *
     * @param  Request  $request  Parámetros de búsqueda
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $clientes = Cliente::query()
            ->when($search, function ($query, $search) {
                $query->where('nombres', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%");
            })
            ->orderBy('apellidos')
            ->paginate(10);

        return inertia('Pos/Clientes/Index', [
            'clientes' => $clientes,
            'search' => $search,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo cliente.
     *
     * @return Response
     */
    public function create()
    {
        return inertia('Pos/Clientes/Create');
    }

    /**
     * Almacena un nuevo cliente en la base de datos.
     * Soporta tanto respuestas JSON como redirección Inertia.
     *
     * @param  StoreClienteRequest  $request  Datos validados del cliente
     * @return JsonResponse|RedirectResponse
     */
    public function store(StoreClienteRequest $request)
    {
        $cliente = Cliente::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json($cliente, 201);
        }

        return redirect()->route('pos.clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un cliente existente.
     *
     * @param  Cliente  $cliente  Cliente a editar
     * @return Response
     */
    public function edit(Cliente $cliente)
    {
        return inertia('Pos/Clientes/Edit', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Actualiza un cliente existente en la base de datos.
     *
     * @param  UpdateClienteRequest  $request  Datos validados del cliente
     * @param  Cliente  $cliente  Cliente a actualizar
     * @return RedirectResponse
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return redirect()->route('pos.clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Elimina un cliente de la base de datos.
     *
     * @param  Cliente  $cliente  Cliente a eliminar
     * @return RedirectResponse
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('pos.clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
