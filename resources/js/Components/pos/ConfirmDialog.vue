<script setup lang="ts">
import Button from '@/Components/pos/ui/Button.vue';

defineProps<{
    open: boolean;
    title?: string;
    message?: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'danger' | 'default';
    loading?: boolean;
}>();

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();

function onBackdropClick(e: MouseEvent) {
    if ((e.target as HTMLElement).dataset?.backdrop) {
        emit('cancel');
    }
}
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <div class="fixed inset-0 bg-black/60" data-backdrop @click="onBackdropClick" />
            <div class="relative z-10 w-full max-w-md rounded-xl border border-gray-800 bg-gray-900 p-6 shadow-2xl">
                <h3 class="text-lg font-semibold text-white">{{ title || 'Confirmar acción' }}</h3>
                <p class="mt-2 text-sm text-gray-400">{{ message || '¿Estás seguro de realizar esta acción?' }}</p>
                <div class="mt-6 flex justify-end gap-3">
                    <Button variant="outline" @click="emit('cancel')">
                        {{ cancelText || 'Cancelar' }}
                    </Button>
                    <Button
                        :variant="variant === 'danger' ? 'destructive' : 'default'"
                        :loading="loading"
                        @click="emit('confirm')"
                    >
                        {{ confirmText || 'Confirmar' }}
                    </Button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
