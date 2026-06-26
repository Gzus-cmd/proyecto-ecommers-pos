<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoteLocal extends Model
{
    /** @use HasFactory<\Database\Factories\LoteLocalFactory> */
    use HasFactory;

    protected $table = 'lotes_local';

    protected $fillable = [
        'sku_producto',
        'numero_lote',
        'fecha_vencimiento',
        'cantidad_disponible',
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
