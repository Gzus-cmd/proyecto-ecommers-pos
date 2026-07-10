<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa una venta física (transacción en mostrador / punto de venta).
 *
 * Agrupa una serie de detalles (productos vendidos) asociados a una sede,
 * un usuario (vendedor), un cliente opcional y un método de pago.
 *
 * @property int $id
 * @property int $sede_id
 * @property int $user_id
 * @property int|null $cliente_id
 * @property string $fecha_venta
 * @property float $subtotal
 * @property float $impuesto
 * @property float $total
 * @property int $metodo_pago_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Sede $sede
 * @property-read User $user
 * @property-read MetodoPago $metodoPago
 * @property-read Cliente|null $cliente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, DetalleVenta> $detalles
 */
class VentaFisica extends Model
{
    /** @use HasFactory<\Database\Factories\VentaFisicaFactory> */
    use HasFactory;

    protected $table = 'ventas_fisicas';

    protected $fillable = [
        'sede_id',
        'user_id',
        'cliente_id',
        'fecha_venta',
        'subtotal',
        'impuesto',
        'total',
        'metodo_pago_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_venta' => 'datetime',
            'subtotal' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Obtiene la sede donde se realizó la venta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * Obtiene el usuario (vendedor) que registró la venta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el método de pago usado en la venta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }

    /**
     * Obtiene el cliente asociado a la venta (opcional).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Obtiene el detalle de los productos vendidos en esta venta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
}
