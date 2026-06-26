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

function isExpired(date: string): boolean {
    return new Date(date) < new Date();
}
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Detalle de Lote" :description="`Lote: ${lote.numero_lote}`">
            <template #actions>
                <Link
                    :href="route('pos.lotes.index')"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-transparent px-4 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-800"
                >
                    Volver
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
                                <span class="text-white">{{ lote.fecha_vencimiento }}</span>
                                <Badge v-if="isExpired(lote.fecha_vencimiento)" variant="danger">Vencido</Badge>
                            </div>
                        </div>
                        <div>
                            <span class="block text-gray-500">Registrado por</span>
                            <span class="text-white">{{ lote.user?.name || '—' }}</span>
                        </div>
                    </div>
                </div>
            </Card>

            <div class="mt-4 flex items-center gap-3">
                <Link
                    :href="route('pos.lotes.edit', lote.id)"
                    class="inline-flex items-center justify-center rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 px-4 py-2 text-sm font-medium transition-colors"
                >
                    Editar Lote
                </Link>
                <Link
                    :href="route('pos.lotes.index')"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-transparent px-4 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-800"
                >
                    Volver al listado
                </Link>
            </div>
        </div>
    </AppPageShell>
</template>
