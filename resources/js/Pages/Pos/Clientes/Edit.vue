<script setup lang="ts">
/**
 * Clientes/Edit.vue
 *
 * Página para editar un cliente existente. Precarga los datos del
 * cliente recibido por props. Envía PUT a 'pos.clientes.update'.
 *
 * Props:
 * - cliente: Objeto Cliente con los datos actuales
 */
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import { toast } from 'vue-sonner';
import type { Cliente } from '@/types';

const props = defineProps<{
    cliente: Cliente;
}>();

const form = useForm({
    dni: props.cliente.dni,
    nombres: props.cliente.nombres || '',
    apellidos: props.cliente.apellidos || '',
    telefono: props.cliente.telefono || '',
    email: props.cliente.email || '',
});

/** Envía el formulario para actualizar el cliente */
function submit() {
    form.put(route('pos.clientes.update', props.cliente.id), {
        onSuccess: () => {
            toast.success('Cliente actualizado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al actualizar el cliente');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Editar Cliente" :description="`Editando: ${cliente.nombres || cliente.dni}`" :is-editing="true" back-route="pos.clientes.index" @submit="submit">
            <div class="grid grid-cols-2 gap-4">
                <FormField label="DNI" required :error="form.errors.dni">
                    <Input v-model="form.dni" placeholder="N° de documento" />
                </FormField>

                <FormField label="Nombres" :error="form.errors.nombres">
                    <Input v-model="form.nombres" placeholder="Nombres (opcional)" />
                </FormField>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <FormField label="Apellidos" :error="form.errors.apellidos">
                    <Input v-model="form.apellidos" placeholder="Apellidos (opcional)" />
                </FormField>

                <FormField label="Teléfono" :error="form.errors.telefono">
                    <Input v-model="form.telefono" placeholder="Ej: 987654321" />
                </FormField>
            </div>

            <FormField label="Correo electrónico" :error="form.errors.email">
                <Input v-model="form.email" type="email" placeholder="correo@ejemplo.com" />
            </FormField>
        </FormPage>
    </AppPageShell>
</template>
