/**
 * app.ts
 *
 * Punto de entrada principal de la aplicación frontend.
 * Configura Inertia.js con Vue 3, registra el helper de rutas
 * y el componente Toaster de vue-sonner para notificaciones.
 *
 * Las páginas se resuelven automáticamente desde ./Pages/**/*.vue
 * usando el glob de Vite + laravel-vite-plugin.
 */
import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h, type DefineComponent } from 'vue';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Toaster } from 'vue-sonner';
import { route } from '@/lib/route';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');
        return resolvePageComponent(`./Pages/${name}.vue`, pages);
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.component('Toaster', Toaster);
        app.config.globalProperties.route = route;

        if (el) {
            app.mount(el);
        }

        return app;
    },
});
