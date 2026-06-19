<script setup lang="ts">
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
