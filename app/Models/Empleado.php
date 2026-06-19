<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'nombres',
        'apellidos',
        'dni',
        'cargo',
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

    public function ventasFisicas()
    {
        return $this->hasMany(VentaFisica::class);
    }
}
