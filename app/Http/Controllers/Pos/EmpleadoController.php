<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreEmpleadoRequest;
use App\Http\Requests\Pos\UpdateEmpleadoRequest;
use App\Models\Empleado;
use App\Models\User;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $empleados = Empleado::query()
            ->with('user')
            ->when($search, function ($query, $search) {
                $query->where('nombres', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%");
            })
            ->orderBy('apellidos')
            ->paginate(10);

        return inertia('Pos/Empleados/Index', [
            'empleados' => $empleados,
            'search'    => $search,
        ]);
    }

    public function create()
    {
        return inertia('Pos/Empleados/Create');
    }

    public function store(StoreEmpleadoRequest $request)
    {
        $data = $request->validated();

        if ($request->boolean('crear_usuario')) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);
            $data['user_id'] = $user->id;
        }

        Empleado::create($data);

        return redirect()->route('pos.empleados.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function edit(Empleado $empleado)
    {
        $empleado->load('user');

        return inertia('Pos/Empleados/Edit', [
            'empleado' => $empleado,
        ]);
    }

    public function update(UpdateEmpleadoRequest $request, Empleado $empleado)
    {
        $data = $request->validated();

        if ($request->boolean('crear_usuario')) {
            $userData = [
                'name'     => $data['name'],
                'email'    => $data['email'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = $data['password'];
            }

            if ($empleado->user_id) {
                $empleado->user->update($userData);
            } else {
                if (empty($data['password'])) {
                    $userData['password'] = str()->random(16);
                }
                $user = User::create($userData);
                $data['user_id'] = $user->id;
            }
        }

        $empleado->update($data);

        return redirect()->route('pos.empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $hasVentas = $empleado->ventasFisicas()->exists();

        if ($hasVentas) {
            return redirect()->route('pos.empleados.index')
                ->with('error', 'No se puede eliminar el empleado porque tiene ventas asociadas.');
        }

        $empleado->delete();

        return redirect()->route('pos.empleados.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }
}
