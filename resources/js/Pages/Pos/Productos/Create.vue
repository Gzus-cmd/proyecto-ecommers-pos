<script setup lang="ts">
/**
 * Productos/Create.vue
 *
 * Página para crear un nuevo producto local. Formulario con campos:
 * SKU, nombre comercial, nombre genérico, descripción, concentración,
 * forma farmacéutica, requiere receta, precio de venta y estado.
 * Envía POST a 'pos.productos.store'.
 */
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import Toggle from '@/Components/pos/ui/Toggle.vue';
import { toast } from 'vue-sonner';

const form = useForm({
    sku: '',
    nombre_comercial: '',
    nombre_generico: '',
    descripcion: '',
    concentracion: '',
    forma_farmaceutica: '',
    requiere_receta: false,
    precio_venta: '',
    activo: true,
});

/** Envía el formulario para crear el producto */
function submit() {
    form.post(route('pos.productos.store'), {
        onSuccess: () => {
            toast.success('Producto creado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al crear el producto');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Nuevo Producto" description="Crear un nuevo producto local" back-route="pos.productos.index" @submit="submit">
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
