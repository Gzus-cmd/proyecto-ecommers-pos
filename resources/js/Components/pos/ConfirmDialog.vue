<script setup lang="ts">
/**
 * ConfirmDialog.vue
 *
 * Diálogo modal de confirmación. Se renderiza con Teleport al body.
 * Útil para confirmar acciones destructivas como eliminaciones.
 * Soporta variantes danger/default, estado de carga y textos
 * personalizados para botones.
 *
 * Props:
 * - open: Controla la visibilidad del diálogo
 * - title: Título del diálogo
 * - message: Mensaje de confirmación
 * - confirmText: Texto del botón de confirmación
 * - cancelText: Texto del botón de cancelar
 * - variant: Estilo visual ('danger' | 'default')
 * - loading: Muestra estado de carga en botón de confirmar
 *
 * Emits:
 * - confirm: Se dispara al confirmar la acción
 * - cancel: Se dispara al cancelar o cerrar
 */
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

/** Cierra el diálogo si se hace clic en el backdrop (fondo oscuro) */
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
