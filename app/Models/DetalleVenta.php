<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    /** @use HasFactory<\Database\Factories\DetalleVentaFactory> */
    use HasFactory;

    protected $table = 'detalle_ventas';

    protected $fillable = [
        'venta_id',
        'producto_sku',
        'lote_local_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'precio_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function venta()
    {
        return $this->belongsTo(VentaFisica::class, 'venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(ProductoLocal::class, 'producto_sku', 'sku');
    }

    public function lote()
    {
        return $this->belongsTo(LoteLocal::class, 'lote_local_id');
    }
}
