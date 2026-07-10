<script setup lang="ts">
/**
 * AppPageHeader.vue
 *
 * Encabezado estándar para páginas del POS. Muestra el título,
 * descripción opcional, botón de "Volver al listado" y espacio
 * para acciones adicionales (slot "actions").
 *
 * Props:
 * - title: Título de la página
 * - description: Descripción corta (opcional)
 * - backRoute: Nombre de ruta para el botón "Volver" (opcional)
 *
 * Slots:
 * - actions: Botones de acción en la esquina superior derecha
 */
import { Link } from '@inertiajs/vue3';
import { route } from '@/lib/route';

defineProps<{
    title: string;
    description?: string;
    backRoute?: string;
}>();
</script>

<template>
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link
                    v-if="backRoute"
                    :href="route(backRoute)"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-transparent px-3 py-1.5 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-800"
                >
                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al listado
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ title }}</h1>
                    <p v-if="description" class="mt-1 text-sm text-gray-400">{{ description }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
