<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@volt/Button.vue';
import InputText from '@volt/InputText.vue';
import Label from '@volt/Label.vue';
import { LucideSave } from 'lucide-vue-next';
import { GridLayout, GridItem } from 'grid-layout-plus';
import { reactive } from 'vue';

const props = defineProps<{
    page: {
        id: number;
        user_id: number;
        company_id: number;
        tenant_id: string;
        slug: string;
        title: string;
        description: string | null;
        style: any;
        settings: any;
        is_active: boolean;
        views: number;
        last_viewed_at: string | null;
        published_at: string | null;
    };
}>();

const form = useForm({
    title: props.page.title,
    description: props.page.description ?? '',
    style: props.page.style ?? {},
    settings: props.page.settings ?? {},
    is_active: props.page.is_active,
    published_at: props.page.published_at ?? '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pagine',
        href: route('pages.index'),
    },
    {
        title: 'Modifica',
        href: route('pages.edit', props.page.id),
    },
];

const submitForm = () => {
    form.put(route('pages.update', props.page.id), {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors: Record<string, any>) => {
            console.error('Form submission errors:', errors);
        },
    });
};

const layout = reactive([
    {"x":0,"y":0,"w":2,"h":2,"i":"Content", static: false},
    {"x":2,"y":0,"w":2,"h":4,"i":"1", static: false},
    {"x":2,"y":0,"w":2,"h":5,"i":"2", static: false},
    {"x":2,"y":2,"w":2,"h":3,"i":"3", static: false},
    {"x":0,"y":4,"w":2,"h":3,"i":"4", static: false},
]);
</script>

<template>
    <Head title="Modifica Pagina" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <form class="flex flex-row flex-wrap gap-y-4 items-center justify-start w-full" @submit.prevent="submitForm">
                <div class="w-full">
                    <Label label="Titolo" />
                    <InputText v-model="form.title" required />
                </div>
                <div class="w-full">
                    <Label label="Descrizione" />
                    <InputText v-model="form.description" />
                </div>
                <!-- Puoi aggiungere qui campi per style/settings se necessario -->
                <div class="w-full">
                    <Label label="Pubblicata il" />
                    <InputText v-model="form.published_at" type="date" />
                </div>
                <div class="w-full">
                    <Label label="Attiva" />
                    <input type="checkbox" v-model="form.is_active" />
                </div>
                <div class="w-full">
                    <Button type="submit">
                        <LucideSave class="mr-2" />
                        Aggiorna Pagina
                    </Button>
                </div>
            </form>
            <GridLayout
                v-model:layout="layout"
                :col-num="12"
                :row-height="100"
                :max-rows="12"
                is-draggable
                is-resizable
                vertical-compact
                use-css-transforms
            >
                <GridItem
                v-for="item in layout"
                :key="item.i"
                :x="item.x"
                :y="item.y"
                :w="item.w"
                :h="item.h"
                :i="item.i"
                class="bg-gray-200 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-700 dark:text-gray-300"
                >
                {{ item.i }}
                </GridItem>
            </GridLayout>
        </div>
    </AppLayout>
</template>

<style scoped>
.vgl-layout {
  background-color: #333;
}

.vgl-layout::before {
  position: absolute;
  width: calc(100% - 5px);
  height: calc(100% - 5px);
  margin: 5px;
  content: '';
  background-image: linear-gradient(to right, rgb(41, 16, 16) 1px, transparent 1px),
    linear-gradient(to bottom, rgb(41, 16, 16) 1px, transparent 1px);
  background-repeat: repeat;
  background-size: calc(calc(100% - 5px) / 12) 40px;
}
</style>
