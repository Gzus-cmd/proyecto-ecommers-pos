<script setup lang="ts">
/**
 * BadgeActivo.vue
 *
 * Badge visual que indica el estado activo/inactivo de una entidad.
 * Muestra "Activo" en verde o "Inactivo" en rojo con estilo pill.
 *
 * Props:
 * - activo: true muestra badge verde "Activo", false muestra rojo "Inactivo"
 */
defineProps<{
    activo: boolean;
}>();
</script>

<template>
    <span
        v-if="activo"
        class="inline-flex items-center rounded-full bg-emerald-900/50 px-2.5 py-0.5 text-xs font-medium text-emerald-400"
    >
        Activo
    </span>
    <span
        v-else
        class="inline-flex items-center rounded-full bg-red-900/50 px-2.5 py-0.5 text-xs font-medium text-red-400"
    >
        Inactivo
    </span>
</template>
