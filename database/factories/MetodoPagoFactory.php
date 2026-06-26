<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MetodoPago;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MetodoPago>
 */
class MetodoPagoFactory extends Factory
{
    protected $model = MetodoPago::class;

    private static array $metodos = [
        'Efectivo', 'Tarjeta de Crédito', 'Tarjeta de Débito',
        'Yape', 'Plin', 'Transferencia Bancaria',
        'Visa', 'Mastercard', 'American Express',
    ];

    public function definition(): array
    {
        return [
            'nombre' => fake()->randomElement(self::$metodos) . ' ' . fake()->unique()->numerify('##'),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
