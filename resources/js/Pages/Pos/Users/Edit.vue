<script setup lang="ts">
/**
 * Users/Edit.vue
 *
 * Página para editar un usuario existente. Precarga los datos del usuario
 * recibido por props. Permite modificar nombre, email, contraseña
 * (opcional), rol y estado. Envía PUT a 'pos.users.update'.
 *
 * Props:
 * - user: Objeto User con los datos actuales
 * - roles: Lista de roles disponibles para asignar
 */
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import Toggle from '@/Components/pos/ui/Toggle.vue';
import { toast } from 'vue-sonner';
import type { User } from '@/types';

const props = defineProps<{
    user: User;
    roles: { id: number; name: string }[];
}>();

const userRole = props.user.roles?.[0]?.name ?? '';

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    activo: props.user.activo,
    role: userRole,
});

/** Envía el formulario para actualizar el usuario */
function submit() {
    form.put(route('pos.users.update', props.user.id), {
        onSuccess: () => {
            toast.success('Usuario actualizado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al actualizar el usuario');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Editar Usuario" :description="`Editando: ${user.name}`" :is-editing="true" back-route="pos.users.index" @submit="submit">
            <FormField label="Nombre" required :error="form.errors.name">
                <Input v-model="form.name" placeholder="Nombre completo" />
            </FormField>

            <FormField label="Correo electrónico" required :error="form.errors.email">
                <Input v-model="form.email" type="email" placeholder="correo@ejemplo.com" />
            </FormField>

            <FormField label="Contraseña" :error="form.errors.password">
                <Input v-model="form.password" type="password" placeholder="Dejar vacío para no cambiar" />
            </FormField>

            <FormField label="Rol" :error="form.errors.role">
                <select v-model="form.role" class="block w-full rounded-lg border border-gray-600 bg-gray-800 px-3 py-2 text-sm text-white shadow-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Sin rol</option>
                    <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                </select>
            </FormField>

            <FormField label="Estado">
                <Toggle v-model="form.activo" label="Activo" />
            </FormField>
        </FormPage>
    </AppPageShell>
</template>
