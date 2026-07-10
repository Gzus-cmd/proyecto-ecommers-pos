<?php

namespace App\Models;

use Database\Factories\ProductoLocalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Representa un producto disponible en el inventario local del POS.
 *
 * La clave primaria es el SKU (código alfanumérico único del producto).
 *
 * @property string $sku
 * @property string $nombre_comercial
 * @property string|null $nombre_generico
 * @property string|null $descripcion
 * @property string|null $concentracion
 * @property string|null $forma_farmaceutica
 * @property bool $requiere_receta
 * @property float $precio_venta
 * @property string|null $fecha_vencimiento
 * @property bool $activo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, LoteLocal> $lotes
 * @property-read Collection<int, DetalleVenta> $detallesVenta
 */
class ProductoLocal extends Model
{
    /** @use HasFactory<ProductoLocalFactory> */
    use HasFactory;

    protected $table = 'productos_local';

    protected $primaryKey = 'sku';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'sku',
        'nombre_comercial',
        'nombre_generico',
        'descripcion',
        'concentracion',
        'forma_farmaceutica',
        'requiere_receta',
        'precio_venta',
        'fecha_vencimiento',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'requiere_receta' => 'boolean',
            'activo' => 'boolean',
            'precio_venta' => 'decimal:2',
            'fecha_vencimiento' => 'date',
        ];
    }

    /**
     * Filtra el query para incluir solo productos activos.
     */
    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    /**
     * Obtiene los lotes asociados a este producto.
     *
     * @return HasMany
     */
    public function lotes()
    {
        return $this->hasMany(LoteLocal::class, 'sku_producto', 'sku');
    }

    /**
     * Obtiene los detalles de venta donde se ha vendido este producto.
     *
     * @return HasMany
     */
    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'producto_sku', 'sku');
    }
}
