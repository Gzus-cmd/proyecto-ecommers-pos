<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import Button from '@/Components/pos/ui/Button.vue';
import { route } from '@/lib/route';

const sedeNombre = usePage<{ sede?: { nombre?: string } }>().props.sede?.nombre;

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login.store'), {
        onError: () => {
            form.reset('password');
        },
    });
}
</script>

<template>
    <Head title="Iniciar Sesión" />

    <div class="flex min-h-screen items-center justify-center bg-gray-950 px-4">
        <div class="w-full max-w-sm">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-blue-600 shadow-lg shadow-blue-600/25">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Pharma Victoria POS</h1>
                <p v-if="sedeNombre" class="mt-1 text-sm font-medium text-blue-400">{{ sedeNombre }}</p>
                <p v-else class="mt-1 text-sm text-gray-400">Ingrese sus credenciales para acceder</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="rounded-xl border border-gray-800 bg-gray-900 p-6 shadow-xl">
                <div class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-gray-300">
                            Correo electrónico
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            placeholder="admin@pharma.com"
                            class="block w-full rounded-lg border bg-gray-950 px-3 py-2.5 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="form.errors.email ? 'border-red-500' : 'border-gray-700'"
                        />
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-400">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-gray-300">
                            Contraseña
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full rounded-lg border bg-gray-950 px-3 py-2.5 text-sm text-white placeholder-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="form.errors.password ? 'border-red-500' : 'border-gray-700'"
                        />
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-400">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember -->
                    <label class="flex items-center gap-2">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="rounded border-gray-700 bg-gray-950 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="text-sm text-gray-400">Recordar sesión</span>
                    </label>
                </div>

                <Button type="submit" :loading="form.processing" class="mt-6 w-full">
                    Iniciar Sesión
                </Button>
            </form>

            <p class="mt-6 text-center text-xs text-gray-600">
                &copy; {{ new Date().getFullYear() }} Pharma Victoria &mdash; Todos los derechos reservados
            </p>
        </div>
    </div>
</template>
