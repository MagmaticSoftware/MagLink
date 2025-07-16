<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LucideSave } from 'lucide-vue-next';
import InputText from '@volt/InputText.vue';
import Button from '@volt/Button.vue';
import Label from '@volt/Label.vue';
import { type PageProps } from '@/types/inertia';

const page = usePage<PageProps>();

const props = defineProps<{
    slug: string;
}>();

const form = useForm({
    user_id: page.props.auth.user.id,
    company_id: page.props.auth.user.company?.id ?? 1,
    tenant_id: page.props.auth.user.tenant_id,
    slug: props.slug,
    title: '',
    description: '',
    style: {},
    settings: {},
    is_active: true,
    published_at: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pagine',
        href: route('pages.index'),
    },
    {
        title: 'Crea',
        href: route('pages.create'),
    },
];

const submitForm = () => {
    form.post(route('pages.store'), {
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

    <Head title="Crea Pagina" />

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
                        Crea Pagina
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
