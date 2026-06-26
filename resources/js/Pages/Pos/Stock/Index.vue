<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import Badge from '@/Components/pos/ui/Badge.vue';
import Button from '@/Components/pos/ui/Button.vue';
import { route } from '@/lib/route';
import { toast } from 'vue-sonner';
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

function estadoLote(row: StockRow): { label: string; variant: string } {
    const hoy = new Date();
    const vence = new Date(row.fecha_vencimiento + 'T00:00:00');
    const diff = Math.ceil((vence.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24));

    if (diff < 0) return { label: 'Vencido', variant: 'danger' };
    if (diff <= 30) return { label: 'Por Vencer', variant: 'warning' };
    return { label: 'Vigente', variant: 'success' };
}

function retirarStock(loteId: number) {
    if (!confirm('¿Retirar todo el stock de este lote? Se marcará como 0.')) return;

    router.post(route('pos.stock.retirar', { lote: loteId }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Stock retirado correctamente.');
        },
        onError: () => {
            toast.error('Error al retirar el stock.');
        },
    });
}

const columns = [
    { key: 'producto_nombre', label: 'Producto' },
    { key: 'sku_producto', label: 'SKU' },
    { key: 'numero_lote', label: 'N° Lote' },
    { key: 'fecha_vencimiento', label: 'Vencimiento' },
    { key: 'estado', label: 'Estado' },
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
            <template #cell-estado="{ row }">
                <Badge :variant="estadoLote(row as unknown as StockRow).variant">
                    {{ estadoLote(row as unknown as StockRow).label }}
                </Badge>
            </template>
            <template #cell-stock_actual="{ row }">
                <Badge :variant="(row as unknown as StockRow).stock_actual > 0 ? 'success' : 'danger'">
                    {{ (row as unknown as StockRow).stock_actual }}
                </Badge>
            </template>
            <template #actions="{ row }">
                <button
                    v-if="estadoLote(row as unknown as StockRow).label === 'Vencido'"
                    class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors"
                    @click="retirarStock((row as unknown as StockRow).id)"
                >
                    Retirar Stock
                </button>
            </template>
        </DataTable>
    </AppPageShell>
</template>
