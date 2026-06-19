<script setup lang="ts">
import { computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import { toast } from 'vue-sonner';
import type { Empleado } from '@/types';

const props = defineProps<{
    empleado: Empleado;
}>();

const form = useForm({
    nombres: props.empleado.nombres,
    apellidos: props.empleado.apellidos,
    dni: props.empleado.dni,
    cargo: props.empleado.cargo || '',
    activo: props.empleado.activo,
    crear_usuario: !!props.empleado.user_id,
    name: props.empleado.user?.name || '',
    email: props.empleado.user?.email || '',
    password: '',
});

const crearUsuario = computed(() => form.crear_usuario);

function submit() {
    form.put(route('pos.empleados.update', props.empleado.id), {
        onSuccess: () => {
            toast.success('Empleado actualizado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al actualizar el empleado');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Editar Empleado" :description="`Editando: ${empleado.nombres} ${empleado.apellidos}`" :is-editing="true" back-route="pos.empleados.index" @submit="submit">
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

            <!-- Separator -->
            <hr class="border-gray-800" />

            <!-- Cuenta de usuario -->
            <FormField label="Cuenta de Usuario">
                <label class="flex items-center gap-2">
                    <input v-model="form.crear_usuario" type="checkbox" class="rounded border-gray-700 bg-gray-900 text-blue-600" />
                    <span class="text-sm text-gray-300">¿Crear cuenta de acceso?</span>
                </label>
            </FormField>

            <template v-if="crearUsuario">
                <div class="grid grid-cols-2 gap-4">
                    <FormField label="Nombre de usuario" required :error="form.errors.name">
                        <Input v-model="form.name" placeholder="Ej: Juan Pérez" />
                    </FormField>

                    <FormField label="Correo electrónico" required :error="form.errors.email">
                        <Input v-model="form.email" type="email" placeholder="correo@ejemplo.com" />
                    </FormField>
                </div>

                <FormField label="Contraseña" :error="form.errors.password">
                    <Input v-model="form.password" type="password" placeholder="Dejar vacío para mantener la actual" />
                </FormField>
            </template>
        </FormPage>
    </AppPageShell>
</template>
