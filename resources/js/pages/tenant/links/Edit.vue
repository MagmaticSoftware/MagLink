<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@volt/Button.vue';
import InputText from '@volt/InputText.vue';
import Label from '@volt/Label.vue';
import { LucideSave } from 'lucide-vue-next';

const props = defineProps<{
    link: { 
        id: number;
        user_id: number; 
        company_id: number; 
        tenant_id: number; 
        slug: string; 
        title: string;
        description: string; 
        url: string; 
        is_active: boolean;
        type: string;
    };
}>();

const form = useForm({
    url: props.link.url,
    title: props.link.title,
    description: props.link.description,
    is_active: props.link.is_active,
    type: props.link.type,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Links',
        href: route('links.index'),
    },
    {
        title: 'Edit',
        href: route('links.edit', props.link.id),
    },
];

const submitForm = () => {
    form.put(route('links.update', props.link.id), {
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
                        Update Link
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
