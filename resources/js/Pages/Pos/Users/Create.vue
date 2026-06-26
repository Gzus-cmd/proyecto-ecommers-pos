<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageShell from '@/Components/pos/AppPageShell.vue';
import FormPage from '@/Components/pos/FormPage.vue';
import FormField from '@/Components/pos/FormField.vue';
import Input from '@/Components/pos/ui/Input.vue';
import Toggle from '@/Components/pos/ui/Toggle.vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    roles: { id: number; name: string }[];
}>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    activo: true,
    role: '',
});

function submit() {
    form.post(route('pos.users.store'), {
        onSuccess: () => {
            toast.success('Usuario creado correctamente');
        },
        onError: (errors) => {
            toast.error('Error al crear el usuario');
        },
    });
}
</script>

<template>
    <AppPageShell>
        <FormPage title="Nuevo Usuario" description="Crear un nuevo usuario del sistema" back-route="pos.users.index" @submit="submit">
            <FormField label="Nombre" required :error="form.errors.name">
                <Input v-model="form.name" placeholder="Nombre completo" />
            </FormField>

            <FormField label="Correo electrónico" required :error="form.errors.email">
                <Input v-model="form.email" type="email" placeholder="correo@ejemplo.com" />
            </FormField>

            <FormField label="Contraseña" required :error="form.errors.password">
                <Input v-model="form.password" type="password" placeholder="Mínimo 8 caracteres" />
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
