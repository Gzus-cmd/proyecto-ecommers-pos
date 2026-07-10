<script setup lang="ts">
/**
 * Productos/Edit.vue
 *
 * Página para editar un producto existente. Precarga los datos del
 * producto recibido por props. Envía PUT a 'pos.productos.update'.
 *
 * Props:
 * - producto: Objeto ProductoLocal con los datos actuales
 */
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import Toggle from '@/Components/pos/ui/Toggle.vue';
import { toast } from 'vue-sonner';
import type { ProductoLocal } from '@/types';

const props = defineProps<{
    producto: ProductoLocal;
}>();

const form = useForm({
    sku: props.producto.sku,
    nombre_comercial: props.producto.nombre_comercial,
    nombre_generico: props.producto.nombre_generico || '',
    descripcion: props.producto.descripcion || '',
    concentracion: props.producto.concentracion || '',
    forma_farmaceutica: props.producto.forma_farmaceutica || '',
    requiere_receta: props.producto.requiere_receta,
    precio_venta: String(props.producto.precio_venta),
    activo: props.producto.activo,
});

/** Envía el formulario para actualizar el producto */
function submit() {
    form.put(route('pos.productos.update', props.producto.sku), {
        onSuccess: () => {
            toast.success('Producto actualizado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al actualizar el producto');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Editar Producto" :description="`Editando: ${producto.nombre_comercial}`" :is-editing="true" back-route="pos.productos.index" @submit="submit">
            <FormField label="SKU" required :error="form.errors.sku">
                <Input v-model="form.sku" placeholder="Ej: PROD-001" />
            </FormField>

            <FormField label="Nombre Comercial" required :error="form.errors.nombre_comercial">
                <Input v-model="form.nombre_comercial" placeholder="Nombre comercial del producto" />
            </FormField>

            <FormField label="Nombre Genérico" :error="form.errors.nombre_generico">
                <Input v-model="form.nombre_generico" placeholder="Nombre genérico (opcional)" />
            </FormField>

            <FormField label="Descripción" :error="form.errors.descripcion">
                <textarea
                    v-model="form.descripcion"
                    placeholder="Descripción (opcional)"
                    class="block w-full rounded-lg border border-gray-700 bg-gray-900 px-3 py-2 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="3"
                />
            </FormField>

            <div class="grid grid-cols-2 gap-4">
                <FormField label="Concentración" :error="form.errors.concentracion">
                    <Input v-model="form.concentracion" placeholder="Ej: 500mg" />
                </FormField>

                <FormField label="Forma Farmacéutica" :error="form.errors.forma_farmaceutica">
                    <Input v-model="form.forma_farmaceutica" placeholder="Ej: Tabletas" />
                </FormField>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <FormField label="Precio de Venta" required :error="form.errors.precio_venta">
                    <Input v-model="form.precio_venta" type="number" step="0.01" placeholder="0.00" />
                </FormField>

                <FormField label="Requiere Receta">
                    <Toggle v-model="form.requiere_receta" label="Requiere Receta" />
                </FormField>
            </div>

            <FormField label="Estado">
                <Toggle v-model="form.activo" label="Activo" />
            </FormField>
        </FormPage>
    </AppPageShell>
</template>
