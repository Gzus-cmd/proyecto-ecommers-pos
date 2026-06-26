<script setup lang="ts">
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import Badge from '@/Components/pos/ui/Badge.vue';
import type { PaginatedData } from '@/types';

interface StockRow {
    id: number;
    sku_producto: string;
    numero_lote: string;
    fecha_vencimiento: string;
    cantidad_inicial: number;
    stock_actual: number;
    producto_nombre: string;
}

defineProps<{
    stock: PaginatedData<StockRow>;
    search?: string;
}>();

const columns = [
    { key: 'producto_nombre', label: 'Producto' },
    { key: 'sku_producto', label: 'SKU' },
    { key: 'numero_lote', label: 'N° Lote' },
    { key: 'fecha_vencimiento', label: 'Vencimiento' },
    { key: 'cantidad_inicial', label: 'Cant. Inicial' },
    { key: 'stock_actual', label: 'Stock Actual' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Stock" description="Inventario de productos — stock calculado por lote" />

        <DataTable
            :columns="columns"
            :rows="stock.data"
            :total="stock.total"
            :current-page="stock.current_page"
            :last-page="stock.last_page"
            :from="stock.from"
            :to="stock.to"
            :search="search"
            base-route="pos.stock.index"
            search-placeholder="Buscar por producto o SKU..."
            :show-search-button="true"
        >
            <template #cell-stock_actual="{ row }">
                <Badge :variant="(row as unknown as StockRow).stock_actual > 0 ? 'success' : 'danger'">
                    {{ (row as unknown as StockRow).stock_actual }}
                </Badge>
            </template>
        </DataTable>
    </AppPageShell>
</template>
