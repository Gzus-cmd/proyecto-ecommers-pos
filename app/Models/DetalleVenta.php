<?php

namespace App\Models;

use Database\Factories\DetalleVentaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Representa la línea de detalle de una venta física.
 *
 * Cada detalle asocia un producto (y opcionalmente un lote) con una venta,
 * registrando la cantidad, el precio unitario y el subtotal.
 *
 * @property int $id
 * @property int $venta_id
 * @property string $producto_sku
 * @property int|null $lote_local_id
 * @property int $cantidad
 * @property float $precio_unitario
 * @property float $subtotal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read VentaFisica $venta
 * @property-read ProductoLocal $producto
 * @property-read LoteLocal|null $lote
 */
class DetalleVenta extends Model
{
    /** @use HasFactory<DetalleVentaFactory> */
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

    /**
     * Obtiene la venta a la que pertenece este detalle.
     *
     * @return BelongsTo
     */
    public function venta()
    {
        return $this->belongsTo(VentaFisica::class, 'venta_id');
    }

    /**
     * Obtiene el producto vendido en este detalle.
     *
     * @return BelongsTo
     */
    public function producto()
    {
        return $this->belongsTo(ProductoLocal::class, 'producto_sku', 'sku');
    }

    /**
     * Obtiene el lote del producto asociado a este detalle (opcional).
     *
     * @return BelongsTo
     */
    public function lote()
    {
        return $this->belongsTo(LoteLocal::class, 'lote_local_id');
    }
}
