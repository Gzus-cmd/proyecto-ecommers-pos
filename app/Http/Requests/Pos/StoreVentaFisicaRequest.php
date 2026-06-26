<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaFisicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'nuevo_cliente' => ['nullable', 'array'],
            'nuevo_cliente.dni' => ['required_with:nuevo_cliente', 'string', 'size:8', 'unique:clientes,dni'],
            'nuevo_cliente.nombres' => ['required_with:nuevo_cliente', 'string', 'max:255'],
            'nuevo_cliente.apellidos' => ['required_with:nuevo_cliente', 'string', 'max:255'],
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
            'nuevo_cliente.dni.required_with' => 'El DNI del nuevo cliente es obligatorio.',
            'nuevo_cliente.dni.size' => 'El DNI debe tener exactamente 8 dígitos.',
            'nuevo_cliente.dni.unique' => 'Este DNI ya está registrado. Selecciona el cliente existente.',
            'nuevo_cliente.nombres.required_with' => 'Los nombres del nuevo cliente son obligatorios.',
            'nuevo_cliente.apellidos.required_with' => 'Los apellidos del nuevo cliente son obligatorios.',
        ];
    }
}
