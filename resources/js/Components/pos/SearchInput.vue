<script setup lang="ts">
/**
 * SearchInput.vue
 *
 * Componente de entrada de búsqueda con icono. Soporta actualización
 * v-model y botón opcional de búsqueda explícita (showButton).
 * Dispara el evento update:modelValue al presionar Enter o al hacer
 * clic en el botón de búsqueda.
 *
 * Props:
 * - modelValue: Valor actual del campo de búsqueda
 * - placeholder: Texto placeholder
 * - showButton: Muestra botón "Buscar" al lado del input
 *
 * Emits:
 * - update:modelValue: Se dispara al buscar
 */
import { ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue?: string;
        placeholder?: string;
        showButton?: boolean;
    }>(),
    {
        modelValue: '',
        placeholder: 'Buscar...',
        showButton: false,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const localValue = ref(props.modelValue);

/** Emite el valor actual al padre */
function onSearch() {
    emit('update:modelValue', localValue.value);
}

/** Escucha la tecla Enter para disparar la búsqueda */
function onKeydown(e: KeyboardEvent) {
    if (e.key === 'Enter') {
        onSearch();
    }
}

/** Sincroniza el valor local cuando la prop cambia externamente */
watch(
    () => props.modelValue,
    (val) => {
        localValue.value = val;
    },
);
</script>

<template>
    <div class="relative flex gap-2">
        <div class="relative flex-1">
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
                @input="localValue = ($event.target as HTMLInputElement).value"
                @keydown="onKeydown"
            />
        </div>
        <button
            v-if="showButton"
            type="button"
            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700"
            @click="onSearch"
        >
            Buscar
        </button>
    </div>
</template>
