<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import BadgeActivo from '@/Components/pos/BadgeActivo.vue';
import ConfirmDialog from '@/Components/pos/ConfirmDialog.vue';
import { toast } from 'vue-sonner';
import type { PaginatedData, MetodoPago } from '@/types';

const props = defineProps<{
    metodos: PaginatedData<MetodoPago>;
    search?: string;
}>();

const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
}

function handleDelete() {
    if (deleteId.value) {
        router.delete(route('pos.metodos-pago.destroy', deleteId.value), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Método de pago eliminado correctamente');
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
    if (page?.props?.errors && Object.keys(page.props.errors).length > 0) {
        toast.error(Object.values(page.props.errors).join(', '));
    }
}
onMounted(() => showFlash());

const columns = [
    { key: 'nombre', label: 'Nombre' },
    { key: 'numero_cuenta', label: 'N° Cuenta' },
    { key: 'titular', label: 'Titular' },
    { key: 'activo', label: 'Estado' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Métodos de Pago" description="Gestión de métodos de pago">
            <template #actions>
                <Link
                    :href="route('pos.metodos-pago.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Método
                </Link>
            </template>
        </AppPageHeader>

        <DataTable
            :columns="columns"
            :rows="metodos.data"
            :total="metodos.total"
            :current-page="metodos.current_page"
            :last-page="metodos.last_page"
            :from="metodos.from"
            :to="metodos.to"
            :search="search"
            base-route="pos.metodos-pago.index"
            search-placeholder="Buscar por nombre..."
            :show-search-button="true"
        >
            <template #cell-numero_cuenta="{ row }">
                <span class="text-gray-400">{{ (row as unknown as MetodoPago).numero_cuenta || '—' }}</span>
            </template>
            <template #cell-titular="{ row }">
                <span class="text-gray-400">{{ (row as unknown as MetodoPago).titular || '—' }}</span>
            </template>
            <template #cell-activo="{ row }">
                <BadgeActivo :activo="(row as unknown as MetodoPago).activo" />
            </template>
            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('pos.metodos-pago.edit', (row as unknown as MetodoPago).id)"
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-amber-500/10 text-amber-400 hover:bg-amber-500/20"
                    >
                        Editar
                    </Link>
                    <button
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors"
                        @click="confirmDelete((row as unknown as MetodoPago).id)"
                    >
                        Eliminar
                    </button>
                </div>
            </template>
        </DataTable>

        <ConfirmDialog
            :open="deleteId !== null"
            title="Eliminar método de pago"
            message="¿Estás seguro de eliminar este método de pago? Esta acción no se puede deshacer."
            variant="danger"
            confirm-text="Eliminar"
            @confirm="handleDelete"
            @cancel="deleteId = null"
        />
    </AppPageShell>
</template>
