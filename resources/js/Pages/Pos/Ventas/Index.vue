<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import type { PaginatedData, VentaFisica } from '@/types';

defineProps<{
    ventas: PaginatedData<VentaFisica>;
    search?: string;
}>();

const columns = [
    { key: 'id', label: 'N° Venta' },
    { key: 'sede', label: 'Sede' },
    { key: 'user', label: 'Usuario' },
    { key: 'fecha_venta', label: 'Fecha' },
    { key: 'total', label: 'Total' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Ventas" description="Historial de ventas realizadas" />

        <DataTable
            :columns="columns"
            :rows="ventas.data"
            :total="ventas.total"
            :current-page="ventas.current_page"
            :last-page="ventas.last_page"
            :from="ventas.from"
            :to="ventas.to"
            :search="search"
            base-route="pos.ventas.index"
            search-placeholder="Buscar por sede o usuario..."
            :show-search-button="true"
        >
            <template #cell-sede="{ row }">
                <span>{{ (row as unknown as VentaFisica).sede?.nombre || '-' }}</span>
            </template>
            <template #cell-user="{ row }">
                <span>{{ (row as unknown as VentaFisica).user?.name || '-' }}</span>
            </template>
            <template #cell-fecha_venta="{ row }">
                <span>{{ new Date((row as unknown as VentaFisica).fecha_venta).toLocaleDateString() }}</span>
            </template>
            <template #cell-total="{ row }">
                <span class="font-semibold text-white">S/ {{ Number((row as unknown as VentaFisica).total).toFixed(2) }}</span>
            </template>
            <template #actions="{ row }">
                <Link
                    :href="route('pos.ventas.show', (row as unknown as VentaFisica).id)"
                    class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium text-gray-400 transition-colors hover:bg-gray-800 hover:text-white"
                >
                    Ver detalle
                </Link>
            </template>
        </DataTable>
    </AppPageShell>
</template>
