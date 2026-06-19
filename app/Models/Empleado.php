<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'nombres',
        'apellidos',
        'dni',
        'cargo',
        'activo',
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ventasFisicas()
    {
        return $this->hasMany(VentaFisica::class);
    }
}
