<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Bienvenido" />

    <div class="flex min-h-screen items-center justify-center bg-gray-950">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-white">POS - Sistema de Ventas</h1>
            <p class="mt-4 text-gray-400">Módulo de administración</p>
            <div class="mt-8 flex justify-center gap-4">
                <a
                    href="/pos/sedes"
                    class="rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700"
                >
                    Ir al POS
                </a>
            </div>
        </div>
    </div>
</template>
