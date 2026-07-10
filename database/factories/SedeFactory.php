<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Sede;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sede>
 */
class SedeFactory extends Factory
{
    protected $model = Sede::class;

    public function definition(): array
    {
        return [
            'codigo' => 'SED-'.fake()->unique()->numerify('###'),
            'nombre' => fake()->company().' Sede',
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
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
