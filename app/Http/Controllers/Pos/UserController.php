<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreUserRequest;
use App\Http\Requests\Pos\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\Permission\Models\Role;

/**
 * Controlador para la gestión de usuarios del sistema.
 * Incluye asignación de roles mediante Spatie Permission.
 */
class UserController extends Controller
{
    /**
     * Muestra el listado paginado de usuarios.
     *
     * @param  Request  $request  Parámetros de búsqueda
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $users = User::query()
            ->with('roles')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return inertia('Pos/Users/Index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     *
     * @return Response
     */
    public function create()
    {
        return inertia('Pos/Users/Create', [
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    /**
     * Almacena un nuevo usuario y le asigna un rol.
     *
     * @param  StoreUserRequest  $request  Datos validados del usuario
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        if ($request->filled('role')) {
            $user->assignRole($request->role);
        }

        return redirect()->route('pos.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     *
     * @param  User  $user  Usuario a editar
     * @return Response
     */
    public function edit(User $user)
    {
        $user->load('roles');

        return inertia('Pos/Users/Edit', [
            'user' => $user,
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    /**
     * Actualiza un usuario existente y sincroniza su rol.
     *
     * @param  UpdateUserRequest  $request  Datos validados del usuario
     * @param  User  $user  Usuario a actualizar
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('pos.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Desactiva un usuario (no lo elimina físicamente).
     *
     * @param  User  $user  Usuario a desactivar
     * @return RedirectResponse
     */
    public function destroy(User $user)
    {
        $user->update(['activo' => false]);

        return redirect()->route('pos.users.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }
}
