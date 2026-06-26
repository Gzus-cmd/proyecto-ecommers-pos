export interface PageProps {
    [key: string]: unknown;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

export interface Sede {
    id: number;
    codigo: string;
    nombre: string;
    direccion: string | null;
    telefono: string | null;
    activo: boolean;
}

export interface MetodoPago {
    id: number;
    nombre: string;
    numero_cuenta: string | null;
    titular: string | null;
    activo: boolean;
}

export interface User {
    id: number;
    name: string;
    email: string;
    activo: boolean;
    created_at?: string;
}

export interface Cliente {
    id: number;
    dni: string;
    nombres: string;
    apellidos: string;
    telefono: string | null;
    email: string | null;
}

export interface ProductoLocal {
    sku: string;
    nombre_comercial: string;
    nombre_generico: string | null;
    descripcion: string | null;
    concentracion: string | null;
    forma_farmaceutica: string | null;
    requiere_receta: boolean;
    precio_venta: number;
    fecha_vencimiento: string | null;
    activo: boolean;
}

export interface LoteLocal {
    id: number;
    sku_producto: string;
    numero_lote: string;
    fecha_vencimiento: string;
    cantidad_disponible: number;
    user_id?: number;
    producto?: ProductoLocal;
    user?: User;
}

export interface VentaFisica {
    id: number;
    sede_id: number;
    user_id: number;
    cliente_id?: number;
    fecha_venta: string;
    subtotal: number;
    impuesto: number;
    total: number;
    metodo_pago_id: number;
    sede?: Sede;
    user?: User;
    metodo_pago?: MetodoPago;
    cliente?: Cliente;
    detalles?: DetalleVenta[];
}

export interface DetalleVenta {
    id: number;
    venta_id: number;
    producto_sku: string;
    lote_local_id?: number;
    cantidad: number;
    precio_unitario: number;
    subtotal: number;
    venta?: VentaFisica;
    producto?: ProductoLocal;
    lote?: LoteLocal;
}
