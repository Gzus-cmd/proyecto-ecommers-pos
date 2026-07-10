<script setup lang="ts">
/**
 * Input.vue
 *
 * Componente de campo de entrada de texto reutilizable.
 * Soporta v-model, etiqueta, mensaje de error, tipos HTML y
 * estado deshabilitado. Estilo oscuro consistente con el tema POS.
 *
 * Props:
 * - modelValue: Valor actual del input
 * - label: Etiqueta sobre el campo (opcional)
 * - error: Mensaje de error (opcional, cambia borde a rojo)
 * - type: Tipo HTML del input (text, email, password, number, date, etc.)
 * - placeholder: Texto placeholder
 * - disabled: Deshabilita el campo
 *
 * Emits:
 * - update:modelValue: Se dispara al escribir en el campo
 */
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue?: string | number;
        label?: string;
        error?: string;
        type?: string;
        placeholder?: string;
        disabled?: boolean;
    }>(),
    {
        modelValue: '',
        type: 'text',
        disabled: false,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

function onInput(e: Event) {
    const target = e.target as HTMLInputElement;
    emit('update:modelValue', target.value);
}

const inputClasses = computed(() => {
    const base =
        'block w-full rounded-lg border bg-gray-900 px-3 py-2 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500';
    return props.error
        ? `${base} border-red-500 focus:ring-red-500`
        : `${base} border-gray-700 hover:border-gray-600`;
});
</script>

<template>
    <div class="space-y-1">
        <label v-if="label" class="block text-sm font-medium text-gray-300">{{ label }}</label>
        <input
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :class="inputClasses"
            @input="onInput"
        />
        <p v-if="error" class="text-xs text-red-400">{{ error }}</p>
    </div>
</template>
