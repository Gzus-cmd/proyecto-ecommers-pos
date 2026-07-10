<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\LoteLocal;
use App\Models\MetodoPago;
use App\Models\ProductoLocal;
use App\Models\Sede;
use App\Models\User;
use App\Models\VentaFisica;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PosDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Sede desde config
        $sede = Sede::firstOrCreate(
            ['codigo' => config('sede.codigo', 'SED-001')],
            [
                'nombre' => config('sede.nombre', 'Sede Principal'),
                'direccion' => config('sede.direccion', ''),
                'telefono' => config('sede.telefono', ''),
                'activo' => true,
            ]
        );

        // 2. Métodos de pago
        $efectivo = MetodoPago::firstOrCreate(
            ['nombre' => 'Efectivo'],
            ['activo' => true]
        );
        $yape = MetodoPago::firstOrCreate(
            ['nombre' => 'Yape'],
            ['numero_cuenta' => '999888777', 'titular' => 'Pharma Victoria', 'activo' => true]
        );
        $transferencia = MetodoPago::firstOrCreate(
            ['nombre' => 'Transferencia Bancaria'],
            ['numero_cuenta' => '0011-0666-1234567890', 'titular' => 'Pharma Victoria SAC', 'activo' => true]
        );

        // 3. Productos con diferentes estados
        $medicamentos = [
            ['sku' => 'PROD-0001', 'nombre_comercial' => 'Paracetamol Genfar 500mg', 'nombre_generico' => 'Paracetamol', 'precio_venta' => 5.50, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '500mg'],
            ['sku' => 'PROD-0002', 'nombre_comercial' => 'Ibuprofeno MK 400mg', 'nombre_generico' => 'Ibuprofeno', 'precio_venta' => 8.90, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '400mg'],
            ['sku' => 'PROD-0003', 'nombre_comercial' => 'Amoxicilina Medrock 500mg', 'nombre_generico' => 'Amoxicilina', 'precio_venta' => 12.50, 'forma_farmaceutica' => 'Cápsulas', 'concentracion' => '500mg'],
            ['sku' => 'PROD-0004', 'nombre_comercial' => 'Omeprazol Astra 20mg', 'nombre_generico' => 'Omeprazol', 'precio_venta' => 15.00, 'forma_farmaceutica' => 'Cápsulas', 'concentracion' => '20mg'],
            ['sku' => 'PROD-0005', 'nombre_comercial' => 'Loratadina Bayer 10mg', 'nombre_generico' => 'Loratadina', 'precio_venta' => 7.20, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '10mg'],
            ['sku' => 'PROD-0006', 'nombre_comercial' => 'Losartán MK 50mg', 'nombre_generico' => 'Losartán', 'precio_venta' => 18.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '50mg'],
            ['sku' => 'PROD-0007', 'nombre_comercial' => 'Metformina Genfar 850mg', 'nombre_generico' => 'Metformina', 'precio_venta' => 9.30, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '850mg'],
            ['sku' => 'PROD-0008', 'nombre_comercial' => 'Aspirina Bayer 500mg', 'nombre_generico' => 'Ácido Acetilsalicílico', 'precio_venta' => 4.80, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '500mg'],
            ['sku' => 'PROD-0009', 'nombre_comercial' => 'Diclofenaco Medrock 75mg', 'nombre_generico' => 'Diclofenaco', 'precio_venta' => 6.50, 'forma_farmaceutica' => 'Inyectable', 'concentracion' => '75mg'],
            ['sku' => 'PROD-0010', 'nombre_comercial' => 'Azitromicina MK 500mg', 'nombre_generico' => 'Azitromicina', 'precio_venta' => 22.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '500mg', 'requiere_receta' => true],
            ['sku' => 'PROD-0011', 'nombre_comercial' => 'Cetirizina Genfar 10mg', 'nombre_generico' => 'Cetirizina', 'precio_venta' => 6.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '10mg'],
            ['sku' => 'PROD-0012', 'nombre_comercial' => 'Salbutamol Astra 100mcg', 'nombre_generico' => 'Salbutamol', 'precio_venta' => 14.00, 'forma_farmaceutica' => 'Inhalador', 'concentracion' => '100mcg'],
            ['sku' => 'PROD-0013', 'nombre_comercial' => 'Prednisona Medrock 20mg', 'nombre_generico' => 'Prednisona', 'precio_venta' => 11.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '20mg', 'requiere_receta' => true],
            ['sku' => 'PROD-0014', 'nombre_comercial' => 'Enalapril MK 10mg', 'nombre_generico' => 'Enalapril', 'precio_venta' => 13.50, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '10mg'],
            ['sku' => 'PROD-0015', 'nombre_comercial' => 'Atorvastatina Genfar 20mg', 'nombre_generico' => 'Atorvastatina', 'precio_venta' => 25.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '20mg'],
            ['sku' => 'PROD-0016', 'nombre_comercial' => 'Clonazepam Roche 2mg', 'nombre_generico' => 'Clonazepam', 'precio_venta' => 16.50, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '2mg', 'requiere_receta' => true],
            ['sku' => 'PROD-0017', 'nombre_comercial' => 'Sertralina MK 50mg', 'nombre_generico' => 'Sertralina', 'precio_venta' => 28.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '50mg', 'requiere_receta' => true],
            ['sku' => 'PROD-0018', 'nombre_comercial' => 'Fluoxetina Genfar 20mg', 'nombre_generico' => 'Fluoxetina', 'precio_venta' => 19.90, 'forma_farmaceutica' => 'Cápsulas', 'concentracion' => '20mg', 'requiere_receta' => true],
            ['sku' => 'PROD-0019', 'nombre_comercial' => 'Dexametasona Astra 4mg', 'nombre_generico' => 'Dexametasona', 'precio_venta' => 3.50, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '4mg'],
            ['sku' => 'PROD-0020', 'nombre_comercial' => 'Vitamina C MK 1000mg', 'nombre_generico' => 'Ácido Ascórbico', 'precio_venta' => 10.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '1000mg'],
            ['sku' => 'PROD-0021', 'nombre_comercial' => 'Complejo B Bayer', 'nombre_generico' => 'Vitaminas B1+B6+B12', 'precio_venta' => 17.00, 'forma_farmaceutica' => 'Inyectable', 'concentracion' => '100mg'],
            ['sku' => 'PROD-0022', 'nombre_comercial' => 'Hierro Genfar 100mg', 'nombre_generico' => 'Sulfato Ferroso', 'precio_venta' => 8.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '100mg'],
            ['sku' => 'PROD-0023', 'nombre_comercial' => 'Calcio MK 500mg', 'nombre_generico' => 'Carbonato de Calcio', 'precio_venta' => 12.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '500mg'],
            ['sku' => 'PROD-0024', 'nombre_comercial' => 'Magnesio Genfar 400mg', 'nombre_generico' => 'Óxido de Magnesio', 'precio_venta' => 14.50, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '400mg'],
            ['sku' => 'PROD-0025', 'nombre_comercial' => 'Omeprazol MK 40mg', 'nombre_generico' => 'Omeprazol', 'precio_venta' => 18.00, 'forma_farmaceutica' => 'Cápsulas', 'concentracion' => '40mg'],
            ['sku' => 'PROD-0026', 'nombre_comercial' => 'Ibuprofeno Genfar 600mg', 'nombre_generico' => 'Ibuprofeno', 'precio_venta' => 10.50, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '600mg'],
            ['sku' => 'PROD-0027', 'nombre_comercial' => 'Losartán Genfar 25mg', 'nombre_generico' => 'Losartán', 'precio_venta' => 14.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '25mg'],
            ['sku' => 'PROD-0028', 'nombre_comercial' => 'Metformina MK 500mg', 'nombre_generico' => 'Metformina', 'precio_venta' => 8.50, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '500mg'],
            ['sku' => 'PROD-0029', 'nombre_comercial' => 'Paracetamol MK 1g', 'nombre_generico' => 'Paracetamol', 'precio_venta' => 7.00, 'forma_farmaceutica' => 'Tabletas', 'concentracion' => '1g'],
            ['sku' => 'PROD-0030', 'nombre_comercial' => 'Amoxicilina Genfar 250mg', 'nombre_generico' => 'Amoxicilina', 'precio_venta' => 9.00, 'forma_farmaceutica' => 'Suspensión', 'concentracion' => '250mg'],
        ];

        foreach ($medicamentos as $data) {
            ProductoLocal::firstOrCreate(
                ['sku' => $data['sku']],
                array_merge($data, [
                    'descripcion' => 'Medicamento '.strtolower($data['nombre_generico']),
                    'requiere_receta' => $data['requiere_receta'] ?? false,
                    'activo' => true,
                ])
            );
        }

        // 4. Lotes — 20 lotes (10 fresh, 5 near expiry, 5 expired)
        $productos = ProductoLocal::all();
        $loteIndex = 0;

        // 10 fresh lotes
        $freshProducts = $productos->take(10);
        foreach ($freshProducts as $producto) {
            LoteLocal::factory()->fresh()->create([
                'sku_producto' => $producto->sku,
                'numero_lote' => 'LOT-FRESH-'.str_pad((string) ++$loteIndex, 3, '0', STR_PAD_LEFT),
                'cantidad_disponible' => 100,
            ]);
        }

        // 5 near expiry lotes
        $nearProducts = $productos->slice(10, 5);
        foreach ($nearProducts as $producto) {
            LoteLocal::factory()->nearExpiry()->create([
                'sku_producto' => $producto->sku,
                'numero_lote' => 'LOT-NEAR-'.str_pad((string) ++$loteIndex, 3, '0', STR_PAD_LEFT),
                'cantidad_disponible' => 50,
            ]);
        }

        // 5 expired lotes
        $expiredProducts = $productos->slice(15, 5);
        foreach ($expiredProducts as $producto) {
            LoteLocal::factory()->expired()->create([
                'sku_producto' => $producto->sku,
                'numero_lote' => 'LOT-EXP-'.str_pad((string) ++$loteIndex, 3, '0', STR_PAD_LEFT),
                'cantidad_disponible' => 30,
            ]);
        }

        // 5. Clientes
        $clientes = [];
        for ($i = 0; $i < 10; $i++) {
            $clientes[] = Cliente::factory()->create();
        }

        // 6. Usuarios
        $admin = User::firstOrCreate(
            ['email' => 'admin@pharma.com'],
            [
                'name' => 'Admin Principal',
                'password' => bcrypt('password'),
                'activo' => true,
            ]
        );

        $empleados = [];
        $nombresEmpleados = [
            ['name' => 'Carlos López', 'email' => 'carlos@pharma.com'],
            ['name' => 'María García', 'email' => 'maria@pharma.com'],
            ['name' => 'José Martínez', 'email' => 'jose@pharma.com'],
            ['name' => 'Ana Rodríguez', 'email' => 'ana@pharma.com'],
        ];

        foreach ($nombresEmpleados as $empData) {
            $empleados[] = User::firstOrCreate(
                ['email' => $empData['email']],
                [
                    'name' => $empData['name'],
                    'password' => bcrypt('password'),
                    'activo' => true,
                ]
            );
        }

        // 7. Ventas con detalles
        $metodosPago = [$efectivo, $yape, $transferencia];
        $todosUsuarios = array_merge([$admin], $empleados);

        for ($i = 0; $i < 15; $i++) {
            $cliente = $clientes[array_rand($clientes)];
            $usuario = $todosUsuarios[array_rand($todosUsuarios)];
            $metodo = $metodosPago[array_rand($metodosPago)];

            $fecha = now()->subDays(rand(0, 30))->subHours(rand(0, 12));

            $venta = VentaFisica::create([
                'sede_id' => $sede->id,
                'user_id' => $usuario->id,
                'cliente_id' => $cliente->id,
                'fecha_venta' => $fecha,
                'subtotal' => 0,
                'impuesto' => 0,
                'total' => 0,
                'metodo_pago_id' => $metodo->id,
            ]);

            // 1-3 detalles por venta
            $numDetalles = rand(1, 3);
            $subtotal = 0;

            // Pick lotes for this sale (mix of fresh and near expiry)
            $lotesDisponibles = LoteLocal::inRandomOrder()->take(5)->get();

            for ($j = 0; $j < $numDetalles; $j++) {
                $lote = $lotesDisponibles[$j];
                $cantidad = rand(1, 5);
                $precioUnitario = $lote->producto?->precio_venta ?? rand(5, 50);
                $detSubtotal = round($cantidad * $precioUnitario, 2);

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_sku' => $lote->sku_producto,
                    'lote_local_id' => $lote->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $detSubtotal,
                ]);

                $subtotal += $detSubtotal;
            }

            $impuesto = round($subtotal * 0.18, 2);
            $total = round($subtotal + $impuesto, 2);

            $venta->update([
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $total,
            ]);
        }

        // 8. Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $vendedorRole = Role::firstOrCreate(['name' => 'vendedor']);

        $admin->assignRole($adminRole);

        foreach ($empleados as $emp) {
            $emp->assignRole($vendedorRole);
        }
    }
}
