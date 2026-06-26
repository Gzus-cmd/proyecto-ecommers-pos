<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import Card from '@/Components/pos/ui/Card.vue';
import Badge from '@/Components/pos/ui/Badge.vue';
import type { LoteLocal } from '@/types';

defineProps<{
    lote: LoteLocal;
}>();

function getEstadoInfo(fecha: string): { label: string; variant: 'danger' | 'warning' | 'success' } {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const venc = new Date(fecha);
    if (isNaN(venc.getTime())) return { label: 'Vencido', variant: 'danger' };
    const diff = Math.floor((venc.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24));
    if (diff <= 0) return { label: 'Vencido', variant: 'danger' };
    if (diff <= 90) return { label: 'Por Vencer', variant: 'warning' };
    return { label: 'Vigente', variant: 'success' };
}

function formatDate(date: string): string {
    if (!date) return '-';
    const d = new Date(date);
    return isNaN(d.getTime()) ? '-' : d.toLocaleDateString('es-PE');
}
</script>

<template>
    <AppPageShell>
        <AppPageHeader
            title="Detalle de Lote"
            :description="`Lote: ${lote.numero_lote}`"
            back-route="pos.lotes.index"
        >
            <template #actions>
                <Link
                    :href="route('pos.lotes.edit', lote.id)"
                    class="inline-flex items-center justify-center rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 px-4 py-2 text-sm font-medium transition-colors"
                >
                    Editar Lote
                </Link>
            </template>
        </AppPageHeader>

        <div class="max-w-3xl">
            <Card title="Información del Lote">
                <div class="space-y-4 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-gray-500">N° Lote</span>
                            <span class="text-white font-medium">{{ lote.numero_lote }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500">SKU Producto</span>
                            <span class="text-white">{{ lote.sku_producto }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-gray-500">Producto</span>
                            <span class="text-white">{{ lote.producto?.nombre_comercial || '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500">Cantidad Disponible</span>
                            <span class="text-white font-semibold">{{ lote.cantidad_disponible }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-gray-500">Fecha de Vencimiento</span>
                            <div class="flex items-center gap-2">
                                <span class="text-white">{{ formatDate(lote.fecha_vencimiento) }}</span>
                                <Badge :variant="getEstadoInfo(lote.fecha_vencimiento).variant">
                                    {{ getEstadoInfo(lote.fecha_vencimiento).label }}
                                </Badge>
                            </div>
                        </div>
                        <div>
                            <span class="block text-gray-500">Registrado por</span>
                            <span class="text-white">{{ lote.user?.name || '—' }}</span>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
    </AppPageShell>
</template>
