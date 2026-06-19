<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoLocalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:50', 'unique:productos_local,sku'],
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'nombre_generico' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'concentracion' => ['nullable', 'string', 'max:255'],
            'forma_farmaceutica' => ['nullable', 'string', 'max:255'],
            'requiere_receta' => ['boolean'],
            'precio_venta' => ['required', 'numeric', 'min:0.01'],
            'activo' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique' => 'Este SKU ya está registrado.',
            'nombre_comercial.required' => 'El nombre comercial es obligatorio.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.min' => 'El precio debe ser mayor a 0.',
        ];
    }
}
