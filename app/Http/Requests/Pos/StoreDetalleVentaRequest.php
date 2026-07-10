<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación para crear un detalle de venta.
 */
class StoreDetalleVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'venta_id' => ['required', 'integer', 'exists:ventas_fisicas,id'],
            'producto_sku' => ['required', 'string', 'exists:productos_local,sku'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['required', 'numeric', 'min:0'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ];
    }
}
