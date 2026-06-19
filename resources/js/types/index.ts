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
    activo: boolean;
}

export interface User {
    id: number;
    name: string;
    email: string;
}

export interface Cliente {
    id: number;
    dni: string;
    nombres: string;
    apellidos: string;
    telefono: string | null;
    email: string | null;
    activo: boolean;
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
    activo: boolean;
}

export interface LoteLocal {
    id: number;
    sku_producto: string;
    numero_lote: string;
    fecha_vencimiento: string;
    cantidad_disponible: number;
    producto?: ProductoLocal;
}

export interface StockLocal {
    id: number;
    sede_id: number;
    lote_local_id: number;
    cantidad_disponible: number;
    sede?: Sede;
    lote_local?: LoteLocal;
    producto?: ProductoLocal;
}

export interface VentaFisica {
    id: number;
    sede_id: number;
    user_id: number;
    fecha_venta: string;
    subtotal: number;
    impuesto: number;
    total: number;
    metodo_pago_id: number;
    sede?: Sede;
    user?: User;
    metodo_pago?: MetodoPago;
    detalles?: DetalleVenta[];
}

export interface DetalleVenta {
    id: number;
    venta_id: number;
    producto_sku: string;
    cantidad: number;
    precio_unitario: number;
    subtotal: number;
    venta?: VentaFisica;
    producto?: ProductoLocal;
}
