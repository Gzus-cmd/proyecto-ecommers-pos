<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import ToastNotification from '@/Components/pos/ToastNotification.vue';

const sidebarOpen = ref(false);
const userMenuOpen = ref(false);

const page = usePage<{
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
        } | null;
    };
    sede?: {
        nombre?: string;
        codigo?: string;
    };
}>();

const user = page.props.auth?.user;
const sedeNombre = page.props.sede?.nombre;

function isActive(routeName: string): boolean {
    const url = usePage().url;
    const path = url.split('?')[0];

    // Dashboard is the root /pos
    if (routeName === 'pos.dashboard') {
        return path === '/pos' || path === '/pos/';
    }

    // Convert pos.sedes.index → /pos/sedes, pos.productos.edit → /pos/productos, etc.
    const prefix = '/' + routeName
        .replace(/^pos\./, 'pos/')
        .replace(/\./g, '/')
        .replace(/\/index$/, '');

    return path.startsWith(prefix);
}

function handleLogout() {
    userMenuOpen.value = false;
    router.post(route('logout'));
}
</script>

<template>
    <div class="flex h-screen bg-gray-950">
        <!-- Sidebar mobile overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/60 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 flex w-60 flex-col bg-gray-900 border-r border-gray-800 transition-transform lg:static lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <!-- Logo / App Name -->
            <div class="flex h-16 items-center gap-3 border-b border-gray-800 px-6">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 shadow-lg shadow-blue-600/25">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-bold leading-tight text-white">Pharma Victoria POS</span>
                    <span v-if="sedeNombre" class="text-xs font-medium text-blue-400">{{ sedeNombre }}</span>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                <!-- Dashboard (alone at top) -->
                <Link
                    :href="route('pos.dashboard')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive('pos.dashboard')
                            ? 'bg-blue-600/20 text-blue-400'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </Link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-medium uppercase tracking-wider text-gray-500">Gestión</p>
                </div>
                <Link
                    :href="route('pos.users.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive('pos.users.index')
                            ? 'bg-blue-600/20 text-blue-400'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Usuarios
                </Link>
                <Link
                    :href="route('pos.clientes.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive('pos.clientes.index')
                            ? 'bg-blue-600/20 text-blue-400'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Clientes
                </Link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-medium uppercase tracking-wider text-gray-500">Productos</p>
                </div>
                <Link
                    :href="route('pos.productos.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive('pos.productos.index')
                            ? 'bg-blue-600/20 text-blue-400'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Productos
                </Link>
                <Link
                    :href="route('pos.lotes.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive('pos.lotes.index')
                            ? 'bg-blue-600/20 text-blue-400'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Lotes
                </Link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-medium uppercase tracking-wider text-gray-500">Ventas</p>
                </div>
                <Link
                    :href="route('pos.ventas.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive('pos.ventas.index')
                            ? 'bg-blue-600/20 text-blue-400'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                    </svg>
                    Ventas
                </Link>
                <Link
                    :href="route('pos.detalle-ventas.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive('pos.detalle-ventas.index')
                            ? 'bg-blue-600/20 text-blue-400'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Detalle Ventas
                </Link>
                <Link
                    :href="route('pos.stock.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive('pos.stock.index')
                            ? 'bg-blue-600/20 text-blue-400'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    Stock
                </Link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-medium uppercase tracking-wider text-gray-500">Configuración</p>
                </div>
                <Link
                    :href="route('pos.metodos-pago.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive('pos.metodos-pago.index')
                            ? 'bg-blue-600/20 text-blue-400'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Métodos de Pago
                </Link>
            </nav>

            <!-- User footer -->
            <div v-if="user" class="relative border-t border-gray-800">
                <button
                    class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-gray-800/50"
                    @click="userMenuOpen = !userMenuOpen"
                >
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">{{ user.name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                    </div>
                    <svg
                        class="h-4 w-4 text-gray-500 transition-transform"
                        :class="userMenuOpen && 'rotate-180'"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div
                    v-if="userMenuOpen"
                    class="absolute bottom-full left-0 right-0 mb-1 mx-3 rounded-lg border border-gray-800 bg-gray-900 shadow-xl"
                >
                    <Link
                        :href="route('settings.profile')"
                        class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-400 transition-colors hover:bg-gray-800 hover:text-white"
                        @click="userMenuOpen = false"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Perfil
                    </Link>
                    <Link
                        :href="route('settings.sede.edit')"
                        class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-400 transition-colors hover:bg-gray-800 hover:text-white"
                        @click="userMenuOpen = false"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Configuración de Sede
                    </Link>
                    <button
                        class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-400 transition-colors hover:bg-red-900/20"
                        @click="handleLogout"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar Sesión
                    </button>
                </div>
            </div>

            <!-- Fallback footer when no user -->
            <div v-else class="border-t border-gray-800 px-4 py-4">
                <p class="text-xs text-gray-600">POS v1.0</p>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-1 flex-col overflow-hidden">
            <!-- Top bar (mobile) -->
            <header class="flex h-16 items-center justify-between border-b border-gray-800 bg-gray-900 px-4 lg:hidden">
                <button
                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-800 hover:text-white"
                    @click="sidebarOpen = true"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="text-lg font-bold text-white">Pharma Victoria POS</span>
                <div class="w-10" />
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>

            <ToastNotification />
        </div>
    </div>
</template>
