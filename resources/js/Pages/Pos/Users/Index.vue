<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import BadgeActivo from '@/Components/pos/BadgeActivo.vue';
import ConfirmDialog from '@/Components/pos/ConfirmDialog.vue';
import { toast } from 'vue-sonner';
import type { PaginatedData, User } from '@/types';

const isAdmin = computed(() => usePage().props.auth?.user?.roles?.includes('admin') ?? false);

const props = defineProps<{
    users: PaginatedData<User>;
    search?: string;
}>();

const deleteId = ref<number | null>(null);

function confirmDeactivate(id: number) {
    deleteId.value = id;
}

function handleDeactivate() {
    if (deleteId.value) {
        router.delete(route('pos.users.destroy', deleteId.value), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Usuario desactivado correctamente');
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
    { key: 'name', label: 'Nombre' },
    { key: 'email', label: 'Email' },
    { key: 'roles', label: 'Roles' },
    { key: 'activo', label: 'Estado' },
    { key: 'created_at', label: 'Creado' },
];
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Usuarios" description="Gestión de usuarios del sistema">
            <template #actions>
                <Link
                    :href="route('pos.users.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Usuario
                </Link>
            </template>
        </AppPageHeader>

        <DataTable
            :columns="columns"
            :rows="users.data"
            :total="users.total"
            :current-page="users.current_page"
            :last-page="users.last_page"
            :from="users.from"
            :to="users.to"
            :search="search"
            base-route="pos.users.index"
            search-placeholder="Buscar por nombre o email..."
            :show-search-button="true"
        >
            <template #cell-activo="{ row }">
                <BadgeActivo :activo="(row as unknown as User).activo" />
            </template>
            <template #cell-roles="{ row }">
                <span>{{ ((row as unknown as User).roles ?? []).map(r => r.name).join(', ') || '—' }}</span>
            </template>
            <template #cell-created_at="{ row }">
                <span>{{ new Date((row as unknown as User).created_at ?? '').toLocaleDateString() }}</span>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('pos.users.edit', (row as unknown as User).id)"
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-amber-500/10 text-amber-400 hover:bg-amber-500/20"
                    >
                        Editar
                    </Link>
                    <button
                        class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors"
                        @click="confirmDeactivate((row as unknown as User).id)"
                    >
                        Desactivar
                    </button>
                </div>
            </template>
        </DataTable>

        <ConfirmDialog
            :open="deleteId !== null"
            title="Desactivar usuario"
            message="¿Estás seguro de desactivar este usuario? El usuario no podrá iniciar sesión."
            variant="danger"
            confirm-text="Desactivar"
            @confirm="handleDeactivate"
            @cancel="deleteId = null"
        />
    </AppPageShell>
</template>
