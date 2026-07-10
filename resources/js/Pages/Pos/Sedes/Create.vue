<script setup lang="ts">
/**
 * Sedes/Create.vue
 *
 * Página para crear una nueva sede. Presenta un formulario con campos:
 * código, nombre, dirección, teléfono y estado (activo/inactivo).
 * Al enviar, realiza POST a 'pos.sedes.store' y redirige.
 *
 * Emite: submit → manejado por FormPage que envía el formulario
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
    codigo: '',
    nombre: '',
    direccion: '',
    telefono: '',
    activo: true,
});

/** Envía el formulario para crear la sede */
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
                <Toggle v-model="form.activo" label="Activo" />
            </FormField>
        </FormPage>
    </AppPageShell>
</template>
