<script setup lang="ts">
/**
 * Card.vue
 *
 * Componente de tarjeta contenedora con borde, fondo oscuro y sombra.
 * Soporta título y descripción en el encabezado, con slot "header"
 * personalizable. El contenido principal va en el slot default.
 *
 * Props:
 * - title: Título de la tarjeta (opcional)
 * - description: Descripción corta (opcional)
 *
 * Slots:
 * - header: Encabezado personalizado (reemplaza title/description)
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
