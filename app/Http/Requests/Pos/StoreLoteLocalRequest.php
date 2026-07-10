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
        // Si se envía un array de lotes (recepción múltiple)
        if ($this->has('lotes') && is_array($this->input('lotes'))) {
            return [
                'lotes' => ['required', 'array', 'min:1'],
                'lotes.*.sku_producto' => ['required', 'string', 'exists:productos_local,sku'],
                'lotes.*.numero_lote' => ['required', 'string', 'max:100'],
                'lotes.*.fecha_vencimiento' => ['required', 'date', 'after_or_equal:today'],
                'lotes.*.cantidad_disponible' => ['required', 'integer', 'min:0'],
            ];
        }

        // Compatibilidad con formulario simple (un solo lote)
        return [
            'sku_producto' => ['required', 'string', 'exists:productos_local,sku'],
            'numero_lote' => ['required', 'string', 'max:100'],
            'fecha_vencimiento' => ['required', 'date', 'after_or_equal:today'],
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
            'fecha_vencimiento.after_or_equal' => 'La fecha debe ser igual o posterior a hoy.',
            'cantidad_disponible.required' => 'La cantidad es obligatoria.',
            'cantidad_disponible.min' => 'La cantidad no puede ser negativa.',
            'lotes.required' => 'Debe agregar al menos un lote.',
            'lotes.*.sku_producto.required' => 'El producto es obligatorio en cada lote.',
            'lotes.*.sku_producto.exists' => 'El producto seleccionado no existe.',
            'lotes.*.numero_lote.required' => 'El número de lote es obligatorio.',
            'lotes.*.fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
            'lotes.*.fecha_vencimiento.after_or_equal' => 'La fecha debe ser igual o posterior a hoy.',
            'lotes.*.cantidad_disponible.required' => 'La cantidad es obligatoria.',
            'lotes.*.cantidad_disponible.min' => 'La cantidad no puede ser negativa.',
        ];
    }
}
