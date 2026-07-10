<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DetalleVenta;
use App\Models\ProductoLocal;
use App\Models\VentaFisica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetalleVenta>
 */
class DetalleVentaFactory extends Factory
{
    protected $model = DetalleVenta::class;

    public function definition(): array
    {
        $cantidad = fake()->numberBetween(1, 10);
        $precioUnitario = fake()->randomFloat(2, 1.50, 120.00);
        $subtotal = round($cantidad * $precioUnitario, 2);

        return [
            'venta_id' => VentaFisica::factory(),
            'producto_sku' => ProductoLocal::factory(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal,
        ];
    }

    public function paraVenta(VentaFisica $venta): static
    {
        return $this->state(fn (array $attributes) => [
            'venta_id' => $venta->id,
        ]);
    }

    public function conProducto(ProductoLocal $producto): static
    {
        return $this->state(fn (array $attributes) => [
            'producto_sku' => $producto->sku,
        ]);
    }

    public function cantidad(int $cantidad): static
    {
        $precioUnitario = fake()->randomFloat(2, 1.50, 120.00);

        return $this->state(fn (array $attributes) => [
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => round($cantidad * $precioUnitario, 2),
        ]);
    }
}
