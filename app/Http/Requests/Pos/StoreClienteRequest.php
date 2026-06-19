<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dni'       => ['required', 'string', 'size:8', 'unique:clientes,dni'],
            'nombres'   => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'email'     => ['nullable', 'string', 'email', 'max:255'],
            'activo'    => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'dni.required'    => 'El DNI es obligatorio.',
            'dni.unique'      => 'Este DNI ya está registrado.',
            'dni.size'        => 'El DNI debe tener exactamente 8 dígitos.',
            'nombres.required' => 'Los nombres son obligatorios.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
        ];
    }
}
