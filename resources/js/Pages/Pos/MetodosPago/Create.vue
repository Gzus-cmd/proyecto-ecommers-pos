<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import { toast } from 'vue-sonner';

const form = useForm({
    nombre: '',
    activo: true,
});

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

            <FormField label="Estado">
                <label class="flex items-center gap-2">
                    <input v-model="form.activo" type="checkbox" class="rounded border-gray-700 bg-gray-900 text-blue-600" />
                    <span class="text-sm text-gray-300">Activo</span>
                </label>
            </FormField>
        </FormPage>
    </AppPageShell>
</template>
