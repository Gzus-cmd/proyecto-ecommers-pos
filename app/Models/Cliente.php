<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'telefono',
        'email',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }
}
