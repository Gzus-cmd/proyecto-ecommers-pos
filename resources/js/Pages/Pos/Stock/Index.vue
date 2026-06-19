<script setup lang="ts">
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import Badge from '@/Components/pos/ui/Badge.vue';
import type { PaginatedData, StockLocal } from '@/types';

defineProps<{
    stock: PaginatedData<StockLocal>;
    search?: string;
}>();

const columns = [
    { key: 'sede', label: 'Sede' },
    { key: 'producto', label: 'Producto' },
    { key: 'sku', label: 'SKU' },
    { key: 'numero_lote', label: 'N° Lote' },
    { key: 'cantidad_disponible', label: 'Cantidad' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Stock" description="Inventario de productos por sede" />

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
            search-placeholder="Buscar por producto o sede..."
        >
            <template #cell-sede="{ row }">
                <span>{{ (row as unknown as StockLocal).sede?.nombre || '-' }}</span>
            </template>
            <template #cell-producto="{ row }">
                <span>{{ (row as unknown as StockLocal).lote_local?.producto?.nombre_comercial || '-' }}</span>
            </template>
            <template #cell-sku="{ row }">
                <span>{{ (row as unknown as StockLocal).lote_local?.sku_producto || '-' }}</span>
            </template>
            <template #cell-numero_lote="{ row }">
                <span>{{ (row as unknown as StockLocal).lote_local?.numero_lote || '-' }}</span>
            </template>
            <template #cell-cantidad_disponible="{ row }">
                <Badge :variant="(row as unknown as StockLocal).cantidad_disponible > 0 ? 'success' : 'danger'">
                    {{ (row as unknown as StockLocal).cantidad_disponible }}
                </Badge>
            </template>
        </DataTable>
    </AppPageShell>
</template>
