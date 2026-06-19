<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import { toast } from 'vue-sonner';

const form = useForm({
    codigo: '',
    nombre: '',
    direccion: '',
    telefono: '',
    activo: true,
});

function submit() {
    form.post(route('pos.sedes.store'), {
        onSuccess: () => {
            toast.success('Sede creada correctamente');
        },
        onError: (errors) => {
            toast.error('Error al crear la sede');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Nueva Sede" description="Crear una nueva sede" back-route="pos.sedes.index" @submit="submit">
            <FormField label="Código" required :error="form.errors.codigo">
                <Input v-model="form.codigo" placeholder="Ej: SEDE-001" />
            </FormField>

            <FormField label="Nombre" required :error="form.errors.nombre">
                <Input v-model="form.nombre" placeholder="Nombre de la sede" />
            </FormField>

            <FormField label="Dirección" :error="form.errors.direccion">
                <Input v-model="form.direccion" placeholder="Dirección (opcional)" />
            </FormField>

            <FormField label="Teléfono" :error="form.errors.telefono">
                <Input v-model="form.telefono" placeholder="Teléfono (opcional)" />
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
