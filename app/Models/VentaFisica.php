<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaFisica extends Model
{
    protected $table = 'ventas_fisicas';

    protected $fillable = [
        'sede_id',
        'user_id',
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

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
}
