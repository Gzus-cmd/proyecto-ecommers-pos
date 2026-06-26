<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import Toggle from '@/Components/pos/ui/Toggle.vue';
import { toast } from 'vue-sonner';
import type { MetodoPago } from '@/types';

const props = defineProps<{
    metodoPago: MetodoPago;
}>();

const form = useForm({
    nombre: props.metodoPago.nombre,
    numero_cuenta: props.metodoPago.numero_cuenta || '',
    titular: props.metodoPago.titular || '',
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

            <FormField label="Número de Cuenta" :error="form.errors.numero_cuenta">
                <Input v-model="form.numero_cuenta" placeholder="Ej: 952123456 para Yape, 191-1234567890 para transferencia" />
            </FormField>

            <FormField label="Titular" :error="form.errors.titular">
                <Input v-model="form.titular" placeholder="Nombre del titular (opcional)" />
            </FormField>

            <FormField label="Estado">
                <Toggle v-model="form.activo" label="Activo" />
            </FormField>
        </FormPage>
    </AppPageShell>
</template>
