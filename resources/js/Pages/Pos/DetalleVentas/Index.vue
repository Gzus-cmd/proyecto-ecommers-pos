<script setup lang="ts">
/**
 * DetalleVentas/Index.vue
 *
 * Página de detalle de ventas (visión global). Muestra tabla paginada
 * con todas las líneas de detalle de ventas registradas: venta #,
 * producto, SKU, cantidad, precio unitario y subtotal. Permite ir
 * al detalle de cada venta desde el enlace en el número de venta.
 *
 * Props:
 * - detalles: Datos paginados de detalles de venta
 * - search: Término de búsqueda actual (opcional)
 */
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import type { PaginatedData, DetalleVenta } from '@/types';

defineProps<{
    detalles: PaginatedData<DetalleVenta>;
    search?: string;
}>();

/** Columnas de la tabla de detalle de ventas */
const columns = [
    { key: 'venta_id', label: 'Venta #' },
    { key: 'producto', label: 'Producto' },
    { key: 'producto_sku', label: 'SKU' },
    { key: 'cantidad', label: 'Cantidad' },
    { key: 'precio_unitario', label: 'Precio Unit.' },
    { key: 'subtotal', label: 'Subtotal' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Detalle de Ventas" description="Líneas de detalle de todas las ventas" />

        <DataTable
            :columns="columns"
            :rows="detalles.data"
            :total="detalles.total"
            :current-page="detalles.current_page"
            :last-page="detalles.last_page"
            :from="detalles.from"
            :to="detalles.to"
            :search="search"
            base-route="pos.detalle-ventas.index"
            search-placeholder="Buscar por producto o venta..."
            :show-search-button="true"
        >
            <template #cell-venta_id="{ row }">
                <a
                    :href="route('pos.ventas.show', (row as unknown as DetalleVenta).venta_id)"
                    class="text-blue-400 hover:text-blue-300"
                >
                    #{{ (row as unknown as DetalleVenta).venta_id }}
                </a>
            </template>
            <template #cell-producto="{ row }">
                <span>{{ (row as unknown as DetalleVenta).producto?.nombre_comercial || '-' }}</span>
            </template>
            <template #cell-precio_unitario="{ row }">
                <span>S/ {{ Number((row as unknown as DetalleVenta).precio_unitario).toFixed(2) }}</span>
            </template>
            <template #cell-subtotal="{ row }">
                <span>S/ {{ Number((row as unknown as DetalleVenta).subtotal).toFixed(2) }}</span>
            </template>
        </DataTable>
    </AppPageShell>
</template>
