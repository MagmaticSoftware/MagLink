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
    url: 'https://magmatic.it',
    title: '',
    description: '',
    is_active: true,
    type: 'link',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Links',
        href: route('links.index'),
    },
    {
        title: 'Create',
        href: route('links.create'),
    },
];

const submitForm = () => {
    form.post(route('links.store'), {
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

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <form class="flex flex-row flex-wrap gap-y-4 items-center justify-start w-full" @submit.prevent="submitForm">
                <div class="w-full">
                    <Label label="URL" />
                    <InputText v-model="form.url" required />
                </div>
                <div class="w-full">
                    <Label label="Title" />
                    <InputText v-model="form.title" required />
                </div>
                <div class="w-full">
                    <Label label="Description" />
                    <InputText v-model="form.description" />
                </div>
                <div class="w-full">
                    <Button type="submit">
                        <LucideSave class="mr-2" />
                        Create Link
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
