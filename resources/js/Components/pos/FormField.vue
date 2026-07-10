<script setup lang="ts">
/**
 * FormField.vue
 *
 * Componente wrapper para campos de formulario. Agrega etiqueta,
 * indicador de campo requerido y mensaje de error. El contenido
 * del campo se pasa via slot default.
 *
 * Props:
 * - label: Etiqueta del campo
 * - error: Mensaje de error (opcional, se muestra en rojo)
 * - required: Muestra asterisco rojo indicando campo obligatorio
 *
 * Slots:
 * - default: El input/control del formulario
 */
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
