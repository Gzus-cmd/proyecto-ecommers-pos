<script setup lang="ts">
/**
 * Ventas/Index.vue
 *
 * Página de historial de ventas. Muestra tabla paginada con número de
 * venta, usuario, fecha y total. Permite ver detalle de cada venta,
 * exportar a CSV con filtro por rango de fechas, y crear nuevas ventas.
 *
 * Props:
 * - ventas: Datos paginados de ventas (PaginatedData<VentaFisica>)
 * - search: Término de búsqueda actual (opcional)
 */
import { ref } from 'vue';
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

/** Columnas de la tabla de ventas */
const columns = [
    { key: 'id', label: 'N° Venta' },
    { key: 'user', label: 'Usuario' },
    { key: 'fecha_venta', label: 'Fecha' },
    { key: 'total', label: 'Total' },
];

/** Controla la visibilidad del modal de exportación CSV */
const showModal = ref(false);
const fechaInicio = ref('');
const fechaFin = ref('');

/** Genera y descarga el CSV de ventas con filtro de fechas opcional */
function exportarCsv() {
    const params = new URLSearchParams();
    if (fechaInicio.value) params.set('fecha_inicio', fechaInicio.value);
    if (fechaFin.value) params.set('fecha_fin', fechaFin.value);
    const qs = params.toString();
    const url = route('pos.ventas.exportar') + (qs ? `?${qs}` : '');
    window.location.href = url;
    showModal.value = false;
}

/** Abre el modal de exportación CSV */
function abrirModal() {
    fechaInicio.value = '';
    fechaFin.value = '';
    showModal.value = true;
}
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Ventas" description="Historial de ventas realizadas">
            <template #actions>
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-gray-800 px-4 py-2 text-sm font-medium text-gray-300 shadow-sm transition-colors hover:bg-gray-700"
                    @click="abrirModal"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.707.293V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar CSV
                </button>
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

    <!-- Modal Exportar CSV -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="w-full max-w-sm rounded-xl border border-gray-700 bg-gray-900 p-6 shadow-2xl">
            <h3 class="mb-1 text-lg font-bold text-white">Exportar Ventas</h3>
            <p class="mb-5 text-sm text-gray-400">Seleccioná un rango de fechas para exportar. Si no elegís fechas, se exportan todas las ventas.</p>

            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Desde</label>
                    <input
                        v-model="fechaInicio"
                        type="date"
                        class="block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Hasta</label>
                    <input
                        v-model="fechaFin"
                        type="date"
                        class="block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-300 transition-colors hover:bg-gray-800"
                    @click="showModal = false"
                >
                    Cancelar
                </button>
                <button
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                    @click="exportarCsv"
                >
                    Exportar
                </button>
            </div>
        </div>
    </div>
</template>
