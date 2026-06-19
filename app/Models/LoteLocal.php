<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteLocal extends Model
{
    protected $table = 'lotes_local';

    protected $fillable = [
        'sku_producto',
        'numero_lote',
        'fecha_vencimiento',
        'cantidad_disponible',
    ];

    protected function casts(): array
    {
        return [
            'fecha_vencimiento' => 'date',
            'cantidad_disponible' => 'integer',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(ProductoLocal::class, 'sku_producto', 'sku');
    }

    public function stockLocal()
    {
        return $this->hasMany(StockLocal::class);
    }
}
