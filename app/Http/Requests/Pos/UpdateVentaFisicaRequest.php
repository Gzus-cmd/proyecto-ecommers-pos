<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVentaFisicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sede_id' => ['required', 'integer', 'exists:sedes,id'],
            'empleado_id' => ['required', 'integer', 'exists:empleados,id'],
            'fecha_venta' => ['nullable', 'date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'impuesto' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
        ];
    }
}
