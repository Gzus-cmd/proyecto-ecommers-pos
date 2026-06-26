<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
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
    nuevo_cliente_dni: '',
});

// Client local filter
const dniSearch = ref('');
const clientesList = ref<Cliente[]>([...props.clientes]);

const filteredClientes = computed(() => {
    if (!dniSearch.value) return clientesList.value;
    const q = dniSearch.value.toLowerCase();
    return clientesList.value.filter(
        (c) =>
            (c.dni && c.dni.toLowerCase().includes(q)) ||
            (c.nombres && c.nombres.toLowerCase().includes(q)) ||
            (c.apellidos && c.apellidos.toLowerCase().includes(q)),
    );
});

// Quick client creation
const showNuevoClienteForm = ref(false);
const nuevoClienteDni = ref('');
const creandoCliente = ref(false);

async function crearYSeleccionarCliente() {
    const dni = nuevoClienteDni.value.trim();
    if (dni.length !== 8) {
        toast.error('El DNI debe tener 8 dígitos');
        return;
    }

    // Verificar si ya existe
    const existe = clientesList.value.find((c) => c.dni === dni);
    if (existe) {
        form.cliente_id = String(existe.id);
        showNuevoClienteForm.value = false;
        nuevoClienteDni.value = '';
        toast.success('Cliente encontrado y seleccionado');
        return;
    }

    // Agregar el nuevo cliente a la lista para enviarlo con la venta
    const tempId = -Date.now();
    clientesList.value.push({ id: tempId, dni, nombres: '', apellidos: '', telefono: null, email: null });
    form.cliente_id = String(tempId);
    form.nuevo_cliente_dni = dni;
    showNuevoClienteForm.value = false;
    nuevoClienteDni.value = '';
    toast.success('Cliente registrado y seleccionado');
}

function cancelarNuevoCliente() {
    showNuevoClienteForm.value = false;
    nuevoClienteDni.value = '';
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
                            <!-- Input de búsqueda por DNI / nombre -->
                            <input
                                v-model="dniSearch"
                                type="text"
                                placeholder="Buscar por DNI o nombre..."
                                class="block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />

                            <!-- Select con clientes filtrados -->
                            <div class="flex gap-2">
                                <select
                                    v-model="form.cliente_id"
                                    class="block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">— Sin Cliente —</option>
                                    <option
                                        v-for="c in filteredClientes"
                                        :key="c.id"
                                        :value="String(c.id)"
                                    >
                                        {{ c.dni }} — {{ c.nombres || '' }} {{ c.apellidos || '' }}
                                    </option>
                                </select>

                                <button
                                    v-if="!showNuevoClienteForm"
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700"
                                    @click="showNuevoClienteForm = true"
                                >
                                    + Nuevo
                                </button>
                            </div>

                            <!-- Mini formulario: registrar nuevo cliente -->
                            <div
                                v-if="showNuevoClienteForm"
                                class="rounded-lg border border-gray-700 bg-gray-800/50 p-3 space-y-2"
                            >
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Registrar Nuevo Cliente</p>
                                <div class="flex gap-2">
                                    <input
                                        v-model="nuevoClienteDni"
                                        type="text"
                                        maxlength="8"
                                        placeholder="DNI"
                                        class="block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:opacity-50"
                                        :disabled="nuevoClienteDni.trim().length !== 8 || creandoCliente"
                                        @click="crearYSeleccionarCliente"
                                    >
                                        <svg v-if="creandoCliente" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        <span v-else>Crear y Seleccionar</span>
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-lg bg-gray-700 px-3 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-600"
                                        @click="cancelarNuevoCliente"
                                    >
                                        Cancelar
                                    </button>
                                </div>
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
