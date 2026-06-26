<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreUserRequest;
use App\Http\Requests\Pos\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
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
            'users'  => $users,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return inertia('Pos/Users/Create', [
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        if ($request->filled('role')) {
            $user->assignRole($request->role);
        }

        return redirect()->route('pos.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $user->load('roles');

        return inertia('Pos/Users/Edit', [
            'user' => $user,
            'roles' => Role::all(['id', 'name']),
        ]);
    }

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

    public function destroy(User $user)
    {
        $user->update(['activo' => false]);

        return redirect()->route('pos.users.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }
}
