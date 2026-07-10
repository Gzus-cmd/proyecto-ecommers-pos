<script setup lang="ts">
/**
 * Clientes/Create.vue
 *
 * Página para crear un nuevo cliente. Formulario con campos: DNI,
 * nombres, apellidos, teléfono y correo electrónico.
 * Envía POST a 'pos.clientes.store'.
 */
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
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
});

/** Envía el formulario para crear el cliente */
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
