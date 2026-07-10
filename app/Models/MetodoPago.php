<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Representa un método de pago disponible en el sistema POS.
 *
 * Ejemplos: Efectivo, Tarjeta, Yape, Plin. Puede incluir datos de cuenta
 * bancaria o billetera digital (número de cuenta y titular).
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $numero_cuenta
 * @property string|null $titular
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VentaFisica> $ventasFisicas
 */
class MetodoPago extends Model
{
    /** @use HasFactory<\Database\Factories\MetodoPagoFactory> */
    use HasFactory;

    protected $table = 'metodos_pago';

    protected $fillable = [
        'nombre',
        'numero_cuenta',
        'titular',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Filtra el query para incluir solo métodos de pago activos.
     *
     * @param  Builder $query
     * @return void
     */
    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    /**
     * Obtiene las ventas físicas que usaron este método de pago.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ventasFisicas()
    {
        return $this->hasMany(VentaFisica::class);
    }
}
