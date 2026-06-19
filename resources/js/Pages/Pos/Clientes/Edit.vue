<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
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
    nombres: props.cliente.nombres,
    apellidos: props.cliente.apellidos,
    telefono: props.cliente.telefono || '',
    email: props.cliente.email || '',
    activo: props.cliente.activo,
});

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
        <FormPage title="Editar Cliente" :description="`Editando: ${cliente.nombres} ${cliente.apellidos}`" :is-editing="true" back-route="pos.clientes.index" @submit="submit">
            <div class="grid grid-cols-2 gap-4">
                <FormField label="DNI" required :error="form.errors.dni">
                    <Input v-model="form.dni" placeholder="N° de documento" />
                </FormField>

                <FormField label="Nombres" required :error="form.errors.nombres">
                    <Input v-model="form.nombres" placeholder="Nombres" />
                </FormField>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <FormField label="Apellidos" required :error="form.errors.apellidos">
                    <Input v-model="form.apellidos" placeholder="Apellidos" />
                </FormField>

                <FormField label="Teléfono" :error="form.errors.telefono">
                    <Input v-model="form.telefono" placeholder="Ej: 987654321" />
                </FormField>
            </div>

            <FormField label="Correo electrónico" :error="form.errors.email">
                <Input v-model="form.email" type="email" placeholder="correo@ejemplo.com" />
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
