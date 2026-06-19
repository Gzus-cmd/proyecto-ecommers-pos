<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import { toast } from 'vue-sonner';

const form = useForm({
    dni: '',
    nombres: '',
    apellidos: '',
    telefono: '',
    email: '',
    activo: true,
});

function submit() {
    form.post(route('pos.clientes.store'), {
        onSuccess: () => {
            toast.success('Cliente creado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al crear el cliente');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Nuevo Cliente" description="Crear un nuevo cliente" back-route="pos.clientes.index" @submit="submit">
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
