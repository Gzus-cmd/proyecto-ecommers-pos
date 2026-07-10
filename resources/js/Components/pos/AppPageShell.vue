<script setup lang="ts">
/**
 * AppPageShell.vue
 *
 * Componente wrapper para páginas del POS. Envuelve el contenido en
 * PosLayout y agrega contenedor con padding responsivo (max-w-7xl).
 * Es el shell estándar para todas las páginas de gestión.
 *
 * Slots:
 * - default: Contenido principal de la página
 */
import PosLayout from '@/Layouts/PosLayout.vue';
</script>

<template>
    <PosLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <slot />
        </div>
    </PosLayout>
</template>
