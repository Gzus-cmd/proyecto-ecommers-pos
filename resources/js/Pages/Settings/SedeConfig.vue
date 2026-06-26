<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import { toast } from 'vue-sonner';
import PosLayout from '@/Layouts/PosLayout.vue';
import Card from '@/Components/pos/ui/Card.vue';
import Input from '@/Components/pos/ui/Input.vue';
import Button from '@/Components/pos/ui/Button.vue';

const props = defineProps<{
    config: {
        nombre: string;
        codigo: string;
        direccion: string;
        telefono: string;
    };
}>();

const form = useForm({
    nombre: props.config.nombre,
    codigo: props.config.codigo,
    direccion: props.config.direccion,
    telefono: props.config.telefono,
});

function submit() {
    form.post(route('settings.sede.update'), {
        onSuccess: () => {
            toast.success('Configuración de sede guardada correctamente');
        },
        onError: () => {
            toast.error('Error al guardar la configuración');
        },
    });
}
</script>

<template>
    <PosLayout>
        <Head title="Configuración de Sede" />
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-white">Configuración de Sede</h1>
                <p class="mt-1 text-sm text-gray-400">Datos de la sede actual del POS</p>
            </div>

            <form @submit.prevent="submit">
                <Card title="Datos de la Sede">
                    <div class="space-y-4">
                        <Input
                            v-model="form.nombre"
                            label="Nombre de la Sede"
                            :error="form.errors.nombre"
                            placeholder="Ej: Sede Principal"
                        />
                        <Input
                            v-model="form.codigo"
                            label="Código"
                            :error="form.errors.codigo"
                            placeholder="Ej: SED-001"
                        />
                        <Input
                            v-model="form.direccion"
                            label="Dirección"
                            :error="form.errors.direccion"
                            placeholder="Dirección de la sede (opcional)"
                        />
                        <Input
                            v-model="form.telefono"
                            label="Teléfono"
                            :error="form.errors.telefono"
                            placeholder="Teléfono (opcional)"
                        />
                    </div>
                    <div class="mt-6 flex justify-end">
                        <Button type="submit" :loading="form.processing">
                            Guardar Configuración
                        </Button>
                    </div>
                </Card>
            </form>
        </div>
    </PosLayout>
</template>
