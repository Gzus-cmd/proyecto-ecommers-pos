<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Representa un cliente registrado en el sistema POS.
 *
 * Almacena los datos básicos de identificación y contacto del cliente.
 *
 * @property int $id
 * @property string $dni
 * @property string $nombres
 * @property string $apellidos
 * @property string|null $telefono
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VentaFisica> $ventasFisicas
 */
class Cliente extends Model
{
    /** @use HasFactory<\Database\Factories\ClienteFactory> */
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'telefono',
        'email',
    ];
}
