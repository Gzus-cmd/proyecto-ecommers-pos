<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import PosLayout from '@/Layouts/PosLayout.vue';
import Card from '@/Components/pos/ui/Card.vue';
import Input from '@/Components/pos/ui/Input.vue';
import Button from '@/Components/pos/ui/Button.vue';

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
    };
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function updateProfile() {
    form.put(route('settings.profile.update'), {
        onSuccess: () => toast.success('Perfil actualizado'),
        onError: () => toast.error('Error al actualizar el perfil'),
    });
}

function updatePassword() {
    passwordForm.put(route('settings.profile.password'), {
        onSuccess: () => {
            toast.success('Contraseña actualizada');
            passwordForm.reset();
        },
        onError: () => toast.error('Error al actualizar la contraseña'),
    });
}
</script>

<template>
    <PosLayout>
        <Head title="Configuración" />
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-white">Configuración</h1>
                <p class="mt-1 text-sm text-gray-400">Administra tu perfil y contraseña</p>
            </div>

            <div class="space-y-8">
                <!-- Profile form -->
                <form @submit.prevent="updateProfile">
                    <Card title="Información del Perfil">
                        <div class="space-y-4">
                            <Input
                                v-model="form.name"
                                label="Nombre"
                                :error="form.errors.name"
                                placeholder="Tu nombre"
                            />
                            <Input
                                v-model="form.email"
                                label="Email"
                                type="email"
                                :error="form.errors.email"
                                placeholder="tu@email.com"
                            />
                        </div>
                        <div class="mt-6 flex justify-end">
                            <Button type="submit" :loading="form.processing">
                                Guardar Cambios
                            </Button>
                        </div>
                    </Card>
                </form>

                <!-- Password form -->
                <form @submit.prevent="updatePassword">
                    <Card title="Cambiar Contraseña">
                        <div class="space-y-4">
                            <Input
                                v-model="passwordForm.current_password"
                                label="Contraseña Actual"
                                type="password"
                                :error="passwordForm.errors.current_password"
                                placeholder="••••••••"
                            />
                            <Input
                                v-model="passwordForm.password"
                                label="Nueva Contraseña"
                                type="password"
                                :error="passwordForm.errors.password"
                                placeholder="Mín. 8 caracteres"
                            />
                            <Input
                                v-model="passwordForm.password_confirmation"
                                label="Confirmar Contraseña"
                                type="password"
                                :error="passwordForm.errors.password_confirmation"
                                placeholder="Repite la nueva contraseña"
                            />
                        </div>
                        <div class="mt-6 flex justify-end">
                            <Button type="submit" :loading="passwordForm.processing">
                                Actualizar Contraseña
                            </Button>
                        </div>
                    </Card>
                </form>
            </div>
        </div>
    </PosLayout>
</template>
