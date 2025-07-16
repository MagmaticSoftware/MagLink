<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@volt/Button.vue';
import InputText from '@volt/InputText.vue';
import Label from '@volt/Label.vue';
import { LucideSave } from 'lucide-vue-next';

const props = defineProps<{
    qrCode: {
        id: number;
        user_id: number;
        company_id: number;
        tenant_id: string;
        slug: string;
        name: string | null;
        description: string | null;
        type: string;
        format: string;
        payload: any;
        options: any;
        scans: number;
        is_active: boolean;
        last_scanned_at: string | null;
    };
}>();

const form = useForm({
    name: props.qrCode.name ?? '',
    description: props.qrCode.description ?? '',
    type: props.qrCode.type,
    format: props.qrCode.format,
    payload: props.qrCode.payload ?? {},
    options: props.qrCode.options ?? {},
    is_active: props.qrCode.is_active,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'QR Codes',
        href: route('qrcodes.index'),
    },
    {
        title: 'Modifica',
        href: route('qrcodes.edit', props.qrCode.id),
    },
];

const submitForm = () => {
    form.put(route('qrcodes.update', props.qrCode.id), {
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
    <Head title="Modifica QR Code" />

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
                        Aggiorna QR Code
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
