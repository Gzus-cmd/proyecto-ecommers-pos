<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLoteView extends Model
{
    protected $table = 'v_stock_lotes';

    public $timestamps = false;

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad_inicial' => 'integer',
        'cantidad_vendida' => 'integer',
        'stock_actual' => 'integer',
    ];

    public function producto()
    {
        return $this->belongsTo(ProductoLocal::class, 'sku_producto', 'sku');
    }

    public function lote()
    {
        return $this->belongsTo(LoteLocal::class, 'id', 'id');
    }
}
