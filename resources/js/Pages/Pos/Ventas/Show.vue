<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import Card from '@/Components/pos/ui/Card.vue';
import type { VentaFisica } from '@/types';

defineProps<{
    venta: VentaFisica;
}>();
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Detalle de Venta" :description="`Venta #${venta.id}`">
            <template #actions>
                <Link
                    :href="route('pos.ventas.index')"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-transparent px-4 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-800"
                >
                    Volver
                </Link>
            </template>
        </AppPageHeader>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <Card title="Información General">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Sede</span>
                        <span class="text-white">{{ venta.sede?.nombre || '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Usuario</span>
                        <span class="text-white">{{ venta.user?.name || '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Cliente</span>
                        <span class="text-white">{{ venta.cliente ? `${venta.cliente.nombres || ''} ${venta.cliente.apellidos || ''}`.trim() || venta.cliente.dni : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Método de Pago</span>
                        <span class="text-white">{{ venta.metodo_pago?.nombre || '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Fecha</span>
                        <span class="text-white">{{ new Date(venta.fecha_venta).toLocaleString() }}</span>
                    </div>
                </div>
            </Card>

            <Card title="Totales">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Subtotal</span>
                        <span class="text-white">S/ {{ Number(venta.subtotal).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Impuesto</span>
                        <span class="text-white">S/ {{ Number(venta.impuesto).toFixed(2) }}</span>
                    </div>
                    <div class="border-t border-gray-800 pt-2">
                        <div class="flex justify-between">
                            <span class="font-semibold text-white">Total</span>
                            <span class="text-lg font-bold text-blue-400">S/ {{ Number(venta.total).toFixed(2) }}</span>
                        </div>
                    </div>
                </div>
            </Card>

            <Card title="Detalles" class="lg:col-span-3">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead>
                        <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-400">
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Cantidad</th>
                            <th class="px-4 py-3">Precio Unit.</th>
                            <th class="px-4 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        <tr v-for="detalle in venta.detalles" :key="detalle.id" class="hover:bg-gray-800/50">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-300">
                                {{ detalle.producto?.nombre_comercial || detalle.producto_sku }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-400">
                                {{ detalle.producto_sku }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-300">
                                {{ detalle.cantidad }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-300">
                                S/ {{ Number(detalle.precio_unitario).toFixed(2) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-300">
                                S/ {{ Number(detalle.subtotal).toFixed(2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </div>
    </AppPageShell>
</template>
