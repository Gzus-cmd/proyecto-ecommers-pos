/**
 * Route helper — enumeración manual de rutas POS.
 * Reemplaza Ziggy: sin dependencias, sin Blade, 100% TypeScript.
 */

type Primitive = string | number | boolean | null | undefined;
type Params = Record<string, Primitive> | Primitive;

const ROUTES: Record<string, string> = {
    // ── Auth ────────────────────────────────────────────────
    'login': '/login',
    'login.store': '/login',
    'logout': '/logout',

    // ── Dashboard ───────────────────────────────────────────
    'pos.dashboard': '/pos',

    // ── Sedes ──────────────────────────────────────────────
    'pos.sedes.index': '/pos/sedes',
    'pos.sedes.create': '/pos/sedes/create',
    'pos.sedes.store': '/pos/sedes',
    'pos.sedes.edit': '/pos/sedes/{sede}/edit',
    'pos.sedes.update': '/pos/sedes/{sede}',
    'pos.sedes.destroy': '/pos/sedes/{sede}',

    // ── Productos ──────────────────────────────────────────
    'pos.productos.index': '/pos/productos',
    'pos.productos.create': '/pos/productos/create',
    'pos.productos.store': '/pos/productos',
    'pos.productos.show': '/pos/productos/{producto}',
    'pos.productos.edit': '/pos/productos/{producto}/edit',
    'pos.productos.update': '/pos/productos/{producto}',
    'pos.productos.destroy': '/pos/productos/{producto}',

    // ── Lotes ──────────────────────────────────────────────
    'pos.lotes.index': '/pos/lotes',
    'pos.lotes.create': '/pos/lotes/create',
    'pos.lotes.store': '/pos/lotes',
    'pos.lotes.show': '/pos/lotes/{lote}',
    'pos.lotes.edit': '/pos/lotes/{lote}/edit',
    'pos.lotes.update': '/pos/lotes/{lote}',
    'pos.lotes.destroy': '/pos/lotes/{lote}',

    // ── Métodos de Pago ────────────────────────────────────
    'pos.metodos-pago.index': '/pos/metodos-pago',
    'pos.metodos-pago.create': '/pos/metodos-pago/create',
    'pos.metodos-pago.store': '/pos/metodos-pago',
    'pos.metodos-pago.edit': '/pos/metodos-pago/{metodos_pago}/edit',
    'pos.metodos-pago.update': '/pos/metodos-pago/{metodos_pago}',
    'pos.metodos-pago.destroy': '/pos/metodos-pago/{metodos_pago}',

    // ── Clientes ───────────────────────────────────────────
    'pos.clientes.index': '/pos/clientes',
    'pos.clientes.create': '/pos/clientes/create',
    'pos.clientes.store': '/pos/clientes',
    'pos.clientes.edit': '/pos/clientes/{cliente}/edit',
    'pos.clientes.update': '/pos/clientes/{cliente}',
    'pos.clientes.destroy': '/pos/clientes/{cliente}',

    // ── Stock ──────────────────────────────────────────────
    'pos.stock.index': '/pos/stock',
    'pos.stock.show': '/pos/stock/{stock}',

    // ── Ventas ─────────────────────────────────────────────
    'pos.ventas.index': '/pos/ventas',
    'pos.ventas.create': '/pos/ventas/crear',
    'pos.ventas.store': '/pos/ventas',
    'pos.ventas.show': '/pos/ventas/{venta}',

    // ── Detalle Ventas ─────────────────────────────────────
    'pos.detalle-ventas.index': '/pos/detalle-ventas',

    // ── Usuarios ───────────────────────────────────────────
    'pos.users.index': '/pos/users',
    'pos.users.create': '/pos/users/crear',
    'pos.users.store': '/pos/users',
    'pos.users.edit': '/pos/users/{user}/edit',
    'pos.users.update': '/pos/users/{user}',
    'pos.users.destroy': '/pos/users/{user}',

    // ── Settings ───────────────────────────────────────────
    'settings.profile': '/settings/profile',
    'settings.profile.update': '/settings/profile',
    'settings.profile.password': '/settings/profile/password',
    'settings.sede.edit': '/settings/sede',
    'settings.sede.update': '/settings/sede',
};

export function route(name: string, params?: Params): string {
    let uri = ROUTES[name];

    if (!uri) {
        console.warn(`[route] Ruta "${name}" no encontrada.`);
        return '#';
    }

    if (params !== undefined) {
        if (typeof params === 'object' && params !== null) {
            for (const [key, value] of Object.entries(params as Record<string, Primitive>)) {
                uri = uri.replace(`{${key}}`, String(value));
            }
        } else {
            const match = uri.match(/\{([^}]+)\}/);
            if (match) {
                uri = uri.replace(`{${match[1]}}`, String(params));
            }
        }
    }

    return uri;
}
