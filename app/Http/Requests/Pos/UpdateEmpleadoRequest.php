<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empleado = $this->route('empleado');
        $passwordRequired = fn () => $this->boolean('crear_usuario') && !$empleado?->user_id;

        return [
            'nombres'         => ['required', 'string', 'max:255'],
            'apellidos'       => ['required', 'string', 'max:255'],
            'dni' => [
                'required',
                'string',
                'size:8',
                Rule::unique('empleados', 'dni')->ignore($empleado),
            ],
            'cargo'           => ['nullable', 'string', 'max:255'],
            'activo'          => ['boolean'],
            'crear_usuario'   => ['boolean'],
            'name'            => ['required_if:crear_usuario,true', 'string', 'max:255'],
            'email'           => [
                Rule::requiredIf(fn () => $this->boolean('crear_usuario')),
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($empleado?->user_id),
            ],
            'password'        => [Rule::requiredIf($passwordRequired), 'string', 'min:8', 'nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required'     => 'Los nombres son obligatorios.',
            'apellidos.required'   => 'Los apellidos son obligatorios.',
            'dni.required'         => 'El DNI es obligatorio.',
            'dni.unique'           => 'Este DNI ya está registrado.',
            'dni.size'             => 'El DNI debe tener exactamente 8 dígitos.',
            'name.required_if'     => 'El nombre de usuario es obligatorio.',
            'email.required_if'    => 'El correo electrónico es obligatorio.',
            'email.unique'         => 'Este correo electrónico ya está registrado.',
            'password.required_if' => 'La contraseña es obligatoria.',
            'password.min'         => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
