<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoteLocalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku_producto' => ['required', 'string', 'exists:productos_local,sku'],
            'numero_lote' => ['required', 'string', 'max:100'],
            'fecha_vencimiento' => ['required', 'date', 'after:today'],
            'cantidad_disponible' => ['required', 'integer', 'min:0'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku_producto.required' => 'El producto es obligatorio.',
            'sku_producto.exists' => 'El producto seleccionado no existe.',
            'numero_lote.required' => 'El número de lote es obligatorio.',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
            'fecha_vencimiento.after' => 'La fecha debe ser posterior a hoy.',
            'cantidad_disponible.required' => 'La cantidad es obligatoria.',
            'cantidad_disponible.min' => 'La cantidad no puede ser negativa.',
        ];
    }
}
