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
    name: '',
    description: '',
    type: 'static',
    format: 'url',
    payload: {},
    options: {},
    is_active: true,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'QR Codes',
        href: route('qrcodes.index'),
    },
    {
        title: 'Crea',
        href: route('qrcodes.create'),
    },
];

const submitForm = () => {
    form.post(route('qrcodes.store'), {
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

    <Head title="Crea QR Code" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <form class="flex flex-row flex-wrap gap-y-4 items-center justify-start w-full" @submit.prevent="submitForm">
                <div class="w-full">
                    <Label label="Nome" />
                    <InputText v-model="form.name" required />
                </div>
                <div class="w-full">
                    <Label label="Descrizione" />
                    <InputText v-model="form.description" />
                </div>
                <div class="w-full">
                    <Label label="Tipo" />
                    <InputText v-model="form.type" required />
                </div>
                <div class="w-full">
                    <Label label="Formato" />
                    <InputText v-model="form.format" required />
                </div>
                <div class="w-full">
                    <Label label="Attivo" />
                    <input type="checkbox" v-model="form.is_active" />
                </div>
                <!-- Add more fields for payload/options if needed -->
                <div class="w-full">
                    <Button type="submit">
                        <LucideSave class="mr-2" />
                        Crea QR Code
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
