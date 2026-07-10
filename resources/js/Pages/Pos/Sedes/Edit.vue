<script setup lang="ts">
/**
 * Sedes/Edit.vue
 *
 * Página para editar una sede existente. Precarga los datos de la sede
 * recibida por props y permite modificar código, nombre, dirección,
 * teléfono y estado. Envía PUT a 'pos.sedes.update'.
 *
 * Props:
 * - sede: Objeto Sede con los datos actuales
 */
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import Toggle from '@/Components/pos/ui/Toggle.vue';
import { toast } from 'vue-sonner';
import type { Sede } from '@/types';

const props = defineProps<{
    sede: Sede;
}>();

const form = useForm({
    codigo: props.sede.codigo,
    nombre: props.sede.nombre,
    direccion: props.sede.direccion || '',
    telefono: props.sede.telefono || '',
    activo: props.sede.activo,
});

/** Envía el formulario para actualizar la sede */
function submit() {
    form.put(route('pos.sedes.update', props.sede.id), {
        onSuccess: () => {
            toast.success('Sede actualizada correctamente');
        },
        onError: (errors) => {
            toast.error('Error al actualizar la sede');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Editar Sede" :description="`Editando: ${sede.nombre}`" :is-editing="true" back-route="pos.sedes.index" @submit="submit">
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
