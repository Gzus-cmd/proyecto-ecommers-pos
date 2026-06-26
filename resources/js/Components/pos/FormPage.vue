<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import AppPageHeader from '@/Components/pos/AppPageHeader.vue';
import Button from '@/Components/pos/ui/Button.vue';
import Card from '@/Components/pos/ui/Card.vue';

const props = defineProps<{
    title: string;
    description?: string;
    backRoute?: string;
    isEditing?: boolean;
}>();

const emit = defineEmits<{
    submit: [];
}>();

function cancel() {
    router.visit(route(props.backRoute || 'pos.dashboard'));
}
</script>

<template>
    <div class="mx-auto max-w-3xl">
        <AppPageHeader :title="title" :description="description" />

        <form @submit.prevent="emit('submit')">
            <Card>
                <div class="space-y-6">
                    <slot />
                </div>
                <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-800 pt-6">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-transparent px-4 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-800"
                        @click="cancel"
                    >
                        Cancelar
                    </button>
                    <Button type="submit">
                        {{ isEditing ? 'Actualizar' : 'Guardar' }}
                    </Button>
                </div>
            </Card>
        </form>
    </div>
</template>
