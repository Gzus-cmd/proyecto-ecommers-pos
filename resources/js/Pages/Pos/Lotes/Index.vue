<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import Badge from '@/Components/pos/ui/Badge.vue';
import ConfirmDialog from '@/Components/pos/ConfirmDialog.vue';
import { toast } from 'vue-sonner';
import type { PaginatedData, LoteLocal } from '@/types';

const isAdmin = computed(() => usePage().props.auth?.user?.roles?.includes('admin') ?? false);

const props = defineProps<{
    lotes: PaginatedData<LoteLocal>;
    search?: string;
}>();

const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
}

function handleDelete() {
    if (deleteId.value) {
        router.delete(route('pos.lotes.destroy', deleteId.value), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Lote eliminado correctamente');
                deleteId.value = null;
            },
            onError: (errors) => {
                toast.error(Object.values(errors).join(', '));
                deleteId.value = null;
            },
        });
    }
}

function showFlash() {
    const page = (router as any).page;
    if (page?.props?.flash?.success) toast.success(page.props.flash.success);
    if (page?.props?.flash?.error) toast.error(page.props.flash.error);
}
onMounted(() => showFlash());

function getEstadoDias(fecha: string): { label: string; clase: string; dias: number } {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const venc = new Date(fecha);
    venc.setHours(0, 0, 0, 0);
    const diff = Math.floor((venc.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24));
    if (diff <= 0) return { label: 'Vencido', clase: 'text-red-400 bg-red-900/20 border-red-800/50', dias: diff };
    if (diff <= 90) return { label: 'Por Vencer', clase: 'text-yellow-400 bg-yellow-900/20 border-yellow-800/50', dias: diff };
    return { label: 'Vigente', clase: 'text-emerald-400 bg-emerald-900/20 border-emerald-800/50', dias: diff };
}

function formatDate(date: string): string {
    if (!date) return '-';
    const d = new Date(date);
    return isNaN(d.getTime()) ? '-' : d.toLocaleDateString('es-PE');
}

const columns = [
    { key: 'numero_lote', label: 'N° Lote' },
    { key: 'producto', label: 'Producto' },
    { key: 'sku_producto', label: 'SKU' },
    { key: 'fecha_vencimiento', label: 'Vencimiento' },
    { key: 'estado', label: 'Estado' },
    { key: 'cantidad_disponible', label: 'Cantidad' },
    { key: 'user', label: 'Registrado por' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Lotes" description="Gestión de lotes de productos">
            <template #actions>
                <Link
                    v-if="isAdmin"
                    :href="route('pos.lotes.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Lote
                </Link>
            </template>
        </AppPageHeader>

        <DataTable
            :columns="columns"
            :rows="lotes.data"
            :total="lotes.total"
            :current-page="lotes.current_page"
            :last-page="lotes.last_page"
            :from="lotes.from"
            :to="lotes.to"
            :search="search"
            base-route="pos.lotes.index"
            search-placeholder="Buscar por número de lote o SKU..."
            :show-search-button="true"
        >
            <template #cell-producto="{ row }">
                <span>{{ (row as unknown as LoteLocal).producto?.nombre_comercial || '-' }}</span>
            </template>
            <template #cell-fecha_vencimiento="{ row }">
                <span>{{ formatDate((row as unknown as LoteLocal).fecha_vencimiento) }}</span>
            </template>
            <template #cell-estado="{ row }">
                <div class="flex items-center gap-2">
                    <span
                        :class="[
                            'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium',
                            getEstadoDias((row as unknown as LoteLocal).fecha_vencimiento).clase,
                        ]"
                    >
                        {{ getEstadoDias((row as unknown as LoteLocal).fecha_vencimiento).label }}
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ getEstadoDias((row as unknown as LoteLocal).fecha_vencimiento).dias > 0
                            ? getEstadoDias((row as unknown as LoteLocal).fecha_vencimiento).dias + 'd'
                            : '' }}
                    </span>
                </div>
            </template>
            <template #cell-user="{ row }">
                <span class="text-gray-400">{{ (row as unknown as LoteLocal).user?.name || '—' }}</span>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('pos.lotes.show', (row as unknown as LoteLocal).id)"
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-blue-500/10 text-blue-400 hover:bg-blue-500/20"
                    >
                        Ver
                    </Link>
                    <Link
                        v-if="isAdmin"
                        :href="route('pos.lotes.edit', (row as unknown as LoteLocal).id)"
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-amber-500/10 text-amber-400 hover:bg-amber-500/20"
                    >
                        Editar
                    </Link>
                    <button
                        v-if="isAdmin"
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors"
                        @click="confirmDelete((row as unknown as LoteLocal).id)"
                    >
                        Eliminar
                    </button>
                </div>
            </template>
        </DataTable>

        <ConfirmDialog
            :open="deleteId !== null"
            title="Eliminar lote"
            message="¿Estás seguro de eliminar este lote? Esta acción no se puede deshacer."
            variant="danger"
            confirm-text="Eliminar"
            @confirm="handleDelete"
            @cancel="deleteId = null"
        />
    </AppPageShell>
</template>
