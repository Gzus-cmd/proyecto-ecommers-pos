<script setup lang="ts">
import { ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';

const props = withDefaults(
    defineProps<{
        modelValue?: string;
        placeholder?: string;
    }>(),
    {
        modelValue: '',
        placeholder: 'Buscar...',
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const localValue = ref(props.modelValue);

const debouncedEmit = useDebounceFn((value: string) => {
    emit('update:modelValue', value);
}, 400);

watch(localValue, (val) => {
    if (val !== props.modelValue) {
        debouncedEmit(val);
    }
});

watch(
    () => props.modelValue,
    (val) => {
        localValue.value = val;
    },
);

function onInput(e: Event) {
    localValue.value = (e.target as HTMLInputElement).value;
}
</script>

<template>
    <div class="relative">
        <svg
            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
        </svg>
        <input
            :value="localValue"
            :placeholder="placeholder"
            type="text"
            class="w-full rounded-lg border border-gray-700 bg-gray-900 py-2 pl-10 pr-4 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="onInput"
        />
    </div>
</template>
