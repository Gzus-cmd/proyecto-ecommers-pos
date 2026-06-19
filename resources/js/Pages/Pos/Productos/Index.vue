<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import BadgeActivo from '@/Components/pos/BadgeActivo.vue';
import Badge from '@/Components/pos/ui/Badge.vue';
import Button from '@/Components/pos/ui/Button.vue';
import ConfirmDialog from '@/Components/pos/ConfirmDialog.vue';
import { toast } from 'vue-sonner';
import type { PaginatedData, ProductoLocal } from '@/types';

const props = defineProps<{
    productos: PaginatedData<ProductoLocal>;
    search?: string;
}>();

const deleteSku = ref<string | null>(null);

function confirmDelete(sku: string) {
    deleteSku.value = sku;
}

function handleDelete() {
    if (deleteSku.value) {
        router.delete(route('pos.productos.destroy', deleteSku.value), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Producto eliminado correctamente');
                deleteSku.value = null;
            },
            onError: (errors) => {
                toast.error(Object.values(errors).join(', '));
                deleteSku.value = null;
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

const columns = [
    { key: 'sku', label: 'SKU' },
    { key: 'nombre_comercial', label: 'Nombre Comercial' },
    { key: 'precio_venta', label: 'Precio Venta' },
    { key: 'requiere_receta', label: 'Receta' },
    { key: 'activo', label: 'Estado' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Productos" description="Gestión de productos locales">
            <template #actions>
                <Link
                    :href="route('pos.productos.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Producto
                </Link>
            </template>
        </AppPageHeader>

        <DataTable
            :columns="columns"
            :rows="productos.data"
            :total="productos.total"
            :current-page="productos.current_page"
            :last-page="productos.last_page"
            :from="productos.from"
            :to="productos.to"
            :search="search"
            base-route="pos.productos.index"
            search-placeholder="Buscar por SKU, nombre comercial o genérico..."
        >
            <template #cell-precio_venta="{ row }">
                <span>S/ {{ Number((row as unknown as ProductoLocal).precio_venta).toFixed(2) }}</span>
            </template>
            <template #cell-requiere_receta="{ row }">
                <Badge :variant="(row as unknown as ProductoLocal).requiere_receta ? 'warning' : 'default'">
                    {{ (row as unknown as ProductoLocal).requiere_receta ? 'Receta' : 'Venta Libre' }}
                </Badge>
            </template>
            <template #cell-activo="{ row }">
                <BadgeActivo :activo="(row as unknown as ProductoLocal).activo" />
            </template>
            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('pos.productos.edit', (row as unknown as ProductoLocal).sku)"
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium text-gray-400 transition-colors hover:bg-gray-800 hover:text-white"
                    >
                        Editar
                    </Link>
                    <Button variant="ghost" size="sm" @click="confirmDelete((row as unknown as ProductoLocal).sku)">
                        Eliminar
                    </Button>
                </div>
            </template>
        </DataTable>

        <ConfirmDialog
            :open="deleteSku !== null"
            title="Eliminar producto"
            message="¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer."
            variant="danger"
            confirm-text="Eliminar"
            @confirm="handleDelete"
            @cancel="deleteSku = null"
        />
    </AppPageShell>
</template>
