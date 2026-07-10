<script setup lang="ts">
/**
 * Toggle.vue
 *
 * Componente de interruptor (switch) on/off con etiqueta opcional.
 * Sigue el patrón v-model para enlace bidireccional.
 *
 * Props:
 * - modelValue: Valor booleano actual del toggle
 * - label: Etiqueta descriptiva (opcional, se muestra a la derecha)
 *
 * Emits:
 * - update:modelValue: Se dispara al cambiar el estado
 */
defineProps<{
    modelValue: boolean;
    label?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: boolean];
}>();
</script>

<template>
    <label class="flex cursor-pointer items-center gap-3" @click.prevent="emit('update:modelValue', !modelValue)">
        <button
            type="button"
            role="switch"
            :aria-checked="modelValue"
            :class="[
                'relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900',
                modelValue ? 'bg-emerald-500' : 'bg-gray-500',
            ]"
        >
            <span
                :class="[
                    'inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200 ease-in-out',
                    modelValue ? 'translate-x-6' : 'translate-x-1',
                ]"
            />
        </button>
        <span v-if="label" class="select-none text-sm text-gray-300">{{ label }}</span>
    </label>
</template>
