<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoteLocal extends Model
{
    /** @use HasFactory<\Database\Factories\LoteLocalFactory> */
    use HasFactory;

    protected $table = 'lotes_local';

    protected $appends = ['stock_actual'];

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

    /** Stock actual calculado: cantidad_disponible - SUM(detalle_ventas.cantidad) */
    public function getStockActualAttribute(): int
    {
        $vendido = DetalleVenta::where('lote_local_id', $this->id)
            ->whereHas('venta')
            ->sum('cantidad');

        return $this->cantidad_disponible - (int) $vendido;
    }

    public function producto()
    {
        return $this->belongsTo(ProductoLocal::class, 'sku_producto', 'sku');
    }

    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'lote_local_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
