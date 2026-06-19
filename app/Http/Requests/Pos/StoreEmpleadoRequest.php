<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres'         => ['required', 'string', 'max:255'],
            'apellidos'       => ['required', 'string', 'max:255'],
            'dni'             => ['required', 'string', 'size:8', 'unique:empleados,dni'],
            'cargo'           => ['nullable', 'string', 'max:255'],
            'activo'          => ['boolean'],
            'crear_usuario'   => ['boolean'],
            'name'            => ['required_if:crear_usuario,true', 'string', 'max:255'],
            'email'           => ['required_if:crear_usuario,true', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'        => ['required_if:crear_usuario,true', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required'    => 'Los nombres son obligatorios.',
            'apellidos.required'  => 'Los apellidos son obligatorios.',
            'dni.required'        => 'El DNI es obligatorio.',
            'dni.unique'          => 'Este DNI ya está registrado.',
            'dni.size'            => 'El DNI debe tener exactamente 8 dígitos.',
            'name.required_if'    => 'El nombre de usuario es obligatorio.',
            'email.required_if'   => 'El correo electrónico es obligatorio.',
            'email.unique'        => 'Este correo electrónico ya está registrado.',
            'password.required_if' => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
