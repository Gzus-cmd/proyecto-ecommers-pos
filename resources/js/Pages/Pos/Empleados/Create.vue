<script setup lang="ts">
import { computed } from 'vue';
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
    crear_usuario: false,
    name: '',
    email: '',
    password: '',
});

const crearUsuario = computed(() => form.crear_usuario);

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

                <FormField label="Contraseña" required :error="form.errors.password">
                    <Input v-model="form.password" type="password" placeholder="••••••••" />
                </FormField>
            </template>
        </FormPage>
    </AppPageShell>
</template>
