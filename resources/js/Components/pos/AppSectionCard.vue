<script setup lang="ts">
/**
 * AppSectionCard.vue
 *
 * Componente de tarjeta de sección reutilizable. Similar a Card pero
 * permite un slot "header" personalizado además del título/descripción
 * por props. Útil para agrupar contenido relacionado.
 *
 * Props:
 * - title: Título de la sección (opcional)
 * - description: Descripción corta (opcional)
 *
 * Slots:
 * - header: Contenido del encabezado (opcional, reemplaza title/description)
 * - default: Contenido del cuerpo de la tarjeta
 */
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="rounded-xl border border-gray-800 bg-gray-900 shadow-lg">
        <div v-if="title || $slots.header" class="border-b border-gray-800 px-6 py-4">
            <slot name="header">
                <h3 v-if="title" class="text-lg font-semibold text-white">{{ title }}</h3>
                <p v-if="description" class="mt-1 text-sm text-gray-400">{{ description }}</p>
            </slot>
        </div>
        <div class="px-6 py-4">
            <slot />
        </div>
    </div>
</template>
