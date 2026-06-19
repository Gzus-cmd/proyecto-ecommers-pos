<script setup lang="ts">
import PosLayout from '@/Layouts/PosLayout.vue';
</script>

<template>
    <PosLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <slot />
        </div>
    </PosLayout>
</template>
