<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductoLocal extends Model
{
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
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'requiere_receta' => 'boolean',
            'activo' => 'boolean',
            'precio_venta' => 'decimal:2',
        ];
    }

    /**
     * Scope a query to only include active records.
     */
    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    public function lotes()
    {
        return $this->hasMany(LoteLocal::class, 'sku_producto', 'sku');
    }

    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'producto_sku', 'sku');
    }
}
