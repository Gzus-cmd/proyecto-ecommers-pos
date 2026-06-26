# Manual Técnico — Pharma Victoria POS

## 1. Requisitos del Sistema
- PHP 8.3+
- Composer
- Node.js 20+
- Docker + Docker Compose (para Laravel Sail)
- MySQL 8.0+

## 2. Instalación
```bash
git clone <repo>
cd proyecto-ecommers-pos
cp .env.example .env
# Editar .env con datos de BD
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
```

## 3. Arquitectura
- **Framework**: Laravel 13 + Inertia Vue 3 + TypeScript
- **Frontend**: Vue 3 + Tailwind CSS v4 + shadcn/vue (reka-ui)
- **Base de datos**: MySQL 8.0+
- **Autenticación**: Laravel Fortify + spatie/laravel-permission
- **Roles**: admin (acceso completo), vendedor (ventas y consultas)

## 4. Estructura de Directorios
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/          → LoginController
│   │   ├── Pos/           → CRUD controllers
│   │   └── Settings/      → Perfil, SedeConfig
│   ├── Requests/          → Form validation
│   └── Middleware/
│       └── HandleInertiaRequests.php
├── Models/
│   ├── User.php
│   ├── Sede.php
│   ├── ProductoLocal.php
│   ├── LoteLocal.php
│   ├── MetodoPago.php
│   ├── Cliente.php
│   ├── VentaFisica.php
│   └── DetalleVenta.php
database/
├── migrations/
├── factories/
└── seeders/
    └── PosDemoSeeder.php  → Datos demo
resources/
├── js/
│   ├── app.ts             → Entry Inertia
│   ├── Layouts/
│   │   └── PosLayout.vue  → Sidebar layout
│   ├── Pages/
│   │   ├── Auth/          → Login
│   │   ├── Pos/           → Módulos POS
│   │   └── Settings/      → Perfil, Sede
│   ├── Components/pos/    → UI compartidos
│   └── lib/
│       └── route.ts       → Route helper (sin Ziggy)
routes/
├── web.php                → Rutas login/auth
└── pos.php                → Rutas del módulo POS
```

## 5. Base de Datos — Tablas principales
| Tabla | Descripción |
|-------|-------------|
| `users` | Usuarios del sistema (empleados) con roles |
| `sedes` | Datos de la sede actual |
| `productos_local` | Catálogo de productos farmacéuticos (PK: sku) |
| `lotes_local` | Lotes de cada producto con fecha vencimiento y stock |
| `metodos_pago` | Métodos de pago (Efectivo, Yape, Transferencia) |
| `clientes` | Clientes con DNI único |
| `ventas_fisicas` | Cabecera de ventas |
| `detalle_ventas` | Líneas de detalle de cada venta |
| `v_stock_lotes` | Vista calculada de stock actual |
| `permissions` / `roles` / `model_has_roles` | Spatie permissions |

## 6. Relaciones clave
- `ventas_fisicas.user_id` → `users.id`
- `ventas_fisicas.metodo_pago_id` → `metodos_pago.id`
- `ventas_fisicas.cliente_id` → `clientes.id`
- `detalle_ventas.venta_id` → `ventas_fisicas.id`
- `detalle_ventas.producto_sku` → `productos_local.sku`
- `detalle_ventas.lote_local_id` → `lotes_local.id`
- `lotes_local.sku_producto` → `productos_local.sku`

## 7. Stock — Cálculo automático
El stock se calcula vía la vista `v_stock_lotes`:
```sql
stock_actual = lotes_local.cantidad_disponible - SUM(detalle_ventas.cantidad)
```

## 8. Rutas del módulo POS
Todas bajo prefijo `/pos/`. Archivo: `routes/pos.php`.
Las rutas se usan en el frontend via `lib/route.ts` (sin Ziggy).

## 9. Comandos útiles
```bash
# Desarrollo
./vendor/bin/sail up -d
./vendor/bin/sail npm run dev
./vendor/bin/sail artisan migrate --seed

# Producción
npm run build
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache

# Gestión roles
./vendor/bin/sail artisan permission:create-role admin
./vendor/bin/sail artisan permission:create-role vendedor
```

## 10. Configuración de Sede
Variables en `.env`:
```
SEDE_NOMBRE="Nombre de Farmacia"
SEDE_CODIGO=SED-001
SEDE_DIRECCION="Dirección completa"
SEDE_TELEFONO=999888777
```
**Importante**: valores con espacios deben ir ENTRE COMILLAS DOBLES.
