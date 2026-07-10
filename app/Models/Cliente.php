<?php

namespace App\Models;

use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, VentaFisica> $ventasFisicas
 */
class Cliente extends Model
{
    /** @use HasFactory<ClienteFactory> */
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
