# Manual de Usuario — Pharma Victoria POS

## 1. Acceso al Sistema
1. Abrir navegador y entrar a la URL del POS
2. Ingresar correo electrónico y contraseña
3. Presionar "Iniciar Sesión"

## 2. Dashboard (Pantalla Principal)
Muestra:
- **Total Productos**: cantidad de productos registrados
- **Ventas Hoy**: ventas realizadas hoy + monto
- **Productos por Vencer**: lotes próximos a caducar (90 días)
- **Stock Bajo**: productos con menos de 10 unidades

- Gráfico de barras: Ventas por día (últimos 30 días)
- Gráfico donut: Productos activos vs inactivos
- Filtro: seleccionar rango de fechas para ver ventas por día
- Top productos más vendidos en el rango seleccionado

## 3. Gestión de Productos
**Agregar**: menú Productos → Nuevo Producto
- Campos: SKU (código único), nombre, precio, vencimiento, etc.
- Productos requieren sku único

**Editar/Ver**: iconos en la tabla

**Eliminar**: solo si no tiene lotes asociados

## 4. Gestión de Lotes
**Recepción de Lotes**: menú Lotes → Nuevo Lote
- Se pueden agregar múltiples productos en una misma recepción
- Cada lote necesita: producto, número de lote, vencimiento, cantidad

**Estados**:
- 🟢 Vigente: > 90 días para vencer
- 🟡 Por Vencer: 1-90 días
- 🔴 Vencido: ya venció

## 5. Ventas
**Nueva Venta**: menú Ventas → Nueva Venta
1. Seleccionar cliente (opcional, buscar por DNI)
2. Seleccionar método de pago
3. Agregar productos: seleccionar producto + lote + cantidad
4. Confirmar venta

## 6. Stock
Menú Stock → tabla con todos los lotes y su stock actual
- Botón "Retirar Stock" para lotes vencidos (pone cantidad a 0)

## 7. Usuarios
Menú Gestión → Usuarios
- Crear/editar usuarios del sistema
- Asignar rol: admin (acceso total) o vendedor (ventas)

## 8. Clientes
Menú Gestión → Clientes
- Solo DNI es obligatorio
- El resto de datos son opcionales

## 9. Métodos de Pago
Menú Configuración → Métodos de Pago
- Efectivo, Yape (con número), Transferencia (con cuenta)
- Se pueden crear nuevos métodos

## 10. Configuración de Sede
Menú Configuración → Sede (en el sidebar)
- Nombre, código, dirección, teléfono
- **Nota**: Si la dirección tiene espacios, el sistema agrega comillas automáticamente

## 11. Perfil de Usuario
Click en tu nombre (abajo del sidebar) → Perfil
- Cambiar nombre, email
- Cambiar contraseña

## 12. Cerrar Sesión
Click en tu nombre (abajo del sidebar) → Cerrar Sesión
