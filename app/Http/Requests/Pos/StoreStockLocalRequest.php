<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockLocalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sede_id' => ['required', 'integer', 'exists:sedes,id'],
            'lote_local_id' => ['required', 'integer', 'exists:lotes_local,id'],
            'cantidad_disponible' => ['required', 'integer', 'min:0'],
        ];
    }
}
