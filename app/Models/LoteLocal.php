<?php

namespace App\Models;

use Database\Factories\LoteLocalFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Representa un lote de un producto en el inventario local del POS.
 *
 * Controla la trazabilidad por lote: fecha de vencimiento, cantidad disponible
 * y stock actual calculado (descontando ventas realizadas).
 *
 * @property int $id
 * @property string $sku_producto
 * @property string $numero_lote
 * @property string $fecha_vencimiento
 * @property int $cantidad_disponible
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int $stock_actual
 * @property-read ProductoLocal $producto
 * @property-read Collection<int, DetalleVenta> $detallesVenta
 * @property-read User|null $user
 */
class LoteLocal extends Model
{
    /** @use HasFactory<LoteLocalFactory> */
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

    /**
     * Obtiene el stock actual del lote calculado como la cantidad disponible
     * menos la suma de cantidades vendidas en detalle_ventas.
     */
    public function getStockActualAttribute(): int
    {
        $vendido = DetalleVenta::where('lote_local_id', $this->id)
            ->whereHas('venta')
            ->sum('cantidad');

        return $this->cantidad_disponible - (int) $vendido;
    }

    /**
     * Obtiene el producto asociado a este lote.
     *
     * @return BelongsTo
     */
    public function producto()
    {
        return $this->belongsTo(ProductoLocal::class, 'sku_producto', 'sku');
    }

    /**
     * Obtiene los detalles de venta asociados a este lote.
     *
     * @return HasMany
     */
    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'lote_local_id');
    }

    /**
     * Obtiene el usuario que registró este lote.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
