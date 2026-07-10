<script setup lang="ts">
/**
 * MetodosPago/Create.vue
 *
 * Página para crear un nuevo método de pago. Formulario con campos:
 * nombre, número de cuenta, titular y estado. Envía POST a
 * 'pos.metodos-pago.store'.
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
    nombre: '',
    numero_cuenta: '',
    titular: '',
    activo: true,
});

/** Envía el formulario para crear el método de pago */
function submit() {
    form.post(route('pos.metodos-pago.store'), {
        onSuccess: () => {
            toast.success('Método de pago creado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al crear el método de pago');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Nuevo Método de Pago" description="Crear un nuevo método de pago" back-route="pos.metodos-pago.index" @submit="submit">
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
