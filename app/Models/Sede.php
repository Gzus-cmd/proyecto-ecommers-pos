<?php

namespace App\Models;

use Database\Factories\SedeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, VentaFisica> $ventasFisicas
 */
class Sede extends Model
{
    /** @use HasFactory<SedeFactory> */
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
     */
    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    /**
     * Obtiene las ventas físicas realizadas en esta sede.
     *
     * @return HasMany
     */
    public function ventasFisicas()
    {
        return $this->hasMany(VentaFisica::class);
    }
}
