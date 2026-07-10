<script setup lang="ts">
/**
 * ToastNotification.vue
 *
 * Sistema de notificaciones toast integrado con mensajes flash de
 * Inertia. Escucha los cambios en usePage().props.flash y muestra
 * notificaciones con animación de entrada/salida. Se renderiza en
 * la esquina superior derecha via Teleport.
 *
 * Soporta tipos: success (verde), error (rojo), warning (ámbar).
 * Las notificaciones se auto-eliminan después de 4 segundos.
 *
 * Uso: Se incluye una vez en PosLayout y funciona automáticamente.
 */
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Toast {
    id: number;
    message: string;
    type: 'success' | 'error' | 'warning';
}

const toasts = ref<Toast[]>([]);
let nextId = 0;

/** Agrega un nuevo toast y programa su eliminación automática */
function addToast(message: string, type: Toast['type']) {
    const id = nextId++;
    toasts.value.push({ id, message, type });
    setTimeout(() => {
        removeToast(id);
    }, 4000);
}

/** Elimina un toast por su ID */
function removeToast(id: number) {
    const idx = toasts.value.findIndex((t) => t.id === id);
    if (idx !== -1) toasts.value.splice(idx, 1);
}

const borderColors: Record<Toast['type'], string> = {
    success: 'border-l-emerald-500',
    error: 'border-l-red-500',
    warning: 'border-l-amber-500',
};

const icons: Record<Toast['type'], string> = {
    success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z',
};

// Watch for flash messages from Inertia
onMounted(() => {
    checkFlash();
});

watch(() => usePage().props.flash, () => {
    checkFlash();
}, { deep: true });

/** Lee los mensajes flash de Inertia y los muestra como toast */
function checkFlash() {
    const flash = usePage().props.flash as Record<string, string> | undefined;
    if (flash?.success) addToast(flash.success, 'success');
    if (flash?.error) addToast(flash.error, 'error');
}
</script>

<template>
    <Teleport to="body">
        <div class="fixed right-4 top-4 z-[100] flex flex-col gap-2 pointer-events-none">
            <TransitionGroup name="toast" tag="div" class="flex flex-col gap-2">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="[
                        'pointer-events-auto flex w-80 items-start gap-3 rounded-lg border border-gray-700 bg-gray-900 px-4 py-3 shadow-xl border-l-4',
                        borderColors[toast.type],
                    ]"
                >
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons[toast.type]" />
                    </svg>
                    <p class="flex-1 text-sm text-gray-200">{{ toast.message }}</p>
                    <button
                        class="flex-shrink-0 text-gray-500 hover:text-white transition-colors"
                        @click="removeToast(toast.id)"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
.toast-enter-active {
    transition: all 0.3s ease-out;
}
.toast-leave-active {
    transition: all 0.2s ease-in;
}
.toast-enter-from {
    transform: translateX(100%);
    opacity: 0;
}
.toast-leave-to {
    transform: translateX(100%);
    opacity: 0;
}
</style>
