<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import { router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';

interface ProductoVencer {
    sku: string;
    nombre_comercial: string;
    fecha_vencimiento: string;
    dias_restantes: number;
}

interface VentaDia {
    fecha: string;
    total: number;
    monto: number;
}

interface StockBajoItem {
    producto: string;
    sku: string;
    cantidad: number;
    lote: string;
    sede: string;
}

const props = defineProps<{
    totalProductos: number;
    ventasHoy: number;
    ventasHoyMonto: number;
    stockBajo: number;
    stockBajoProductos: StockBajoItem[];
    productosPorVencer: ProductoVencer[];
    productosPorVencerCount: number;
    ventasPorDia: VentaDia[];
    productosPorEstado: { activos: number; inactivos: number };
}>();

const showVencerModal = ref(false);
const showStockBajoModal = ref(false);

const productosVencidos = computed(() =>
    props.productosPorVencer.filter((p) => p.dias_restantes <= 0),
);
const productosProximos = computed(() =>
    props.productosPorVencer.filter((p) => p.dias_restantes > 0),
);

function diasColor(dias: number): string {
    if (dias <= 0) return 'text-red-400';
    if (dias <= 7) return 'text-orange-400';
    if (dias <= 15) return 'text-yellow-400';
    return 'text-emerald-400';
}

function diasLabel(dias: number): string {
    if (dias <= 0) return 'Vencido';
    if (dias === 1) return '1 día';
    return `${dias} días`;
}

const cards = [
    {
        label: 'Total Productos',
        value: props.totalProductos,
        icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        color: 'blue',
    },
    {
        label: 'Ventas Hoy',
        value: props.ventasHoy,
        subtitle: `S/ ${props.ventasHoyMonto.toFixed(2)}`,
        icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
        color: 'emerald',
    },
    {
        label: 'Productos por Vencer',
        value: props.productosPorVencerCount,
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'amber',
        clickable: true,
    },
    {
        label: 'Stock Bajo',
        value: props.stockBajo,
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z',
        color: 'red',
        clickable: true,
    },
];

const totalProductos = computed(() => props.productosPorEstado.activos + props.productosPorEstado.inactivos);
const donutPercentage = computed(() =>
    totalProductos.value > 0 ? (props.productosPorEstado.activos / totalProductos.value) * 100 : 0,
);
const donutCircumference = 2 * Math.PI * 40;
const donutOffset = computed(() => donutCircumference - (donutPercentage.value / 100) * donutCircumference);

const maxVentas = computed(() => Math.max(...props.ventasPorDia.map((d) => d.total), 1));

function formatDate(fecha: string): string {
    const d = new Date(fecha + 'T00:00:00');
    return d.toLocaleDateString('es', { weekday: 'short' });
}

function cardClicked(card: typeof cards[0]) {
    if (card.label === 'Productos por Vencer') {
        showVencerModal.value = true;
    }
    if (card.label === 'Stock Bajo') {
        showStockBajoModal.value = true;
    }
}
</script>

<template>
    <AppPageShell>
        <Head title="Dashboard" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-400">Resumen del sistema</p>
        </div>

        <!-- Metric cards -->
        <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="card in cards"
                :key="card.label"
                :class="[
                    'group relative overflow-hidden rounded-xl border border-gray-800 bg-gray-900 p-6 transition-colors',
                    card.clickable ? 'cursor-pointer hover:bg-gray-800/50' : '',
                ]"
                @click="cardClicked(card)"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">{{ card.label }}</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ card.value }}</p>
                        <p v-if="'subtitle' in card && card.subtitle" class="mt-1 text-xs text-gray-500">{{ card.subtitle }}</p>
                    </div>
                    <div
                        :class="[
                            'flex h-12 w-12 items-center justify-center rounded-lg',
                            card.color === 'blue' && 'bg-blue-600/10 text-blue-400',
                            card.color === 'emerald' && 'bg-emerald-600/10 text-emerald-400',
                            card.color === 'amber' && 'bg-amber-600/10 text-amber-400',
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

        <!-- Charts row -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Bar chart: Ventas por día -->
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h3 class="mb-1 text-base font-semibold text-white">Ventas por Día</h3>
                <p class="mb-6 text-sm text-gray-400">Últimos 7 días</p>

                <div class="flex items-end justify-between gap-3" style="height: 160px">
                    <div
                        v-for="dia in ventasPorDia"
                        :key="dia.fecha"
                        class="flex flex-1 flex-col items-center justify-end gap-2"
                    >
                        <span class="text-xs font-medium text-gray-300">{{ dia.total }}</span>
                        <div
                            class="w-full rounded-t-md transition-all duration-500"
                            :style="{
                                height: Math.max((dia.total / maxVentas) * 120, 4) + 'px',
                                background: 'linear-gradient(to top, #3b82f6, #60a5fa)',
                            }"
                        />
                        <span class="text-xs text-gray-500">{{ formatDate(dia.fecha) }}</span>
                    </div>
                </div>
            </div>

            <!-- Donut chart: Productos por estado -->
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h3 class="mb-1 text-base font-semibold text-white">Productos por Estado</h3>
                <p class="mb-6 text-sm text-gray-400">Activos vs Inactivos</p>

                <div class="flex items-center justify-center gap-8">
                    <svg width="120" height="120" viewBox="0 0 100 100">
                        <!-- Background circle -->
                        <circle
                            cx="50"
                            cy="50"
                            r="40"
                            fill="none"
                            stroke="#374151"
                            stroke-width="10"
                        />
                        <!-- Active segment -->
                        <circle
                            cx="50"
                            cy="50"
                            r="40"
                            fill="none"
                            stroke="#10b981"
                            stroke-width="10"
                            stroke-linecap="round"
                            :stroke-dasharray="donutCircumference"
                            :stroke-dashoffset="donutOffset"
                            transform="rotate(-90 50 50)"
                            class="transition-all duration-700"
                        />
                        <!-- Center text -->
                        <text x="50" y="48" text-anchor="middle" class="text-lg font-bold" fill="#f3f4f6" font-size="14">
                            {{ productosPorEstado.activos }}
                        </text>
                        <text x="50" y="62" text-anchor="middle" fill="#9ca3af" font-size="8">
                            activos
                        </text>
                    </svg>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full bg-emerald-500" />
                            <div>
                                <p class="text-sm text-gray-300">Activos</p>
                                <p class="text-xs text-gray-500">{{ productosPorEstado.activos }} productos</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full bg-gray-500" />
                            <div>
                                <p class="text-sm text-gray-300">Inactivos</p>
                                <p class="text-xs text-gray-500">{{ productosPorEstado.inactivos }} productos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Stock Bajo -->
        <Teleport to="body">
            <div
                v-if="showStockBajoModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
                @click.self="showStockBajoModal = false"
            >
                <div class="w-full max-w-2xl rounded-xl border border-gray-800 bg-gray-900 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4">
                        <div>
                            <h3 class="text-lg font-bold text-white">Stock Bajo</h3>
                            <p class="text-sm text-gray-400">Productos con menos de 10 unidades disponibles</p>
                        </div>
                        <button
                            class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-800 hover:text-white"
                            @click="showStockBajoModal = false"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="max-h-96 overflow-y-auto p-6">
                        <table v-if="stockBajoProductos.length > 0" class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-800 text-left text-xs uppercase text-gray-500">
                                    <th class="pb-2 pr-4 font-medium">Producto</th>
                                    <th class="pb-2 pr-4 font-medium">SKU</th>
                                    <th class="pb-2 pr-4 font-medium">Cantidad</th>
                                    <th class="pb-2 pr-4 font-medium">Lote</th>
                                    <th class="pb-2 font-medium">Sede</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, i) in stockBajoProductos"
                                    :key="i"
                                    class="border-b border-gray-800/50"
                                >
                                    <td class="py-2 pr-4 text-white">{{ item.producto }}</td>
                                    <td class="py-2 pr-4 text-gray-400">{{ item.sku }}</td>
                                    <td class="py-2 pr-4">
                                        <span class="font-medium text-red-400">{{ item.cantidad }}</span>
                                    </td>
                                    <td class="py-2 pr-4 text-gray-400">{{ item.lote }}</td>
                                    <td class="py-2 text-gray-400">{{ item.sede }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-else class="py-8 text-center text-sm text-gray-500">
                            No hay productos con stock bajo.
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Modal: Productos por Vencer -->
        <Teleport to="body">
            <div
                v-if="showVencerModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
                @click.self="showVencerModal = false"
            >
                <div class="w-full max-w-2xl rounded-xl border border-gray-800 bg-gray-900 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4">
                        <div>
                            <h3 class="text-lg font-bold text-white">Productos por Vencer</h3>
                            <p class="text-sm text-gray-400">Productos próximos a vencer y vencidos</p>
                        </div>
                        <button
                            class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-800 hover:text-white"
                            @click="showVencerModal = false"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="max-h-96 overflow-y-auto p-6">
                        <!-- Vencidos -->
                        <div v-if="productosVencidos.length > 0" class="mb-6">
                            <h4 class="mb-3 text-sm font-semibold text-red-400">
                                Vencidos ({{ productosVencidos.length }})
                            </h4>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-800 text-left text-xs uppercase text-gray-500">
                                        <th class="pb-2 pr-4 font-medium">SKU</th>
                                        <th class="pb-2 pr-4 font-medium">Producto</th>
                                        <th class="pb-2 pr-4 font-medium">Vence</th>
                                        <th class="pb-2 font-medium">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="p in productosVencidos"
                                        :key="p.sku"
                                        class="border-b border-gray-800/50"
                                    >
                                        <td class="py-2 pr-4 text-gray-400">{{ p.sku }}</td>
                                        <td class="py-2 pr-4 text-white">{{ p.nombre_comercial }}</td>
                                        <td class="py-2 pr-4 text-gray-400">{{ p.fecha_vencimiento }}</td>
                                        <td class="py-2">
                                            <span class="font-medium text-red-400">Vencido</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Próximos a vencer -->
                        <div v-if="productosProximos.length > 0">
                            <h4 class="mb-3 text-sm font-semibold text-amber-400">
                                Próximos a Vencer ({{ productosProximos.length }})
                            </h4>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-800 text-left text-xs uppercase text-gray-500">
                                        <th class="pb-2 pr-4 font-medium">SKU</th>
                                        <th class="pb-2 pr-4 font-medium">Producto</th>
                                        <th class="pb-2 pr-4 font-medium">Vence</th>
                                        <th class="pb-2 font-medium">Días</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="p in productosProximos"
                                        :key="p.sku"
                                        class="border-b border-gray-800/50"
                                    >
                                        <td class="py-2 pr-4 text-gray-400">{{ p.sku }}</td>
                                        <td class="py-2 pr-4 text-white">{{ p.nombre_comercial }}</td>
                                        <td class="py-2 pr-4 text-gray-400">{{ p.fecha_vencimiento }}</td>
                                        <td class="py-2">
                                            <span :class="['font-medium', diasColor(p.dias_restantes)]">
                                                {{ diasLabel(p.dias_restantes) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            v-if="productosPorVencer.length === 0"
                            class="py-8 text-center text-sm text-gray-500"
                        >
                            No hay productos próximos a vencer.
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppPageShell>
</template>
