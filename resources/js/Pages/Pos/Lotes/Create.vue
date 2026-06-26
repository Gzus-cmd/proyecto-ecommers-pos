<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import Select from '@/Components/pos/ui/Select.vue';
import { toast } from 'vue-sonner';
import type { ProductoLocal } from '@/types';

const props = defineProps<{
    productos: ProductoLocal[];
}>();

const form = useForm({
    sku_producto: '',
    numero_lote: '',
    fecha_vencimiento: '',
    cantidad_disponible: '',
});

function submit() {
    form.post(route('pos.lotes.store'), {
        onSuccess: () => {
            toast.success('Lote creado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al crear el lote');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Nuevo Lote" description="Crear un nuevo lote para un producto" back-route="pos.lotes.index" @submit="submit">
            <FormField label="Producto" required :error="form.errors.sku_producto">
                <Select
                    v-model="form.sku_producto"
                    :options="productos.map(p => ({ value: p.sku, label: `${p.sku} - ${p.nombre_comercial}` }))"
                />
            </FormField>

            <FormField label="Número de Lote" required :error="form.errors.numero_lote">
                <Input v-model="form.numero_lote" placeholder="Ej: LOTE-001" />
            </FormField>

            <FormField label="Fecha de Vencimiento" required :error="form.errors.fecha_vencimiento">
                <Input v-model="form.fecha_vencimiento" type="date" />
            </FormField>

            <FormField label="Cantidad Disponible" required :error="form.errors.cantidad_disponible">
                <Input v-model="form.cantidad_disponible" type="number" min="0" placeholder="0" />
            </FormField>
        </FormPage>
    </AppPageShell>
</template>
