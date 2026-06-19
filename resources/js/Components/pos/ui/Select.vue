<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue?: string | number;
        label?: string;
        error?: string;
        disabled?: boolean;
        options: { value: string | number; label: string }[];
    }>(),
    {
        modelValue: '',
        disabled: false,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

function onChange(e: Event) {
    const target = e.target as HTMLSelectElement;
    emit('update:modelValue', target.value);
}

const selectClasses = computed(() => {
    const base =
        'block w-full rounded-lg border bg-gray-900 px-3 py-2 text-sm text-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500';
    return props.error
        ? `${base} border-red-500 focus:ring-red-500`
        : `${base} border-gray-700 hover:border-gray-600`;
});
</script>

<template>
    <div class="space-y-1">
        <label v-if="label" class="block text-sm font-medium text-gray-300">{{ label }}</label>
        <select :value="modelValue" :disabled="disabled" :class="selectClasses" @change="onChange">
            <option value="" disabled selected>Seleccionar...</option>
            <option v-for="opt in options" :key="opt.value" :value="opt.value">
                {{ opt.label }}
            </option>
        </select>
        <p v-if="error" class="text-xs text-red-400">{{ error }}</p>
    </div>
</template>
