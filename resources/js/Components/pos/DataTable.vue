<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import SearchInput from '@/Components/pos/SearchInput.vue';
import EmptyState from '@/Components/pos/EmptyState.vue';
import { route } from '@/lib/route';

interface Column {
    key: string;
    label: string;
    sortable?: boolean;
}

const props = withDefaults(
    defineProps<{
        columns: Column[];
        rows: Record<string, unknown>[];
        total: number;
        currentPage: number;
        lastPage: number;
        from: number | null;
        to: number | null;
        search?: string;
        baseRoute: string;
        searchPlaceholder?: string;
        showSearchButton?: boolean;
    }>(),
    {
        search: '',
        searchPlaceholder: 'Buscar...',
        showSearchButton: false,
    },
);

function visit(url: string | null) {
    if (url) {
        router.get(url, { search: props.search || undefined }, { preserveState: true, preserveScroll: true });
    }
}

function onSearch(value: string) {
    router.get(
        route(props.baseRoute),
        { search: value || undefined },
        { preserveState: true, preserveScroll: true },
    );
}
</script>

<template>
    <div>
        <div class="mb-4">
            <slot name="toolbar">
                <div class="flex items-center justify-between gap-4">
                    <div class="w-72">
                        <SearchInput
                            :model-value="search"
                            :placeholder="searchPlaceholder"
                            :show-button="showSearchButton"
                            @update:model-value="onSearch"
                        />
                    </div>
                    <div>
                        <slot name="header-actions" />
                    </div>
                </div>
            </slot>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-800">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400"
                        >
                            {{ col.label }}
                        </th>
                        <th v-if="$slots.actions" class="relative px-4 py-3">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    <tr v-for="(row, i) in rows" :key="i" class="hover:bg-gray-800/50">
                        <td
                            v-for="col in columns"
                            :key="col.key"
                            class="whitespace-nowrap px-4 py-3 text-sm text-gray-300"
                        >
                            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                {{ row[col.key] != null ? String(row[col.key]) : '-' }}
                            </slot>
                        </td>
                        <td v-if="$slots.actions" class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <slot name="actions" :row="row" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="rows.length === 0" class="border-t border-gray-800">
                <EmptyState>
                    <template #default>
                        <slot name="empty" />
                    </template>
                </EmptyState>
            </div>
        </div>

        <div v-if="lastPage > 1" class="mt-4 flex items-center justify-between">
            <p class="text-sm text-gray-400">
                Mostrando {{ from }}–{{ to }} de {{ total }} registros
            </p>
            <div class="flex gap-2">
                <button
                    :disabled="currentPage <= 1"
                    class="rounded-lg border border-gray-700 px-3 py-1.5 text-sm text-gray-300 transition-colors hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="currentPage > 1 ? visit(`${route(baseRoute)}?page=${currentPage - 1}`) : null"
                >
                    Anterior
                </button>
                <button
                    v-for="link in lastPage"
                    :key="link"
                    :class="[
                        'rounded-lg border px-3 py-1.5 text-sm transition-colors',
                        link === currentPage
                            ? 'border-blue-600 bg-blue-600 text-white'
                            : 'border-gray-700 text-gray-300 hover:bg-gray-800',
                    ]"
                    @click="visit(`${route(baseRoute)}?page=${link}`)"
                >
                    {{ link }}
                </button>
                <button
                    :disabled="currentPage >= lastPage"
                    class="rounded-lg border border-gray-700 px-3 py-1.5 text-sm text-gray-300 transition-colors hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="currentPage < lastPage ? visit(`${route(baseRoute)}?page=${currentPage + 1}`) : null"
                >
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</template>
