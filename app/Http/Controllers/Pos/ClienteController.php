<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreClienteRequest;
use App\Http\Requests\Pos\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function searchByDni(Request $request)
    {
        $dni = $request->get('dni');

        if (!$dni || strlen($dni) !== 8) {
            return response()->json(['cliente' => null]);
        }

        $cliente = Cliente::where('dni', $dni)->first();

        return response()->json(['cliente' => $cliente]);
    }

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
            'search'   => $search,
        ]);
    }

    public function create()
    {
        return inertia('Pos/Clientes/Create');
    }

    public function store(StoreClienteRequest $request)
    {
        Cliente::create($request->validated());

        return redirect()->route('pos.clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return inertia('Pos/Clientes/Edit', [
            'cliente' => $cliente,
        ]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return redirect()->route('pos.clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('pos.clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
