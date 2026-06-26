<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import DataTable from '@/Components/pos/DataTable.vue';
import type { PaginatedData, User } from '@/types';

defineProps<{
    users: PaginatedData<User>;
    search?: string;
}>();

const columns = [
    { key: 'name', label: 'Nombre' },
    { key: 'email', label: 'Email' },
    { key: 'created_at', label: 'Creado' },
];
</script>

<template>
    <AppPageShell>
        <Head title="Usuarios" />
        <AppPageHeader title="Usuarios" description="Gestión de usuarios del sistema" />

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
            <template #cell-created_at="{ row }">
                <span>{{ new Date((row as unknown as User).created_at ?? '').toLocaleDateString() }}</span>
            </template>
        </DataTable>
    </AppPageShell>
</template>
