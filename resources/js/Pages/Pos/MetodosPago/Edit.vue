<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import { toast } from 'vue-sonner';
import type { MetodoPago } from '@/types';

const props = defineProps<{
    metodoPago: MetodoPago;
}>();

const form = useForm({
    nombre: props.metodoPago.nombre,
    activo: props.metodoPago.activo,
});

function submit() {
    form.put(route('pos.metodos-pago.update', props.metodoPago.id), {
        onSuccess: () => {
            toast.success('Método de pago actualizado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al actualizar el método de pago');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Editar Método de Pago" :description="`Editando: ${metodoPago.nombre}`" :is-editing="true" back-route="pos.metodos-pago.index" @submit="submit">
            <FormField label="Nombre" required :error="form.errors.nombre">
                <Input v-model="form.nombre" placeholder="Ej: Efectivo, Tarjeta, Yape..." />
            </FormField>

            <FormField label="Estado">
                <label class="flex items-center gap-2">
                    <input v-model="form.activo" type="checkbox" class="rounded border-gray-700 bg-gray-900 text-blue-600" />
                    <span class="text-sm text-gray-300">Activo</span>
                </label>
            </FormField>
        </FormPage>
    </AppPageShell>
</template>
