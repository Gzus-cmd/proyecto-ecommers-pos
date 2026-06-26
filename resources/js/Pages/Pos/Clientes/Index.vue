<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import ConfirmDialog from '@/Components/pos/ConfirmDialog.vue';
import { toast } from 'vue-sonner';
import type { PaginatedData, Cliente } from '@/types';

const props = defineProps<{
    clientes: PaginatedData<Cliente>;
    search?: string;
}>();

const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
}

function handleDelete() {
    if (deleteId.value) {
        router.delete(route('pos.clientes.destroy', deleteId.value), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Cliente eliminado correctamente');
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
    { key: 'dni', label: 'DNI' },
    { key: 'nombres', label: 'Nombres' },
    { key: 'apellidos', label: 'Apellidos' },
    { key: 'telefono', label: 'Teléfono' },
    { key: 'email', label: 'Email' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Clientes" description="Gestión de clientes">
            <template #actions>
                <Link
                    :href="route('pos.clientes.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Cliente
                </Link>
            </template>
        </AppPageHeader>

        <DataTable
            :columns="columns"
            :rows="clientes.data"
            :total="clientes.total"
            :current-page="clientes.current_page"
            :last-page="clientes.last_page"
            :from="clientes.from"
            :to="clientes.to"
            :search="search"
            base-route="pos.clientes.index"
            search-placeholder="Buscar por nombre, apellido o DNI..."
            :show-search-button="true"
        >
            <template #cell-telefono="{ row }">
                <span class="text-gray-400">{{ (row as unknown as Cliente).telefono || '—' }}</span>
            </template>
            <template #cell-email="{ row }">
                <span class="text-gray-400">{{ (row as unknown as Cliente).email || '—' }}</span>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('pos.clientes.edit', (row as unknown as Cliente).id)"
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-amber-500/10 text-amber-400 hover:bg-amber-500/20"
                    >
                        Editar
                    </Link>
                    <button
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors"
                        @click="confirmDelete((row as unknown as Cliente).id)"
                    >
                        Eliminar
                    </button>
                </div>
            </template>
        </DataTable>

        <ConfirmDialog
            :open="deleteId !== null"
            title="Eliminar cliente"
            message="¿Estás seguro de eliminar este cliente? Esta acción no se puede deshacer."
            variant="danger"
            confirm-text="Eliminar"
            @confirm="handleDelete"
            @cancel="deleteId = null"
        />
    </AppPageShell>
</template>
