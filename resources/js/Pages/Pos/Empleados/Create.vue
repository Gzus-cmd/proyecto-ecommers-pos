<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import { toast } from 'vue-sonner';

const form = useForm({
    nombres: '',
    apellidos: '',
    dni: '',
    cargo: '',
    activo: true,
});

function submit() {
    form.post(route('pos.empleados.store'), {
        onSuccess: () => {
            toast.success('Empleado creado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al crear el empleado');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Nuevo Empleado" description="Crear un nuevo empleado" back-route="pos.empleados.index" @submit="submit">
            <div class="grid grid-cols-2 gap-4">
                <FormField label="Nombres" required :error="form.errors.nombres">
                    <Input v-model="form.nombres" placeholder="Nombres" />
                </FormField>

                <FormField label="Apellidos" required :error="form.errors.apellidos">
                    <Input v-model="form.apellidos" placeholder="Apellidos" />
                </FormField>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <FormField label="DNI" required :error="form.errors.dni">
                    <Input v-model="form.dni" placeholder="N° de documento" />
                </FormField>

                <FormField label="Cargo" :error="form.errors.cargo">
                    <Input v-model="form.cargo" placeholder="Ej: Farmacéutico" />
                </FormField>
            </div>

            <FormField label="Estado">
                <label class="flex items-center gap-2">
                    <input v-model="form.activo" type="checkbox" class="rounded border-gray-700 bg-gray-900 text-blue-600" />
                    <span class="text-sm text-gray-300">Activo</span>
                </label>
            </FormField>
        </FormPage>
    </AppPageShell>
</template>
