<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';

interface MetricCard {
    label: string;
    value: number;
    icon: string;
    color: string;
}

const props = defineProps<{
    totalProductos: number;
    ventasHoy: number;
    sedesActivas: number;
    stockBajo: number;
}>();

const cards: MetricCard[] = [
    {
        label: 'Total Productos',
        value: props.totalProductos,
        icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        color: 'blue',
    },
    {
        label: 'Ventas Hoy',
        value: props.ventasHoy,
        icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
        color: 'emerald',
    },
    {
        label: 'Sedes Activas',
        value: props.sedesActivas,
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        color: 'violet',
    },
    {
        label: 'Stock Bajo',
        value: props.stockBajo,
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z',
        color: 'red',
    },
];
</script>

<template>
    <AppPageShell>
        <Head title="Dashboard" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-400">Resumen del sistema</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="card in cards"
                :key="card.label"
                class="group relative overflow-hidden rounded-xl border border-gray-800 bg-gray-900 p-6 transition-colors hover:bg-gray-800/50"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">{{ card.label }}</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ card.value }}</p>
                    </div>
                    <div
                        :class="[
                            'flex h-12 w-12 items-center justify-center rounded-lg',
                            card.color === 'blue' && 'bg-blue-600/10 text-blue-400',
                            card.color === 'emerald' && 'bg-emerald-600/10 text-emerald-400',
                            card.color === 'violet' && 'bg-violet-600/10 text-violet-400',
                            card.color === 'red' && 'bg-red-600/10 text-red-400',
                        ]"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </AppPageShell>
</template>
