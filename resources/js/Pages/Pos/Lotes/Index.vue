<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import Badge from '@/Components/pos/ui/Badge.vue';
import Button from '@/Components/pos/ui/Button.vue';
import ConfirmDialog from '@/Components/pos/ConfirmDialog.vue';
import { toast } from 'vue-sonner';
import type { PaginatedData, LoteLocal } from '@/types';

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
    if (page.props.flash?.success) toast.success(page.props.flash.success);
    if (page.props.flash?.error) toast.error(page.props.flash.error);
}
showFlash();

function isExpired(date: string): boolean {
    return new Date(date) < new Date();
}

const columns = [
    { key: 'numero_lote', label: 'N° Lote' },
    { key: 'producto', label: 'Producto' },
    { key: 'sku_producto', label: 'SKU' },
    { key: 'fecha_vencimiento', label: 'Vencimiento' },
    { key: 'cantidad_disponible', label: 'Cantidad' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Lotes" description="Gestión de lotes de productos">
            <template #actions>
                <Link
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
        >
            <template #cell-producto="{ row }">
                <span>{{ (row as unknown as LoteLocal).producto?.nombre_comercial || '-' }}</span>
            </template>
            <template #cell-fecha_vencimiento="{ row }">
                <div class="flex items-center gap-2">
                    <span>{{ (row as unknown as LoteLocal).fecha_vencimiento }}</span>
                    <Badge v-if="isExpired((row as unknown as LoteLocal).fecha_vencimiento)" variant="danger">
                        Vencido
                    </Badge>
                </div>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('pos.lotes.edit', (row as unknown as LoteLocal).id)"
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium text-gray-400 transition-colors hover:bg-gray-800 hover:text-white"
                    >
                        Editar
                    </Link>
                    <Button variant="ghost" size="sm" @click="confirmDelete((row as unknown as LoteLocal).id)">
                        Eliminar
                    </Button>
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
