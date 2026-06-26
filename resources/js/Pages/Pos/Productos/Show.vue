<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import Card from '@/Components/pos/ui/Card.vue';
import Badge from '@/Components/pos/ui/Badge.vue';
import BadgeActivo from '@/Components/pos/BadgeActivo.vue';
import type { ProductoLocal } from '@/types';

defineProps<{
    producto: ProductoLocal;
}>();
</script>

<template>
    <AppPageShell>
        <AppPageHeader
            title="Detalle de Producto"
            :description="`${producto.nombre_comercial} (${producto.sku})`"
            back-route="pos.productos.index"
        >
            <template #actions>
                <Link
                    :href="route('pos.productos.edit', producto.sku)"
                    class="inline-flex items-center justify-center rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 px-4 py-2 text-sm font-medium transition-colors"
                >
                    Editar Producto
                </Link>
            </template>
        </AppPageHeader>

        <div class="max-w-3xl">
            <Card title="Información del Producto">
                <div class="space-y-4 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-gray-500">SKU</span>
                            <span class="text-white font-medium">{{ producto.sku }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500">Estado</span>
                            <BadgeActivo :activo="producto.activo" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-gray-500">Nombre Comercial</span>
                            <span class="text-white">{{ producto.nombre_comercial }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500">Nombre Genérico</span>
                            <span class="text-white">{{ producto.nombre_generico || '—' }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-gray-500">Descripción</span>
                        <span class="text-white">{{ producto.descripcion || '—' }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-gray-500">Concentración</span>
                            <span class="text-white">{{ producto.concentracion || '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500">Forma Farmacéutica</span>
                            <span class="text-white">{{ producto.forma_farmaceutica || '—' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-gray-500">Precio de Venta</span>
                            <span class="text-white font-semibold">S/ {{ Number(producto.precio_venta).toFixed(2) }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500">Requiere Receta</span>
                            <Badge :variant="producto.requiere_receta ? 'warning' : 'default'">
                                {{ producto.requiere_receta ? 'Sí' : 'No' }}
                            </Badge>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-gray-500">Fecha de Vencimiento</span>
                            <span class="text-white">{{ producto.fecha_vencimiento || 'No aplica' }}</span>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
    </AppPageShell>
</template>
