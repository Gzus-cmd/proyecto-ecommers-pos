<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Representa una sede o sucursal del sistema POS.
 *
 * Cada sede puede tener múltiples ventas físicas asociadas.
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property string|null $direccion
 * @property string|null $telefono
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VentaFisica> $ventasFisicas
 */
class Sede extends Model
{
    /** @use HasFactory<\Database\Factories\SedeFactory> */
    use HasFactory;

    protected $table = 'sedes';

    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Filtra el query para incluir solo registros activos.
     *
     * @param  Builder $query
     * @return void
     */
    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    /**
     * Obtiene las ventas físicas realizadas en esta sede.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ventasFisicas()
    {
        return $this->hasMany(VentaFisica::class);
    }
}
