<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import Card from '@/Components/pos/ui/Card.vue';
import Select from '@/Components/pos/ui/Select.vue';
import Button from '@/Components/pos/ui/Button.vue';
import { toast } from 'vue-sonner';
import type { ProductoLocal, MetodoPago, Cliente, LoteLocal } from '@/types';

const props = defineProps<{
    productos: ProductoLocal[];
    metodosPago: MetodoPago[];
    clientes: Cliente[];
    lotes: LoteLocal[];
}>();

interface DetalleForm {
    producto_sku: string;
    lote_local_id: number | '';
    cantidad: number;
    precio_unitario: number;
    subtotal: number;
}

const form = useForm({
    cliente_id: '',
    metodo_pago_id: '',
    detalles: [] as DetalleForm[],
    subtotal: 0,
    impuesto: 0,
    total: 0,
    nuevo_cliente: null as {
        dni: string;
        nombres: string;
        apellidos: string;
    } | null,
});

// Client inline search
const dniSearch = ref('');
const searchingDni = ref(false);
const clienteEncontrado = ref<Cliente | null>(null);
const showNuevoClienteForm = ref(false);

async function buscarCliente() {
    const dni = dniSearch.value.trim();
    if (dni.length !== 8) return;

    searchingDni.value = true;
    clienteEncontrado.value = null;
    showNuevoClienteForm.value = false;
    form.cliente_id = '';
    form.nuevo_cliente = null;

    try {
        const res = await fetch(route('pos.clientes.search-by-dni') + '?dni=' + encodeURIComponent(dni));
        const data = await res.json();
        if (data.cliente) {
            clienteEncontrado.value = data.cliente;
            form.cliente_id = String(data.cliente.id);
        } else {
            clienteEncontrado.value = null;
            showNuevoClienteForm.value = true;
        }
    } catch {
        toast.error('Error al buscar cliente');
    } finally {
        searchingDni.value = false;
    }
}

function registrarNuevoCliente() {
    const dni = dniSearch.value.trim();
    if (dni.length !== 8) {
        toast.error('El DNI debe tener 8 dígitos');
        return;
    }

    form.nuevo_cliente = {
        dni,
        nombres: '',
        apellidos: '',
    };
    form.cliente_id = '';
    showNuevoClienteForm.value = false;
    dniSearch.value = dni;
}

function cancelarNuevoCliente() {
    form.nuevo_cliente = null;
    showNuevoClienteForm.value = false;
}

function limpiarCliente() {
    dniSearch.value = '';
    clienteEncontrado.value = null;
    showNuevoClienteForm.value = false;
    form.cliente_id = '';
    form.nuevo_cliente = null;
}

// Product modal
const showProductModal = ref(false);
const productSearch = ref('');
const selectedSku = ref('');

const filteredProductos = computed(() => {
    if (!productSearch.value) return props.productos;
    const q = productSearch.value.toLowerCase();
    return props.productos.filter(
        (p) =>
            p.sku.toLowerCase().includes(q) ||
            p.nombre_comercial.toLowerCase().includes(q),
    );
});

function openProductModal() {
    productSearch.value = '';
    selectedSku.value = '';
    showProductModal.value = true;
}

function lotesPorProducto(sku: string): LoteLocal[] {
    return props.lotes.filter(
        (l) => l.sku_producto === sku && (l as any).stock_actual > 0,
    );
}

function selectProduct(sku: string) {
    const existente = form.detalles.find((d) => d.producto_sku === sku);
    if (existente) {
        toast.error('El producto ya está agregado.');
        return;
    }
    const producto = props.productos.find((p) => p.sku === sku);
    if (!producto) return;

    const lotes = lotesPorProducto(sku);
    if (lotes.length === 0) {
        toast.error('No hay lotes disponibles para este producto.');
        return;
    }

    form.detalles.push({
        producto_sku: sku,
        lote_local_id: lotes.length === 1 ? lotes[0].id : '',
        cantidad: 1,
        precio_unitario: Number(producto.precio_venta),
        subtotal: Number(producto.precio_venta),
    });
    recalcTotals();
    showProductModal.value = false;
}

function removeProduct(index: number) {
    form.detalles.splice(index, 1);
    recalcTotals();
}

function updateSubtotal(index: number) {
    const d = form.detalles[index];
    d.subtotal = d.cantidad * d.precio_unitario;
    recalcTotals();
}

function recalcTotals() {
    const sub = form.detalles.reduce((acc, d) => acc + d.subtotal, 0);
    form.subtotal = sub;
    const imp = sub * 0.18;
    form.impuesto = Math.round(imp * 100) / 100;
    form.total = sub + form.impuesto;
}

function getProductName(sku: string): string {
    const p = props.productos.find((p) => p.sku === sku);
    return p ? `${p.nombre_comercial} (${p.sku})` : sku;
}

function submit() {
    if (form.detalles.length === 0) {
        toast.error('Debe agregar al menos un producto.');
        return;
    }
    if (!form.metodo_pago_id) {
        toast.error('Debe seleccionar un método de pago.');
        return;
    }

    const sinLote = form.detalles.find((d) => !d.lote_local_id);
    if (sinLote) {
        toast.error('Debe seleccionar un lote para cada producto.');
        return;
    }

    form.post(route('pos.ventas.store'), {
        onSuccess: () => {
            toast.success('Venta registrada correctamente');
        },
        onError: (errors) => {
            toast.error('Error al registrar la venta');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <AppPageHeader title="Nueva Venta" description="Registrar una venta">
            <template #actions>
                <Button variant="outline" @click="router.visit(route('pos.ventas.index'))">
                    Cancelar
                </Button>
            </template>
        </AppPageHeader>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left: product selection & table -->
                <div class="lg:col-span-2 space-y-6">
                    <Card title="Productos">
                        <template #header>
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                                <h3 class="text-lg font-semibold text-white">Productos</h3>
                                <Button type="button" size="sm" @click="openProductModal">
                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Agregar Producto
                                </Button>
                            </div>
                        </template>

                        <div v-if="form.detalles.length === 0" class="py-8 text-center text-sm text-gray-500">
                            No hay productos agregados. Presiona "Agregar Producto" para empezar.
                        </div>

                        <table v-else class="min-w-full divide-y divide-gray-800">
                            <thead>
                                <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-400">
                                    <th class="px-4 py-3">Producto</th>
                                    <th class="px-4 py-3 w-44">Lote</th>
                                    <th class="px-4 py-3 w-24">Cantidad</th>
                                    <th class="px-4 py-3 w-28">Precio Unit.</th>
                                    <th class="px-4 py-3 w-28">Subtotal</th>
                                    <th class="px-4 py-3 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                <tr v-for="(det, i) in form.detalles" :key="i" class="hover:bg-gray-800/50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-300">
                                        {{ getProductName(det.producto_sku) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <select
                                            :value="det.lote_local_id"
                                            @change="(e) => { det.lote_local_id = Number((e.target as HTMLSelectElement).value); }"
                                            class="w-full rounded-lg border border-gray-700 bg-gray-900 px-2 py-1 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="" disabled>Seleccionar lote</option>
                                            <option
                                                v-for="l in lotesPorProducto(det.producto_sku)"
                                                :key="l.id"
                                                :value="l.id"
                                            >
                                                {{ l.numero_lote }} — vence: {{ l.fecha_vencimiento }} (disp: {{ (l as any).stock_actual }})
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input
                                            type="number"
                                            min="1"
                                            :value="det.cantidad"
                                            @input="(e) => { det.cantidad = Number((e.target as HTMLInputElement).value); updateSubtotal(i); }"
                                            class="w-20 rounded-lg border border-gray-700 bg-gray-900 px-2 py-1 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-300">
                                        S/ {{ det.precio_unitario.toFixed(2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-300 font-medium">
                                        S/ {{ det.subtotal.toFixed(2) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            type="button"
                                            class="rounded p-1 text-red-400 hover:bg-red-500/10 transition-colors"
                                            @click="removeProduct(i)"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </Card>
                </div>

                <!-- Right: cliente, metodo pago, totals -->
                <div class="space-y-6">
                    <Card title="Cliente">
                        <div class="space-y-3">
                            <!-- Buscador por DNI -->
                            <div class="flex gap-2">
                                <input
                                    v-model="dniSearch"
                                    type="text"
                                    maxlength="8"
                                    placeholder="Buscar por DNI..."
                                    class="block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @keyup.enter="buscarCliente"
                                />
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:opacity-50"
                                    :disabled="dniSearch.trim().length !== 8 || searchingDni"
                                    @click="buscarCliente"
                                >
                                    <svg v-if="searchingDni" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                    <span v-else>Buscar</span>
                                </button>
                            </div>

                            <!-- Cliente encontrado -->
                            <div
                                v-if="clienteEncontrado"
                                class="flex items-center justify-between rounded-lg border border-emerald-800/50 bg-emerald-900/20 px-3 py-2"
                            >
                                <div>
                                    <p class="text-sm font-medium text-emerald-300">
                                        {{ clienteEncontrado.dni }}
                                    </p>
                                    <p class="text-xs text-emerald-400/70">
                                        {{ clienteEncontrado.nombres }} {{ clienteEncontrado.apellidos }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="rounded p-1 text-emerald-400/50 hover:text-emerald-300 transition-colors"
                                    @click="limpiarCliente"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Sugerencia: registrar nuevo cliente -->
                            <div
                                v-if="showNuevoClienteForm && !clienteEncontrado"
                                class="rounded-lg border border-amber-800/50 bg-amber-900/20 px-3 py-2"
                            >
                                <p class="text-xs text-amber-400 mb-2">
                                    Cliente con DNI <strong>{{ dniSearch }}</strong> no encontrado.
                                </p>
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 px-3 py-1.5 text-xs font-medium transition-colors"
                                    @click="registrarNuevoCliente"
                                >
                                    + Registrar nuevo cliente
                                </button>
                            </div>

                            <!-- Formulario nuevo cliente inline -->
                            <div
                                v-if="form.nuevo_cliente"
                                class="space-y-2 rounded-lg border border-gray-700 bg-gray-800/50 p-3"
                            >
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nuevo Cliente</p>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">DNI</label>
                                    <input
                                        type="text"
                                        maxlength="8"
                                        :value="form.nuevo_cliente.dni"
                                        disabled
                                        class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-gray-400"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Nombres</label>
                                    <input
                                        v-model="form.nuevo_cliente.nombres"
                                        type="text"
                                        placeholder="Nombres"
                                        class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Apellidos</label>
                                    <input
                                        v-model="form.nuevo_cliente.apellidos"
                                        type="text"
                                        placeholder="Apellidos"
                                        class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-lg bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-300 transition-colors hover:bg-gray-600"
                                        @click="cancelarNuevoCliente"
                                    >
                                        Cancelar
                                    </button>
                                    <span class="text-xs text-gray-500 self-center">Cliente se creará al registrar la venta</span>
                                </div>
                            </div>

                            <!-- Select de clientes existentes (fallback) -->
                            <div v-if="!clienteEncontrado && !form.nuevo_cliente">
                                <Select
                                    v-model="form.cliente_id"
                                    :options="[{ value: '', label: 'Sin cliente' }, ...clientes.map(c => ({ value: String(c.id), label: `${c.dni} - ${c.nombres || ''} ${c.apellidos || ''}` }))]"
                                />
                            </div>
                        </div>
                    </Card>

                    <Card title="Método de Pago">
                        <Select
                            v-model="form.metodo_pago_id"
                            :options="metodosPago.map(m => ({ value: String(m.id), label: m.nombre }))"
                        />
                        <p v-if="form.errors.metodo_pago_id" class="mt-1 text-xs text-red-400">{{ form.errors.metodo_pago_id }}</p>
                    </Card>

                    <Card title="Totales">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Subtotal</span>
                                <span class="text-white">S/ {{ form.subtotal.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Impuesto (18%)</span>
                                <span class="text-white">S/ {{ form.impuesto.toFixed(2) }}</span>
                            </div>
                            <div class="border-t border-gray-800 pt-2">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-white">Total</span>
                                    <span class="text-lg font-bold text-blue-400">S/ {{ form.total.toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>
                    </Card>

                    <Button type="submit" class="w-full" size="lg" :loading="form.processing">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Registrar Venta
                    </Button>
                </div>
            </div>
        </form>

        <!-- Product Modal -->
        <Teleport to="body">
            <div
                v-if="showProductModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
                @click.self="showProductModal = false"
            >
                <div class="w-full max-w-xl rounded-xl border border-gray-800 bg-gray-900 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4">
                        <h3 class="text-lg font-bold text-white">Agregar Producto</h3>
                        <button
                            type="button"
                            class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-800 hover:text-white"
                            @click="showProductModal = false"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-4">
                        <input
                            v-model="productSearch"
                            type="text"
                            placeholder="Buscar por SKU o nombre..."
                            class="block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4"
                        />

                        <div class="max-h-72 overflow-y-auto space-y-1">
                            <button
                                v-for="p in filteredProductos"
                                :key="p.sku"
                                type="button"
                                class="w-full text-left rounded-lg px-3 py-2.5 text-sm text-gray-300 transition-colors hover:bg-gray-800"
                                @click="selectProduct(p.sku)"
                            >
                                <span class="font-medium text-white">{{ p.nombre_comercial }}</span>
                                <span class="ml-2 text-gray-500">{{ p.sku }}</span>
                                <span class="float-right text-blue-400">S/ {{ Number(p.precio_venta).toFixed(2) }}</span>
                            </button>

                            <div v-if="filteredProductos.length === 0" class="py-6 text-center text-sm text-gray-500">
                                No se encontraron productos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppPageShell>
</template>
