<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MetodoPago;
use App\Models\Sede;
use App\Models\User;
use App\Models\VentaFisica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VentaFisica>
 */
class VentaFisicaFactory extends Factory
{
    protected $model = VentaFisica::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 10, 500);
        $impuesto = round($subtotal * 0.18, 2);
        $total = round($subtotal + $impuesto, 2);

        return [
            'sede_id' => Sede::factory(),
            'user_id' => User::factory(),
            'fecha_venta' => fake()->dateTimeBetween('-1 month', 'now'),
            'subtotal' => $subtotal,
            'impuesto' => $impuesto,
            'total' => $total,
            'metodo_pago_id' => MetodoPago::factory(),
        ];
    }

    public function deHoy(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_venta' => now(),
        ]);
    }

    public function deAyer(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_venta' => now()->subDay(),
        ]);
    }

    public function conMetodoPago(MetodoPago $metodoPago): static
    {
        return $this->state(fn (array $attributes) => [
            'metodo_pago_id' => $metodoPago->id,
        ]);
    }

    public function enSede(Sede $sede): static
    {
        return $this->state(fn (array $attributes) => [
            'sede_id' => $sede->id,
        ]);
    }
}
