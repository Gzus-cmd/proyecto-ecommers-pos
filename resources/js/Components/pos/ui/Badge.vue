<script setup lang="ts">
import { cva, type VariantProps } from 'class-variance-authority';

const badgeVariants = cva(
    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
    {
        variants: {
            variant: {
                default: 'bg-gray-800 text-gray-300',
                success: 'bg-emerald-900/50 text-emerald-400',
                danger: 'bg-red-900/50 text-red-400',
                warning: 'bg-yellow-900/50 text-yellow-400',
                info: 'bg-blue-900/50 text-blue-400',
            },
        },
        defaultVariants: {
            variant: 'default',
        },
    },
);

type BadgeVariants = VariantProps<typeof badgeVariants>;

defineProps<{
    variant?: BadgeVariants['variant'];
}>();
</script>

<template>
    <span :class="badgeVariants({ variant })">
        <slot />
    </span>
</template>
