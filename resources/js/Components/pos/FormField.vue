<script setup lang="ts">
import Input from '@/Components/pos/ui/Input.vue';
import Select from '@/Components/pos/ui/Select.vue';

defineProps<{
    label: string;
    error?: string;
    required?: boolean;
}>();
</script>

<template>
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-300">
            {{ label }}<span v-if="required" class="ml-1 text-red-400">*</span>
        </label>
        <slot />
        <p v-if="error" class="mt-1 text-xs text-red-400">{{ error }}</p>
    </div>
</template>
