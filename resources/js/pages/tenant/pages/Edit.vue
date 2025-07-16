<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@volt/Button.vue';
import InputText from '@volt/InputText.vue';
import Label from '@volt/Label.vue';
import { LucideSave } from 'lucide-vue-next';

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
        </div>
    </AppLayout>
</template>
