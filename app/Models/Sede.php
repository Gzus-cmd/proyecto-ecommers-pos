<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Sede extends Model
{
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
     * Scope a query to only include active records.
     */
    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    public function stockLocal()
    {
        return $this->hasMany(StockLocal::class);
    }

    public function ventasFisicas()
    {
        return $this->hasMany(VentaFisica::class);
    }
}
