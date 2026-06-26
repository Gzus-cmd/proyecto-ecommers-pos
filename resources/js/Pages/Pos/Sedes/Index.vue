<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import BadgeActivo from '@/Components/pos/BadgeActivo.vue';
import Button from '@/Components/pos/ui/Button.vue';
import ConfirmDialog from '@/Components/pos/ConfirmDialog.vue';
import { toast } from 'vue-sonner';
import type { PaginatedData, Sede } from '@/types';

const props = defineProps<{
    sedes: PaginatedData<Sede>;
    search?: string;
}>();

const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
}

function handleDelete() {
    if (deleteId.value) {
        router.delete(route('pos.sedes.destroy', deleteId.value), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Sede eliminada correctamente');
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

const columns = [
    { key: 'codigo', label: 'Código' },
    { key: 'nombre', label: 'Nombre' },
    { key: 'direccion', label: 'Dirección' },
    { key: 'telefono', label: 'Teléfono' },
    { key: 'activo', label: 'Estado' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Sedes" description="Gestión de sedes del POS">
            <template #actions>
                <Link
                    :href="route('pos.sedes.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Sede
                </Link>
            </template>
        </AppPageHeader>

        <DataTable
            :columns="columns"
            :rows="sedes.data"
            :total="sedes.total"
            :current-page="sedes.current_page"
            :last-page="sedes.last_page"
            :from="sedes.from"
            :to="sedes.to"
            :search="search"
            base-route="pos.sedes.index"
            search-placeholder="Buscar por nombre o código..."
            :show-search-button="true"
        >
            <template #cell-activo="{ row }">
                <BadgeActivo :activo="(row as unknown as Sede).activo" />
            </template>
            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('pos.sedes.edit', (row as unknown as Sede).id)"
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium text-gray-400 transition-colors hover:bg-gray-800 hover:text-white"
                    >
                        Editar
                    </Link>
                    <Button
                        variant="ghost"
                        size="sm"
                        @click="confirmDelete((row as unknown as Sede).id)"
                    >
                        Eliminar
                    </Button>
                </div>
            </template>
        </DataTable>

        <ConfirmDialog
            :open="deleteId !== null"
            title="Eliminar sede"
            message="¿Estás seguro de eliminar esta sede? Esta acción no se puede deshacer."
            variant="danger"
            confirm-text="Eliminar"
            @confirm="handleDelete"
            @cancel="deleteId = null"
        />
    </AppPageShell>
</template>
