<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación para registrar una nueva venta física con sus detalles.
 */
class StoreVentaFisicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['nullable', 'integer', function ($attribute, $value, $fail) {
                if ($value && $value > 0 && !\App\Models\Cliente::where('id', $value)->exists()) {
                    $fail('El cliente seleccionado no existe.');
                }
            }],
            'nuevo_cliente_dni' => ['nullable', 'string', 'size:8'],
            'metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'impuesto' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_sku' => ['required', 'string', 'exists:productos_local,sku'],
            'detalles.*.lote_local_id' => ['required', 'integer', 'exists:lotes_local,id'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'detalles.*.subtotal' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'metodo_pago_id.required' => 'El método de pago es obligatorio.',
            'detalles.required' => 'Debe agregar al menos un producto.',
            'detalles.min' => 'Debe agregar al menos un producto.',
            'detalles.*.producto_sku.required' => 'El producto es obligatorio.',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}
