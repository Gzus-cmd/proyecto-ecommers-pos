<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from '@/lib/route';
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
    { key: 'user', label: 'Usuario' },
    { key: 'fecha_venta', label: 'Fecha' },
    { key: 'total', label: 'Total' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Ventas" description="Historial de ventas realizadas">
            <template #actions>
                <a
                    :href="route('pos.ventas.exportar')"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-gray-800 px-4 py-2 text-sm font-medium text-gray-300 shadow-sm transition-colors hover:bg-gray-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar CSV
                </a>
                <Link
                    :href="route('pos.ventas.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Venta
                </Link>
            </template>
        </AppPageHeader>

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
            search-placeholder="Buscar por usuario..."
            :show-search-button="true"
        >
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
                    class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-blue-500/10 text-blue-400 hover:bg-blue-500/20"
                >
                    Ver detalle
                </Link>
            </template>
        </DataTable>
    </AppPageShell>
</template>
