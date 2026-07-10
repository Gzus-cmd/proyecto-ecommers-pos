<script setup lang="ts">
/**
 * Lotes/Create.vue
 *
 * Página de recepción de lotes. Permite registrar múltiples lotes de
 * productos de una sola vez con un formulario dinámico de filas.
 * Cada fila incluye: producto, número de lote, fecha de vencimiento y
 * cantidad. Envía POST a 'pos.lotes.store'.
 *
 * Props:
 * - productos: Lista completa de productos disponibles para seleccionar
 */
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import { toast } from 'vue-sonner';
import type { ProductoLocal } from '@/types';

interface LoteRow {
    id: number;
    sku_producto: string;
    numero_lote: string;
    fecha_vencimiento: string;
    cantidad_disponible: string;
}

const props = defineProps<{
    productos: ProductoLocal[];
}>();

let nextId = 1;
/** Lista reactiva de filas de lotes a registrar */
const lotes = reactive<LoteRow[]>([]);
const saving = ref(false);

/** Agrega una nueva fila vacía al formulario de lotes */
function agregarFila() {
    lotes.push({
        id: nextId++,
        sku_producto: '',
        numero_lote: '',
        fecha_vencimiento: '',
        cantidad_disponible: '',
    });
}

/** Elimina una fila del formulario por su ID */
function quitarFila(id: number) {
    const idx = lotes.findIndex((l) => l.id === id);
    if (idx !== -1) lotes.splice(idx, 1);
}

// Agregar primera fila por defecto
agregarFila();

/**
 * Valida y envía el formulario de lotes.
 * Realiza validación client-side antes de enviar POST.
 */
function submit() {
    // Validación client-side básica
    for (const lote of lotes) {
        if (!lote.sku_producto || !lote.numero_lote || !lote.fecha_vencimiento || !lote.cantidad_disponible) {
            toast.error('Completa todos los campos de cada lote.');
            return;
        }
        if (Number(lote.cantidad_disponible) < 0) {
            toast.error('La cantidad no puede ser negativa.');
            return;
        }
    }

    saving.value = true;
    router.post(
        route('pos.lotes.store'),
        { lotes: lotes.map((l) => ({ ...l, cantidad_disponible: Number(l.cantidad_disponible) })) },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success(`${lotes.length} lote(s) creado(s) correctamente`);
                saving.value = false;
            },
            onError: (errors) => {
                const msgs = Object.values(errors).join(', ');
                toast.error(msgs || 'Error al crear los lotes');
                saving.value = false;
            },
        },
    );
}

/** Retorna la etiqueta de un producto para mostrar en el select */
function productoLabel(sku: string): string {
    const p = props.productos.find((p) => p.sku === sku);
    return p ? `${p.sku} - ${p.nombre_comercial}` : sku;
}
</script>

<template>
    <AppPageShell>
        <div class="mb-8">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Recepción de Lotes</h1>
                    <p class="mt-1 text-sm text-gray-400">Registrar múltiples lotes de productos</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        class="inline-flex items-center justify-center rounded-lg border border-gray-700 px-4 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-800"
                        @click="router.get(route('pos.lotes.index'))"
                    >
                        Cancelar
                    </button>
                    <button
                        class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:opacity-50"
                        :disabled="saving || lotes.length === 0"
                        @click="submit"
                    >
                        <svg v-if="saving" class="mr-2 h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <svg v-else class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Guardar Lotes
                    </button>
                </div>
            </div>
        </div>

        <div v-if="lotes.length === 0" class="rounded-xl border border-dashed border-gray-800 py-16 text-center">
            <p class="text-gray-500">Agrega al menos un producto para crear lotes.</p>
        </div>

        <div v-for="(lote, idx) in lotes" :key="lote.id" class="mb-4 rounded-xl border border-gray-800 bg-gray-900 p-5">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-300">Lote #{{ idx + 1 }}</span>
                <button
                    class="rounded-lg p-1.5 text-gray-500 transition-colors hover:bg-red-900/30 hover:text-red-400"
                    @click="quitarFila(lote.id)"
                    title="Quitar"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Producto -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-gray-500">Producto <span class="text-red-400">*</span></label>
                    <select
                        v-model="lote.sku_producto"
                        class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-gray-200 focus:border-blue-500 focus:outline-none"
                    >
                        <option value="" disabled>Seleccionar producto</option>
                        <option
                            v-for="p in productos"
                            :key="p.sku"
                            :value="p.sku"
                        >
                            {{ p.sku }} — {{ p.nombre_comercial }}
                        </option>
                    </select>
                </div>

                <!-- Número de Lote -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-gray-500">N° Lote <span class="text-red-400">*</span></label>
                    <input
                        v-model="lote.numero_lote"
                        type="text"
                        placeholder="Ej: LOTE-001"
                        class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:border-blue-500 focus:outline-none"
                    />
                </div>

                <!-- Fecha de Vencimiento -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-gray-500">Vencimiento <span class="text-red-400">*</span></label>
                    <input
                        v-model="lote.fecha_vencimiento"
                        type="date"
                        class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-gray-200 focus:border-blue-500 focus:outline-none"
                    />
                </div>

                <!-- Cantidad -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-gray-500">Cantidad <span class="text-red-400">*</span></label>
                    <input
                        v-model="lote.cantidad_disponible"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:border-blue-500 focus:outline-none"
                    />
                </div>
            </div>
        </div>

        <!-- Botón inferior para agregar -->
        <div class="mt-2">
            <button
                class="inline-flex items-center gap-2 rounded-lg border border-dashed border-gray-700 px-4 py-2 text-sm font-medium text-gray-400 transition-colors hover:border-gray-500 hover:text-gray-200"
                @click="agregarFila"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Agregar Producto
            </button>
        </div>
    </AppPageShell>
</template>
