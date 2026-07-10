<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ProductoLocal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductoLocal>
 */
class ProductoLocalFactory extends Factory
{
    protected $model = ProductoLocal::class;

    private static array $medicamentos = [
        'Paracetamol', 'Ibuprofeno', 'Amoxicilina', 'Omeprazol', 'Loratadina',
        'Losartán', 'Metformina', 'Aspirina', 'Diclofenaco', 'Azitromicina',
        'Cetirizina', 'Ranitidina', 'Salbutamol', 'Prednisona', 'Dexametasona',
        'Clonazepam', 'Sertralina', 'Fluoxetina', 'Enalapril', 'Atorvastatina',
    ];

    private static array $formas = [
        'Tabletas', 'Cápsulas', 'Jarabe', 'Suspensión', 'Inyectable',
        'Crema', 'Ungüento', 'Gotas', 'Solución', 'Polvo',
    ];

    private static array $concentraciones = [
        '500mg', '250mg', '100mg', '50mg', '25mg',
        '10mg', '5mg', '1g', '750mg', '200mg',
    ];

    public function definition(): array
    {
        $nombre = fake()->randomElement(self::$medicamentos);
        $sku = 'PROD-'.fake()->unique()->numerify('####');

        return [
            'sku' => $sku,
            'nombre_comercial' => $nombre.' '.fake()->randomElement(['Genfar', 'MK', 'Medrock', 'Astra', 'Bayer']),
            'nombre_generico' => $nombre,
            'descripcion' => 'Medicamento '.strtolower($nombre).' para uso farmacéutico.',
            'concentracion' => fake()->randomElement(self::$concentraciones),
            'forma_farmaceutica' => fake()->randomElement(self::$formas),
            'requiere_receta' => fake()->boolean(),
            'precio_venta' => fake()->randomFloat(2, 1.50, 150.00),
            'fecha_vencimiento' => fake()->optional(0.7)->dateTimeBetween('+1 month', '+3 years'),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }

    public function requiereReceta(): static
    {
        return $this->state(fn (array $attributes) => [
            'requiere_receta' => true,
        ]);
    }

    public function proximoAVencer(): static
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
}
