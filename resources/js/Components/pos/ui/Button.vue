<script setup lang="ts">
/**
 * Button.vue
 *
 * Componente de botón reutilizable con variantes visuales.
 * Usa class-variance-authority (CVA) para gestionar las combinaciones
 * de variante + tamaño. Soporta estado de carga con spinner.
 *
 * Props:
 * - variant: Estilo visual ('default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'success')
 * - size: Tamaño ('sm' | 'default' | 'lg' | 'icon')
 * - disabled: Deshabilita el botón
 * - loading: Muestra spinner de carga y deshabilita
 * - type: Tipo del botón HTML ('button' | 'submit' | 'reset')
 *
 * Slots:
 * - default: Contenido del botón (texto o iconos)
 */
import { computed } from 'vue';
import { cva, type VariantProps } from 'class-variance-authority';

const buttonVariants = cva(
    'inline-flex items-center justify-center rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:pointer-events-none disabled:opacity-50',
    {
        variants: {
            variant: {
                default: 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm',
                destructive: 'bg-red-600 text-white hover:bg-red-700 shadow-sm',
                outline: 'border border-gray-700 bg-transparent text-gray-300 hover:bg-gray-800',
                secondary: 'bg-gray-800 text-gray-200 hover:bg-gray-700',
                ghost: 'text-gray-400 hover:text-white hover:bg-gray-800',
                success: 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm',
            },
            size: {
                sm: 'h-8 px-3 text-xs',
                default: 'h-10 px-4',
                lg: 'h-12 px-6 text-base',
                icon: 'h-10 w-10',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

type ButtonVariants = VariantProps<typeof buttonVariants>;

const props = withDefaults(
    defineProps<{
        variant?: ButtonVariants['variant'];
        size?: ButtonVariants['size'];
        disabled?: boolean;
        loading?: boolean;
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        variant: 'default',
        size: 'default',
        disabled: false,
        loading: false,
        type: 'button',
    },
);

const classes = computed(() => buttonVariants({ variant: props.variant, size: props.size }));
</script>

<template>
    <button :class="classes" :disabled="disabled || loading" :type="type">
        <svg v-if="loading" class="-ml-1 mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
        </svg>
        <slot />
    </button>
</template>
