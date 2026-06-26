<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LoteLocal;
use App\Models\ProductoLocal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoteLocal>
 */
class LoteLocalFactory extends Factory
{
    protected $model = LoteLocal::class;

    public function definition(): array
    {
        return [
            'sku_producto' => ProductoLocal::factory(),
            'numero_lote' => 'LOT-' . strtoupper(fake()->bothify('??###')),
            'fecha_vencimiento' => fake()->dateTimeBetween('+1 month', '+3 years'),
            'cantidad_disponible' => fake()->numberBetween(10, 500),
        ];
    }

    public function porVencer(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_vencimiento' => fake()->dateTimeBetween('now', '+30 days'),
        ]);
    }

    public function vencido(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_vencimiento' => fake()->dateTimeBetween('-6 months', '-1 day'),
        ]);
    }

    public function stockBajo(): static
    {
        return $this->state(fn (array $attributes) => [
            'cantidad_disponible' => fake()->numberBetween(1, 5),
        ]);
    }
}
